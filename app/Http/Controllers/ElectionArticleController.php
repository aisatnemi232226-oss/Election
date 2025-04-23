<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectionArticle;
use App\Models\Election;
use Illuminate\Support\Facades\Storage;

class ElectionArticleController extends Controller
{
    public function index()
    {
        $articles = ElectionArticle::with('election')->get();
        
        // Create a test article if none exists
        if ($articles->isEmpty()) {
            $firstElection = Election::first();
            if ($firstElection) {
                $testArticle = new ElectionArticle();
                $testArticle->title = "Test Article";
                $testArticle->author = "Test Author";
                $testArticle->content = "<p>This is a test article</p>";
                $testArticle->election_id = $firstElection->id;
                $testArticle->visible = true;
                $testArticle->tags = ["test", "debug"];
                $testArticle->save();
                
                // Reload articles
                $articles = ElectionArticle::with('election')->get();
            }
        }
        
        $elections = Election::all();
        return view('admin.election-articles', compact('articles', 'elections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'content' => 'required|string',
            'election_id' => 'required|exists:elections,id', // Updated table name if needed
            'visible' => 'boolean',
            'tags' => 'nullable',
        ]);

        // Handle tags if they come as JSON string
        if (is_string($request->tags)) {
            $validated['tags'] = json_decode($request->tags, true);
        }

        // Set visible to false if not provided
        $validated['visible'] = $request->has('visible') ? true : false;

        ElectionArticle::create($validated);

        return redirect()->route('admin.election-articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function edit($id)
    {
        $article = ElectionArticle::findOrFail($id);
        return response()->json($article);
    }

    public function update(Request $request, $id)
    {
        $article = ElectionArticle::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'content' => 'required|string',
            'election_id' => 'required|exists:elections,id', // Updated table name if needed
            'visible' => 'boolean',
            'tags' => 'nullable',
        ]);

        // Handle tags if they come as JSON string
        if (is_string($request->tags)) {
            $validated['tags'] = json_decode($request->tags, true);
        }

        // Set visible to false if not provided
        $validated['visible'] = $request->has('visible') ? true : false;

        $article->update($validated);

        return redirect()->route('admin.election-articles.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy($id)
    {
        $article = ElectionArticle::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.election-articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    public function uploadQuillImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('article-images', 'public');
            return response()->json(['url' => Storage::url($path)]);
        }
        
        return response()->json(['error' => 'No image found'], 400);
    }
}