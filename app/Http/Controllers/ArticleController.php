<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArchiveResource;
use App\Http\Resources\ArticleResource;
use App\Models\Archive;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);
        $perPage = $request->input('per_page', 12); // defaults to 12
        // eager load Article relationships to User (n+1 problem fix)
        $articles = Article::with(['category', 'writer', 'coverArtist', 'thumbnailArtist'])
            ->latest()
            ->paginate($perPage);

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
        $this->authorize('create', Article::class);
        $validatedArticle = $request->validated();

        // Handler 1: cover photo upload
        if ($request->hasFile('cover_photo')) {
            $coverPath = $request->file('cover_photo')->store('articles/covers', 'public');
            $validatedArticle['cover_photo'] = $coverPath;
        }

        // Handler 2: thumbnail_same_as_cover logic
        if ($request->has('thumbnail_same_as_cover') && $request->thumbnail_same_as_cover) {

            // use same file as cover_photo for thumbnail
            $validatedArticle['thumbnail'] = $validatedArticle['cover_photo'] ?? null;

            // copy cover_photo metadata to thumbnail if not provided
            if (! $request->has('thumbnail_caption') && $request->has('cover_caption')) {
                $validatedArticle['thumbnail_caption'] = $validatedArticle['cover_caption'];
            }

            // copy cover_artist_id to thumbnail_artist_id if not provided
            if (! $request->has('thumbnail_artist_id') && $request->has('cover_artist_id')) {
                $validatedArticle['thumbnail_artist_id'] = $validatedArticle['cover_artist_id'];
            }
        } else {

            // Handler 3: thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('articles/thumbnail', 'public');
                $validatedArticle['thumbnail'] = $thumbnailPath;
            }
        }

        // Handler 4: date/time for published_at
        if (isset($validatedArticle['published_at']) && $validatedArticle['published_at']) {

            // date provided (either now or past)
            $validatedArticle['published_at'] = Carbon::parse($validatedArticle['published_at']);
        } else {

            $validatedArticle['published_at'] = Carbon::now();
        }

        // Handler 5: Ticker
        if (isset($validatedArticle['add_to_ticker']) && $validatedArticle['add_to_ticker']) {
            // ticker expiration 7 days after publication
            $validatedArticle['ticker_expires_at'] = Carbon::parse($validatedArticle['published_at'])
                ->addDays(7);
        } else {
            $validatedArticle['ticker_expires_at'] = null;
        }

        // $validatedArticle['published_at'] = Carbon::now();
        $article = Article::create($validatedArticle);
        // Eager load Article relationships to User (n+1 problem fix)
        $article->load(['category', 'writer', 'coverArtist', 'thumbnailArtist']);

        return ArticleResource::make($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $this->authorize('view', $article);

        // Eager load Article relationships to User (n+1 problem fix)
        $article->load(['category', 'writer', 'coverArtist', 'thumbnailArtist']);

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
        $this->authorize('update', $article);

        $validatedArticle = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: cover photo upload or URL
        if ($request->hasFile('cover_photo')) {

            // delete old cover if it exists
            if ($article->cover_photo && $storage->exists($article->cover_photo)) {
                $storage->delete($article->cover_photo);
            }

            $validatedArticle['cover_photo'] = $request->file('cover_photo')->store('articles/covers', 'public');
        } else {
            // Exclude cover in any subsequent db operation
            unset($validatedArticle['cover_photo']);
        }

        /**
         * Handler 2: thumbnail_same_as_cover logic
         * when thumbnail is the same as cover, copy all cover properties to thumbnail
         */
        if ($request->has('thumbnail_same_as_cover') && $request->thumbnail_same_as_cover) {

            // if same as cover, force thumbnail with adapt cover_photo
            $validatedArticle['thumbnail'] = $validatedArticle['cover_photo'] ?? $article->cover_photo;

            // force adapt cover artist
            if (! $request->has('thumbnail_artist_id')) {
                $validatedArticle['thumbnail_artist_id'] = $validatedArticle['cover_artist_id'] ?? $article->cover_artist_id;
            }
        } else {

            // Handler 3: thumbnail upload (only if not using same as cover)
            if ($request->hasFile('thumbnail')) {

                // delete old thumbnail if it exists and it's not same as cover
                if ($article->thumbnail && $storage->exists($article->thumbnail)) {
                    $storage->delete($article->thumbnail);
                }

                $validatedArticle['thumbnail'] = $request->file('thumbnail')->store('articles/covers', 'public');
            } else {
                // Exclude cover in any subsequent db operation
                unset($validatedArticle['thumbnail']);
            }
        }

        $article->update($validatedArticle);
        // reload relationships
        $article->load(['category', 'writer', 'coverArtist', 'thumbnailArtist']);

        return ArticleResource::make($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        $storage = Storage::disk('public');
        $trashDir = 'articles/trash/';

        $destroyFile = function ($filePath) use ($storage, $trashDir) {
            // Check if is empty, null, falsy
            if (! $filePath) {
                return;
            }

            $filename = basename($filePath); // cover123.jpg
            $trashPath = $trashDir . $filename; // articles/covers/cover123.jpg

            // if it exists in the original path
            if ($storage->exists($filePath)) {
                $storage->move($filePath, $trashPath);
            }
        };

        // Check if trash directory exists
        if (! $storage->exists($trashDir)) {
            $storage->makeDirectory($trashDir);
        }

        $destroyFile($article->cover_photo);
        $destroyFile($article->thumbnail);

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

    public function forceDestroy(Article $article)
    {
        $this->authorize('forceDelete', $article);
        $storage = Storage::disk('public');
        $trashDir = 'articles/trash/';

        $forceDestroyFile = function ($filePath) use ($storage, $trashDir) {
            if (! $filePath) {
                return;
            }

            $filename = basename($filePath);
            $trashPath = $trashDir . $filename;

            // if it exists in the trash
            if ($storage->exists($trashPath)) {
                $storage->delete($trashPath);
            }
        };

        $forceDestroyFile($article->cover_photo);
        $forceDestroyFile($article->thumbnail);

        $article->forceDelete();

        return response()->json([
            'message' => 'Article was permanently deleted',
        ], 200);
    }

    public function restore(Article $article)
    {
        $this->authorize('restore', $article);
        $storage = Storage::disk('public');
        $trashDir = 'articles/trash/';

        // Anonymous/closure helper function
        $restoreFile = function ($filePath) use ($storage, $trashDir) {
            if (! $filePath) {
                return;
            }

            $filename = basename($filePath);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $filePath);
            }
        };

        $restoreFile($article->cover_photo);
        $restoreFile($article->thumbnail);

        $article->restore();

        return response()->json([
            'message' => 'Article was restored',
            'data' => ArticleResource::make($article),
        ]);
    }

    /**
     * Archive an article
     */
    public function archive($id)
    {
        $article = Article::findOrFail($id); // find article or fail
        $this->authorize('archive', $article);
        $archive = $article->archive(); // calls the trait method to create archive | returns Archive or null

        // If trait didn’t create a new archive because the article was already archived
        if (! $archive) { // if $archive is falsy (null)
            return response()->json([
                'message' => 'This article has already been archived',
            ], 409);
        }

        return response()->json([
            'message' => 'Article archived successfully',
            'data' => new ArchiveResource($archive),
        ]);
    }

    /**
     * Show all archived articles
     */
    public function archiveIndex()
    {
        $archivedArticles = Archive::where('archivable_type', 'article')
            ->with(['archiver']) // load archiver relationship
            ->orderBy('archived_at', 'desc')
            ->get();

        return ArchiveResource::collection($archivedArticles);
        // return view('articles.archived', compact('articles'));
    }

    /**
     * Show archived article
     */
    public function showArchived($id)
    {
        try {
            $archive = Archive::where('archivable_type', 'article')
                ->where('id', $id)
                ->firstOrFail();

            return new ArchiveResource($archive);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Can only show archived articles']);
        }
    }
}
