<?php

namespace App\Http\Controllers;

use App\Models\Multimedia;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMultimediaRequest;
use App\Http\Requests\UpdateMultimediaRequest;
use App\Http\Resources\ArchiveResource;
use App\Http\Resources\MultimediaResource;
use App\Models\Archive;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MultimediaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Multimedia::class);

        # eager load relationships to User 
        $multimedia = Multimedia::with(['multimediaArtists', 'thumbnailArtist'])->get();
        return MultimediaResource::collection($multimedia);
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
    public function store(StoreMultimediaRequest $request)
    {
        $this->authorize('create', Multimedia::class);

        $validatedMultimedia = $request->validated();

        // Handler 1: files upload 
        if ($request->hasFile('files')) {
            $filesPaths = []; # array to store the file paths 
            $files = $request->file('files');

            # convert non-array files into array if needed 
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                $filePath = $file->store('multimedia/files', 'public');
                $filesPaths[] = $filePath;
            }

            $validatedMultimedia['files'] = json_encode($filesPaths);
        } else {
            $validatedMultimedia['files'] = json_encode([]);
        }

        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('multimedia/thumbnails', 'public');
            $validatedMultimedia['thumbnail'] = $thumbnailPath;
        }

        // Handler 3: published_at date/time
        if (isset($validatedMultimedia['published_at']) && $validatedMultimedia['published_at']) {
            # if date is set, convert it to Carbon instance 
            $validatedMultimedia['published_at'] = Carbon::parse($validatedMultimedia['published_at']);
        } else {
            $validatedMultimedia['published_at'] = Carbon::now();
        }

        // Handler 4: multiple multimedia_artists 
        $artistIds = $validatedMultimedia['multimedia_artists_id'] ?? []; # extract the array
        unset($validatedMultimedia['multimedia_artists_id']); # remove from the main data 

        $multimedia = Multimedia::create($validatedMultimedia); # create without artist ids  

        # attach artists, connect to pivot table 
        if (!empty($artistIds)) {
            $multimedia->multimediaArtists()->attach($artistIds); # use pivot table 
        }

        // Eager load relationships to User 
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }

    /**
     * Display the specified resource.
     */
    public function show(Multimedia $multimedia)
    {
        $this->authorize('view', $multimedia);

        // Eager load relationships
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Multimedia $multimedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMultimediaRequest $request, Multimedia $multimedia)
    {
        $this->authorize('update', $multimedia);

        $validatedMultimedia = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: files upload (multiple files as JSON array)
        if ($request->hasFile('files')) {

            # delete old cover if it exists 
            $oldFiles = json_decode($multimedia->files, true);
            if (is_array($oldFiles)) {
                foreach ($oldFiles as $oldFile) {
                    if ($storage->exists($oldFile)) {
                        $storage->delete($oldFile);
                    }
                }
            }

            $filesPaths = []; # path to store the arrays 
            $files = $request->file('files'); # gets the files from request

            // Convert non-array files into array if needed 
            if (!is_array($files)) {
                $files = [$files];
            }

            # store new files 
            foreach ($files as $file) {
                $filePath = $file->store('multimedia/files', 'public');
                $filesPaths[] = $filePath;
            }

            $validatedMultimedia['files'] = json_encode($filesPaths);
        } else {
            // Exclude cover in any subsequent db operation 
            unset($validatedMultimedia['files']);
        }

        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {

            # delete old thumbnails if it exists 
            if ($multimedia->thumbnail && $storage->exists($multimedia->thumbnail)) {
                $storage->delete($multimedia->thumbnail);
            }
            $validatedMultimedia['thumbnail'] = $request->file('thumbnail')->store('multimedia/thumbnails');
        } else {
            unset($validatedMultimedia['thumbnail']);
        }

        // Handler 3: multiple multimedia_artists 
        if (array_key_exists('multimedia_artists_id', $validatedMultimedia)) {
            $artistIds = $validatedMultimedia['multimedia_artists_id'];
            unset($validatedMultimedia['multimedia_artists_id']); # remove from main data

            // Update artist relationships only when explicitly provided 
            if (!empty($artistIds)) {
                $multimedia->multimediaArtists()->sync($artistIds); # sync = replace all ids with new ones 
            }
        }

        $multimedia->update($validatedMultimedia);
        # reload relationships 
        $multimedia->load(['multimediaArtists', 'thumbnailArtist']);
        return MultimediaResource::make($multimedia);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Multimedia $multimedia)
    {
        $this->authorize('delete', $multimedia);
        $storage = Storage::disk('public');
        $trashDir = 'multimedia/trash/';

        // Ensure directory exists 
        if (!$storage->exists($trashDir)) {
            $storage->makeDirectory($trashDir);
        }

        // Delete files stored as JSON array 
        if ($multimedia->files) {
            $files = json_decode($multimedia->files, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    # deletes the local files 
                    if ($storage->exists($file)) {
                        $filename = basename($file);
                        $storage->move($file, $trashDir . $filename);
                    }
                }
            }
        }

        if ($multimedia->thumbnail && $storage->exists($multimedia->thumbnail)) {
            $filename = basename($multimedia->thumbnail);
            $storage->move($multimedia->thumbnail, $trashDir . $filename);
        }

        try {
            $multimedia->delete();
            return response()->json(['message' => 'Multimedia deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete multimedia'], 500);
        }
    }

    /**
     * Permanently delete soft deleted models 
     */
    public function forceDestroy(Multimedia $multimedia)
    {
        $this->authorize('forceDelete', $multimedia);
        $storage = Storage::disk('public');
        $trashDir = 'multimedia/trash/';

        // Delete files 
        if ($multimedia->files) {
            $files = json_decode($multimedia->files, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    $filename = basename($file);
                    $trashPath = $trashDir . $filename;

                    if ($storage->exists($trashPath)) {
                        $storage->delete($trashPath);
                    }
                }
            }
        }

        // Delete multimedia
        if ($multimedia->thumbnail) {
            $filename = basename($multimedia->thumbnail);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->delete($trashPath);
            }
        }

        try {
            $multimedia->forceDelete();
            return response()->json(['message' => 'Multimedia was permanently deleted'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete multimedia'], 500);
        }
    }

    public function restore(Multimedia $multimedia)
    {
        $this->authorize('restore', $multimedia);
        $storage = Storage::disk('public');
        $trashDir = 'multimedia/trash/';

        if ($multimedia->files) {
            $files = json_decode($multimedia->files, true);

            if (is_array($files)) {

                foreach ($files as $file) {
                    $filename = basename($file); # file.jpg
                    $trashPath = $trashDir . $filename;

                    if ($storage->exists($trashPath)) {
                        $storage->move($trashPath, $file);
                    }
                }
            }
        }

        if ($multimedia->thumbnail) {
            $filename = basename($multimedia->thumbnail);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $multimedia->thumbnail);
            }
        }

        $multimedia->restore();

        return response()->json([
            'message' => 'Multimedia was restored',
            'data' => MultimediaResource::make($multimedia)
        ]);
    }

    public function archive($id)
    {
        $multimedia = Multimedia::findOrFail($id); # find multimedia 
        $this->authorize('archive', $multimedia);
        $archive = $multimedia->archive(); # calls the trait method to create archive

        // If trait didn’t create a new archive because the article was already archived
        if (! $archive) {
            return response()->json([
                'message' => 'This multimedia has already been archived'
            ], 409);
        }

        return response()->json([
            'message' => 'Multimedia archived successfully',
            'data' => new ArchiveResource($archive)
        ]);
    }

    public function showArchived($id)
    {

        try {
            $archive = Archive::where('archivable_type', 'multimedia')
                ->where('id', $id)
                ->firstOrFail();
            return response()->json($archive);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Can show only archived multimedia']);
        }
    }

    public function archiveIndex()
    {
        $archivedMultimedia = Multimedia::archived()->get(); # query scope
        return response()->json($archivedMultimedia);
    }
}
