<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Election;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ElectionArticleController extends Controller
{
    public function index()
    {
        $articles = DB::table('election_articles')
            ->join('articles', 'articles.id', '=', 'election_articles.articles_id')
            ->join('election', 'election.id', '=', 'election_articles.election_id')
            ->select(
                'articles.*',
                'election.name as election_name',
                'election_articles.show_status'
            )
            ->get();

        $elections = Election::all();

        return view('admin.election-articles', compact('articles', 'elections'));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        $election = Election::select('election.*')
            ->join('election_articles', 'election.id', '=', 'election_articles.election_id')
            ->where('election_articles.articles_id', $article->id)
            ->first();

        $article->election = $election;

        return response()->json($article);
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
            'show_status' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ]);

        // Set show_status as boolean based on checkbox presence
        $validated['show_status'] = $request->has('show_status');

        // Handle featured image
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

            $validated['featured_image'] = Storage::url($path);
        }

        // Update article content
        $article->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'content' => $validated['content'],
            'featured_image' => $validated['featured_image'] ?? $article->featured_image,
        ]);

        // Update election_articles pivot
        DB::table('election_articles')
            ->where('articles_id', $article->id)
            ->update([
                'election_id' => $validated['election_id'],
                'show_status' => $validated['show_status'] ? '1' : '0',
            ]);

        return redirect()->route('admin.election-articles.index')
            ->with('success', 'Article updated successfully.');
    }
}
