<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Article;
    use App\Models\Election;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Log;

    class ArticleController extends Controller
    {
        public function index()
        {
            $articles = Article::select('articles.*')
                ->join('election_articles', 'articles.id', '=', 'election_articles.articles_id')
                ->join('election', 'election.id', '=', 'election_articles.election_id')
                ->with('election') // requires Article model to have election() relationship
                ->get();

            $elections = Election::all();

            return view('admin.election-articles', compact('articles', 'elections'));
        }

        public function show($id)
        {
            $article = Article::findOrFail($id);


            // Get related election
            $election = Election::select('election.*')
            ->join('election_articles', 'election.id', '=', 'election_articles.election_id')
            ->where('election_articles.articles_id', $article->id)
            ->first();

            $article->election = $election;

            return response()->json($article);
        }

        public function publicJson($id)
        {
            $article = Article::findOrFail($id);

            return response()->json($article);
        }


        public function store(Request $request)
        {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'content' => 'required|string',
                'election_id' => 'required|exists:election,id',
                'visible' => 'boolean',
                'tags' => 'nullable',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
            ]);

            if (is_string($request->tags)) {
                $validated['tags'] = json_decode($request->tags, true);
            }

            $validated['visible'] = $request->has('visible');
            
            // Handle featured image upload
            $featuredImagePath = null;
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = time() . '_featured_' . str_replace(' ', '_', $image->getClientOriginalName());

                Storage::disk('public')->makeDirectory('article-images');
                $featuredImagePath = Storage::disk('public')->putFileAs('article-images', $image, $filename);
            }

            $article = Article::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'content' => $validated['content'],
                'visible' => $validated['visible'],
                'tags' => $validated['tags'],
                'featured_image' => $featuredImagePath ? Storage::url($featuredImagePath) : null,
            ]);

            DB::table('election_articles')->insert([
                'articles_id' => $article->id,
                'election_id' => $validated['election_id'],
                'created_at' => now(),
            ]);

            return redirect()->route('admin.election-articles.index')
                ->with('success', 'Article created successfully.');
        }

        public function edit($id)
        {
            $article = Article::findOrFail($id);

            $election = Election::select('election.*')
                ->join('election_articles', 'election.id', '=', 'election_articles.election_id')
                ->where('election_articles.articles_id', $article->id)
                ->first();

            if ($election) {
                $article->election_id = $election->id;
            }

            return response()->json($article);
        }

        public function update(Request $request, $id)
        {
            $article = Article::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'content' => 'required|string',
                'election_id' => 'required|exists:election,id',
                'visible' => 'boolean',
                'tags' => 'nullable',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
            ]);

            if (is_string($request->tags)) {
                $validated['tags'] = json_decode($request->tags, true);
            }

            $validated['visible'] = $request->has('visible');
            
            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                // Delete old image if it exists
                if ($article->featured_image) {
                    $oldPath = str_replace('/storage/', 'public/', $article->featured_image);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }
                
                $image = $request->file('featured_image');
                $filename = time() . '_featured_' . str_replace(' ', '_', $image->getClientOriginalName());

                Storage::disk('public')->makeDirectory('article-images');
                $path = Storage::disk('public')->putFileAs('article-images', $image, $filename);
                
                $validated['featured_image'] = Storage::url($path);
            }

            $article->update([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'content' => $validated['content'],
                'visible' => $validated['visible'],
                'tags' => $validated['tags'],
                'featured_image' => $request->hasFile('featured_image') ? $validated['featured_image'] : $article->featured_image,
            ]);

            DB::table('election_articles')
                ->where('articles_id', $article->id)
                ->update([
                    'election_id' => $validated['election_id'],
                ]);

            return redirect()->route('admin.election-articles.index')
                ->with('success', 'Article updated successfully.');
        }


        public function destroy($id)
        {
            $article = Article::findOrFail($id);

            // Delete featured image if exists
            if ($article->featured_image) {
                $path = str_replace('/storage/', 'public/', $article->featured_image);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            DB::table('election_articles')
                ->where('articles_id', $article->id)
                ->delete();

            $article->delete();

            return redirect()->route('admin.election-articles.index')
                ->with('success', 'Article deleted successfully.');
        }

 public function uploadQuillImage(Request $request)
{
    Log::info('Quill image upload attempt started');

    if (!$request->hasFile('image')) {
        Log::error('No image file found in request');
        return response()->json(['error' => 'No image uploaded'], 400);
    }

    try {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
        ]);

        if ($validator->fails()) {
            Log::error('Image validation failed: ' . $validator->errors()->first('image'));
            return response()->json([
                'error' => $validator->errors()->first('image'),
                'success' => false
            ], 422);
        }

        // ✅ Assign image
        $image = $request->file('image');
        $filename = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());

        Log::info('FULL FILE PATH: ' . storage_path('app/public/upload/quill-images/' . $filename));

        // Ensure directory exists
        Storage::makeDirectory('public/upload/quill-images');

        // Store file
        $path = Storage::disk('public')->putFileAs('upload/quill-images', $image, $filename);

        if (!$path) {
            Log::error('Failed to store Quill image');
            return response()->json(['error' => 'Failed to store image'], 500);
        }

        // Generate public URL (via storage symlink: public/storage)
        $url = Storage::url($path); 

        Log::info('Quill image successfully stored at: ' . $path);
        Log::info('Accessible URL: ' . $url);

        return response()->json([
            'url' => $url,
            'success' => true
        ]);

    } catch (\Exception $e) {
        Log::error('Quill image upload error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Exception: ' . $e->getMessage(),
            'success' => false
        ], 500);
    }
}
        
        public function uploadFeaturedImage(Request $request)
        {
            if (!$request->hasFile('featured_image')) {
                return response()->json(['error' => 'No image found'], 400);
            }

            try {
                $validator = Validator::make($request->all(), [
                    'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()->first('featured_image'),
                        'success' => false
                    ], 422);
                }

                $image = $request->file('featured_image');
                $filename = time() . '_featured_' . str_replace(' ', '_', $image->getClientOriginalName());

                Storage::disk('public')->makeDirectory('article-images');

                $path = Storage::disk('public')->putFileAs('article-images', $image, $filename);

                if ($path) {
                    return response()->json([
                        'url' => Storage::url($path),
                        'success' => true
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Failed to store image',
                        'success' => false
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to upload: ' . $e->getMessage(),
                    'success' => false
                ], 500);
            }
        }
    }