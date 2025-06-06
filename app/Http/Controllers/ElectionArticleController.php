<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Election;
use App\Models\Candidate; 
use App\Models\ElectionArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ElectionArticleController extends Controller
{
    public function index()
    {
        try {
            Log::info('ElectionArticleController@index - Starting');
            
            // Fixed query - use proper eager loading instead of join
            $articles = ElectionArticle::with(['articles', 'election'])
                ->whereHas('articles') // Only get election articles that have valid articles
                ->get()
                ->sortByDesc(function ($electionArticle) {
                    return $electionArticle->articles ? $electionArticle->articles->created_at : null;
                });
            
            Log::info('Retrieved election articles: ' . $articles->count());
            
            // Verify the data structure
            foreach ($articles as $article) {
                if (!$article->articles) {
                    Log::warning("Article ID {$article->id} has no associated article data");
                }
                Log::info("Article ID: {$article->id}, Show Status: {$article->show_status}");
            }

            // Get only active elections (not soft deleted) for the dropdown
            $elections = Election::orderBy('name')->get();
            Log::info('Fetched elections for index: ' . $elections->count() . ' records');

            // Get unique candidates more efficiently
            $candidates = Candidate::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->unique('name')
                ->values();
            Log::info('Fetched candidates for index: ' . $candidates->count() . ' records');

            return view('admin.election-articles', compact('articles', 'elections', 'candidates'));
            
        } catch (\Exception $e) {
            Log::error('Error in election-articles index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return view with empty collections instead of redirecting back
            return view('admin.election-articles', [
                'articles' => collect([]),
                'elections' => collect([]),
                'candidates' => collect([]),
                'error' => 'Error loading articles: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('New article creation request received', $request->all());
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'content' => 'required|string',
                'election_name' => 'required|string|max:255',
                'show_status' => 'nullable',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
                'tags' => 'nullable',
            ]);

            Log::info('Validation passed for new article');

            // Find or create election with all required fields
            $currentDate = Carbon::now();
            $election = Election::firstOrCreate(
                ['name' => $validated['election_name']],
                [
                    'description' => 'Election created from article',
                    'start_date' => $currentDate->format('Y-m-d'),
                    'end_date' => $currentDate->addDays(30)->format('Y-m-d'),
                    'reg_start' => $currentDate->format('Y-m-d'),
                    'reg_end' => $currentDate->addDays(7)->format('Y-m-d'),
                    'campaign_start' => $currentDate->addDays(8)->format('Y-m-d'),
                    'campaign_end' => $currentDate->addDays(25)->format('Y-m-d'),
                    'result_date' => $currentDate->addDays(31)->format('Y-m-d'),
                    'admin_id' => auth()->id() ?? 1,
                ]
            );

            // Handle featured image
            $featuredImagePath = null;
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = time() . '_featured_' . str_replace(' ', '_', $image->getClientOriginalName());
                
                Storage::makeDirectory('public/featured-images');
                $path = $image->storeAs('public/featured-images', $filename);
                $featuredImagePath = Storage::url($path);
                Log::info('Featured image saved at: ' . $featuredImagePath);
            }

            // Parse tags
            $tags = [];
            if (!empty($validated['tags'])) {
                try {
                    $tags = json_decode($validated['tags'], true);
                    if (!is_array($tags)) {
                        $tags = [];
                    }
                    Log::info('Parsed tags for new article:', $tags);
                } catch (\Exception $e) {
                    Log::warning('Error parsing tags in store: ' . $e->getMessage());
                    $tags = [];
                }
            }

            // Create article
            $article = Article::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'content' => $validated['content'],
                'featured_image' => $featuredImagePath,
                'tags' => json_encode($tags)
            ]);
            
            Log::info('Created new article with ID: ' . $article->id);

            // Create election article relationship
            $electionArticle = ElectionArticle::create([
                'articles_id' => $article->id,
                'election_id' => $election->id,
                'show_status' => $request->has('show_status') ? 1 : 0
            ]);
            
            Log::info('Created new election article relationship with ID: ' . $electionArticle->id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Article created successfully',
                    'redirect' => route('admin.election-articles.index')
                ]);
            }

            return redirect()->route('admin.election-articles.index')
                ->with('success', 'Article created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating election article: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error creating article: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error creating article: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            Log::info("Fetching election article with ID: $id");
            
            $electionArticle = ElectionArticle::with(['articles', 'election'])->find($id);
            
            if (!$electionArticle) {
                Log::error("ElectionArticle not found with ID: $id");
                return response()->json([
                    'error' => 'Article not found',
                    'message' => "No election article found with ID: $id"
                ], 404);
            }

            if (!$electionArticle->articles) {
                Log::error("Related Article not found for ElectionArticle ID: $id");
                return response()->json([
                    'error' => 'Related article not found',
                    'message' => "No related article found for election article ID: $id"
                ], 404);
            }

            $article = $electionArticle->articles;
            
            $tags = $article->tags ?? [];
            if (is_string($tags) && !empty($tags)) {
                $decoded = json_decode($tags, true);
                $tags = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($tags)) {
                $tags = [];
            }

            $candidateNames = [];
            if (!empty($tags)) {
                try {
                    $candidates = Candidate::whereIn('id', $tags)->pluck('name', 'id');
                    
                    foreach ($tags as $candidateId) {
                        if (isset($candidates[$candidateId])) {
                            $candidateNames[] = $candidates[$candidateId];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error fetching candidate names: ' . $e->getMessage());
                }
            }

            $responseData = [
                'id' => $electionArticle->id,
                'article_id' => $article->id,
                'title' => $article->title ?? '',
                'author' => $article->author ?? '',
                'content' => $article->content ?? '',
                'tags' => implode(', ', $candidateNames),
                'tags_array' => $tags,
                'created_at' => $article->created_at ? $article->created_at->format('Y-m-d H:i:s') : null,
                'featured_image' => $article->featured_image ?? null,
                'election_id' => $electionArticle->election_id ?? null,
                'show_status' => $electionArticle->show_status ?? 0,
            ];

            if ($electionArticle->election) {
                $responseData['election'] = [
                    'id' => $electionArticle->election->id,
                    'name' => $electionArticle->election->name ?? ''
                ];
            } else {
                $responseData['election'] = null;
            }

            Log::info("Successfully returning election article data for ID: $id");
            
            return response()->json($responseData);
            
        } catch (\Exception $e) {
            Log::error('Error fetching election article: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred',
                'message' => 'An error occurred while fetching the article.'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            Log::info("Edit method called for election article ID: $id");

            $electionArticle = ElectionArticle::with([
                'articles' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'election' => function ($query) {
                    $query->withTrashed();  // Show even soft-deleted elections
                }
            ])->find($id);

            if (!$electionArticle || !$electionArticle->articles) {
                Log::error("No election article or related article found with ID: $id");
                return response()->json([
                    'error' => 'Article not found',
                    'message' => "No article found with ID: $id"
                ], 404);
            }

            $article = $electionArticle->articles;

            // Handle tags - ensure they're properly processed
            $tags = $article->tags ?? '[]';
            if (is_string($tags)) {
                try {
                    $decoded = json_decode($tags, true);
                    $tags = (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
                } catch (\Exception $e) {
                    Log::warning('Error parsing tags JSON: ' . $e->getMessage());
                    $tags = [];
                }
            } elseif (!is_array($tags)) {
                $tags = [];
            }

            $candidateNames = [];
            if (!empty($tags)) {
                try {
                    $candidates = Candidate::whereIn('id', $tags)
                        ->pluck('name', 'id');

                    foreach ($tags as $candidateId) {
                        if (isset($candidates[$candidateId])) {
                            $candidateNames[] = [
                                'id' => $candidateId,
                                'name' => $candidates[$candidateId]
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error fetching candidate names in edit: ' . $e->getMessage());
                }
            }

            $responseData = [
                'id' => $electionArticle->id,
                'article_id' => $article->id,
                'title' => $article->title ?? '',
                'author' => $article->author ?? '',
                'content' => $article->content ?? '',
                'tags' => $tags,
                'candidate_tags' => $candidateNames,
                'created_at' => $article->created_at ? $article->created_at->format('Y-m-d H:i:s') : null,
                'featured_image' => $article->featured_image ?? null,
                'election_name' => $electionArticle->election ? $electionArticle->election->name : '',
                'election_id' => $electionArticle->election ? $electionArticle->election->id : null,
                'show_status' => (int) $electionArticle->show_status,
            ];

            Log::info("Successfully returning edit data for election article ID: $id");

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Article not found or error occurred',
                'message' => 'An error occurred while fetching the article for editing.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info("Update method called for ID: $id");
            Log::info('Request data in update: ', $request->all());
            
            $electionArticle = ElectionArticle::with('articles')->find($id);
            
            if (!$electionArticle || !$electionArticle->articles) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found'
                ], 404);
            }

            $article = $electionArticle->articles;

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'content' => 'required|string',
                'election_name' => 'required|string|max:255',
                'show_status' => 'nullable|in:0,1',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
                'tags' => 'nullable'
            ]);

            // Find or create election with all required fields
            $currentDate = Carbon::now();
            $election = Election::firstOrCreate(
                ['name' => $validated['election_name']],
                [
                    'description' => 'Election created from article update',
                    'start_date' => $currentDate->format('Y-m-d'),
                    'end_date' => $currentDate->addDays(30)->format('Y-m-d'),
                    'reg_start' => $currentDate->format('Y-m-d'),
                    'reg_end' => $currentDate->addDays(7)->format('Y-m-d'),
                    'campaign_start' => $currentDate->addDays(8)->format('Y-m-d'),
                    'campaign_end' => $currentDate->addDays(25)->format('Y-m-d'),
                    'result_date' => $currentDate->addDays(31)->format('Y-m-d'),
                    'admin_id' => auth()->id() ?? 1,
                ]
            );

            $featuredImagePath = $article->featured_image;
            if ($request->hasFile('featured_image')) {
                if ($article->featured_image) {
                    $oldPath = str_replace('/storage/', 'public/', $article->featured_image);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                $image = $request->file('featured_image');
                $filename = time() . '_featured_' . str_replace(' ', '_', $image->getClientOriginalName());

                Storage::makeDirectory('public/featured-images');
                $path = $image->storeAs('public/featured-images', $filename);
                $featuredImagePath = Storage::url($path);
            }

            $tags = [];
            if ($validated['tags']) {
                try {
                    $tags = json_decode($validated['tags'], true);
                    if (!is_array($tags)) {
                        $tags = [];
                    }
                } catch (\Exception $e) {
                    Log::warning('Error parsing tags in update: ' . $e->getMessage());
                    $tags = [];
                }
            }

            $article->update([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'content' => $validated['content'],
                'featured_image' => $featuredImagePath,
                'tags' => json_encode($tags)
            ]);

            // Get show_status value correctly from request
            $showStatus = $request->input('show_status', 0);
            
            Log::info("Processing show_status - Input value: " . $showStatus);

            $electionArticle->update([
                'election_id' => $election->id,
                'show_status' => (int) $showStatus,
            ]);

            Log::info("Successfully updated show_status to: " . $showStatus);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Article updated successfully'
                ]);
            }

            return redirect()->route('admin.election-articles.index')
                ->with('success', 'Article updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error updating article: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error updating article: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error updating article: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Log::info("Delete method called for ID: $id");
            
            $electionArticle = ElectionArticle::with('articles')->find($id);
            
            if (!$electionArticle) {
                DB::rollBack();
                Log::error("ElectionArticle not found with ID: $id");
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found'
                ], 404);
            }

            // Delete article image if exists
            if ($electionArticle->articles && $electionArticle->articles->featured_image) {
                $imagePath = str_replace('/storage/', 'public/', $electionArticle->articles->featured_image);
                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                    Log::info("Deleted featured image at path: $imagePath");
                }
            }
            
            // Store the article ID for logging
            $articleId = $electionArticle->articles ? $electionArticle->articles->id : null;
            
            // Delete the article
            if ($electionArticle->articles) {
                $electionArticle->articles->delete();
                Log::info("Deleted article with ID: $articleId");
            }
            
            // Delete the election article relationship
            $electionArticle->delete();
            Log::info("Deleted election article with ID: $id");
            
            DB::commit();
            
            Log::info("Successfully completed delete operation for ID: $id");
            
            return response()->json([
                'success' => true,
                'message' => 'Article deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting article: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting article: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                
                Storage::makeDirectory('public/quill-images');
                $path = $image->storeAs('public/quill-images', $filename);
                $url = Storage::url($path);

                Log::info('Image uploaded successfully to: ' . $url);

                return response()->json([
                    'success' => true,
                    'url' => $url,
                    'message' => 'Image uploaded successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Error uploading image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}