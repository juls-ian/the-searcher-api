<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // eager load Article relationships to User (n+1 problem fix)
        $articles = Article::with(['writer', 'coverArtist', 'thumbnailArtist'])->get();
        return ArticleResource::collection($articles);

        // return ArticleResource::collection(Article::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();

        // Handler 1: cover photo upload 
        if ($request->hasFile('cover_photo')) {
            $coverPath = $request->file('cover_photo')->store('articles/covers', 'public');
            $validatedData['cover_photo'] = $coverPath;
        }

        // Handler 2: thumbnail_same_as_cover logic 
        if ($request->has('thumbnail_same_as_cover') && $request->thumbnail_same_as_cover) {

            // use same file as cover_photo for thumbnail
            $validatedData['thumbnail'] = $validatedData['cover_photo'] ?? null;

            // copy cover_photo metadata to thumbnail if not provided 
            if (!$request->has('thumbnail_caption') && $request->has('cover_caption')) {
                $validatedData['thumbnail_caption'] = $validatedData['cover_caption'];
            }

            // copy cover_artist_id to thumbnail_artist_id if not provided 
            if (!$request->has('thumbnail_artist_id') && $request->has('cover_artist_id')) {
                $validatedData['thumbnail_artist_id'] = $validatedData['cover_artist_id'];
            }
        } else {

            // Handler 3: thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('articles/thumbnail', 'public');
                $validatedData['thumbnail'] = $thumbnailPath;
            }
        }

        // Handler 4: date/time for published_at
        if (isset($validatedData['published_at']) && $validatedData['published_at']) {

            // date provided (either now or past)
            $validatedData['published_at'] = Carbon::parse($validatedData['published_at']);
        } else {

            $validatedData['published_at'] = Carbon::now();
        }

        // $validatedData['published_at'] = Carbon::now();
        $article = Article::create($validatedData);
        return ArticleResource::make($article);

    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // eager load Article relationships to User (n+1 problem fix)
        $article->load(['writer', 'coverArtist', 'thumbnailArtist']);
        return ArticleResource::make($article);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $validatedData = $request->validated();

        // Handler 1: cover photo upload or URL
        if ($request->hasFile('cover_photo')) {

            // delete old cover if it exists
            if ($article->cover_photo && Storage::disk('public')->exists($article->cover_photo)) {
                Storage::disk('public')->delete($article->cover_photo);
            }

            $validatedData['cover_photo'] = $request->file('cover_photo')->store('articles/covers', 'public');

        }


        /**
         * Handler 2: thumbnail_same_as_cover logic 
         * check thumbnail path and caption based on thumbnail_same_as_cover
         */
        if ($request->has('thumbnail_same_as_cover') && $request->thumbnail_same_as_cover) {

            // if same as cover, thumbnail with adapt cover_photo  
            $validatedData['thumbnail'] = $validatedData['cover_photo'] ?? $article->cover_photo;

            // adapt cover metadata if not provided
            if (!$request->has('thumbnail_caption')) {
                $validatedData['thumbnail_caption'] = $validatedData['cover_caption'] ?? $article->cover_caption;
            }

            // adapt cover artist 
            if (!$request->has('thumbnail_artist_id')) {
                $validatedData['thumbnail_artist_id'] = $validatedData['thumbnail_artist_id'] ?? $article->cover_artist_id;
            }

        } else {

            // Handler 3: thumbnail upload (only if not using same as cover)
            if ($request->hasFile('thumbnail')) {

                // delete old thumbnail if it exists and it's not same as cover
                if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
                    Storage::disk('public')->delete($article->thumbnail);
                }

                $validatedData['thumbnail'] = $request->file('thumbnail')->store('articles/covers', 'public');
            }
            // if thumbnail is provided as URL or string, it's already in $validatedData
        }


        Log::info('Request method: ' . $request->method());
        Log::info('Request data: ', $request->all());
        Log::info('Files: ', $request->allFiles());
        Log::info('Has cover_photo file: ' . ($request->hasFile('cover_photo') ? 'yes' : 'no'));
        Log::info('Has thumbnail file: ' . ($request->hasFile('thumbnail') ? 'yes' : 'no'));

        $article->update($validatedData);
        // reload relationships
        $article->load('writer', 'coverArtist', 'thumbnailArtist');
        return ArticleResource::make($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }
}