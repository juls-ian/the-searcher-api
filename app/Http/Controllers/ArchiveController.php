<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArchiveRequest;
use App\Http\Requests\UpdateArchiveRequest;
use App\Http\Resources\ArchiveResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    use AuthorizesRequests;

    private function processFiles($data, callable $callback) # callback = a function can be called later 
    {

        // Base case: strings 
        if (is_string($data) && str_contains($data, 'archives/')) {
            $callback($data);
            return;
        }
        // Recursive case: arrays 
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Handle case where value is ['path' => ..., 'original_dir' => ...]
                if (is_array($value) && isset($value['path']) && str_contains($value['path'], 'archives/')) {
                    $callback($value['path']);
                } else {
                    # recurse deeper if it's needed array
                    $this->processFiles($value, $callback);
                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Archive::class);
        // $archives = Archive::with('archivable')->get();
        $archives = Archive::get();
        return ArchiveResource::collection($archives);
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
    public function store(StoreArchiveRequest $request)
    {
        $this->authorize('create', Archive::class);
        $validatedArchive = $request->validated();
        $archivableType = $validatedArchive['archivable_type'];
        $archivableData = $validatedArchive['data'] ?? [];

        #                         'cover' => null 
        foreach ($archivableData as $key => $value) {
            if ($request->hasFile("data.$key")) {

                $files = $request->file("data.$key"); # get uploaded file 

                // Handler 1: multiple files uploaded 
                if (is_array($files)) {
                    $archivableData[$key] = []; # reset to hold multiple files 


                    foreach ($files as $file) {
                        $mimeType = $file->getMimeType(); # get file type: returns "image/jpg"

                        // Build the subdirectory
                        if (str_starts_with($mimeType, 'image/')) {
                            $dir = 'archives/covers';
                        } elseif (str_starts_with($mimeType, 'video/')) {
                            $dir = 'archives/videos';
                        } else {
                            $dir = 'archives/files';
                        }

                        $filename = $archivableType . '-' . $file->hashName(); # add prefix 
                        $path = $file->storeAs($dir, $filename, 'public'); # store file with custom name

                        // Replace original value with URL or storage path 
                        $archivableData[$key][] = [ # multiple file but single file is returned fix 
                            'path' => $path,
                            'original_dir' => $dir
                        ];
                    }
                } else {
                    // Handler 2: single file upload fallback 
                    $file = $files;
                    $mimeType = $file->getMimeType();

                    if (str_starts_with($mimeType, 'image/')) {
                        $dir = 'archives/covers';
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $dir = 'archives/videos';
                    } else {
                        $dir = 'archives/files';
                    }

                    $filename = $archivableType . '-' . $file->hashName();
                    $path = $file->storeAs($dir, $filename, 'public');

                    $archivableData[$key] = [
                        'path' => $path,
                        'original_dir' => $dir
                    ];
                }
            }
        }

        $archive = Archive::create([
            'archivable_type' => $validatedArchive['archivable_type'],
            'archivable_id' => $validatedArchive['archivable_id'] ?? null,
            'title' => $validatedArchive['title'],
            'data' => json_encode($archivableData),
            'archived_at' => now(),
            'archiver_id' => Auth::id()

        ]);

        return response()->json([
            'message' => 'Archive created successfully',
            'data' => new ArchiveResource($archive)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Archive $archive)
    {
        $this->authorize('view', $archive);
        // $archive->load('archivable');
        return ArchiveResource::make($archive);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archive $archive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $this->authorize('update', $archive);

        // When archivable_id is not null 
        if (!is_null($archive->archivable_id)) {
            return response()->json([
                'message' => 'This archive is came from other resource and cannot be updated directly.'
            ], 403);
        }

        $validatedArchive = $request->validated();
        $archivableData = $validatedArchive['data'] ?? null; # raw validated data 
        $archivableType = $validatedArchive['archivable_type'] ?? $archive->archivable_type;

        // Convert to array 
        $oldData = is_string($archive->data)
            # if string (contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            # if data has value it uses that value hence = empty array 
            : ($archive->data ?? []);

        if (is_array($archivableData)) {
            foreach ($archivableData as $key => $value) {
                // Checks if a file was uploaded for this specific field (like data.cover_image or data.video)
                if ($request->hasFile("data.$key")) {

                    $files = $request->file("data.$key"); # get uploaded file 

                    // Handler 1: multiple files 
                    if (is_array($files)) {
                        $archivableData[$key] = [];

                        foreach ($files as $file) {

                            $mimeType = $file->getMimeType(); # get mime type: returns 'image/jpeg', 'video/mp4', 'application/pdf'

                            if (str_starts_with($mimeType, 'image/')) {
                                $dir = 'archives/covers';
                            } elseif (str_starts_with($mimeType, 'videos/')) {
                                $dir = 'archives/videos';
                            } else {
                                $dir = 'archives/files';
                            }

                            $filename = $archivableType . '-' . $file->hashName();
                            $path = $file->storeAs($dir, $filename, 'public');

                            $archivableData[$key][] = [
                                'path' => $path, #replace original value with URL or storage path
                                'original_dir' => $dir # save the original directory
                            ];
                        }

                        // Cleanup old files if exists 
                        if (isset($oldData[$key]) && is_array($oldData[$key])) {
                            foreach ($oldData[$key] as $oldFile) {
                                if (isset($oldFile['path']) && Storage::disk('public')->exists($oldFile['path'])) {
                                    Storage::disk('public')->delete($oldFile['path']);
                                }
                            }
                        }
                        // Handler 2: single file 
                    } else {

                        $file = $files;
                        $mimeType = $file->getMimeType();

                        // Handle different file types storage 
                        if (str_starts_with($mimeType, 'image/')) {
                            $dir = 'archives/covers';
                        } elseif (str_starts_with($mimeType, 'video/')) {
                            $dir = 'archives/videos';
                        } else {
                            $dir = 'archives/files';
                        }

                        // Generating random hash filename with prefix 
                        $filename = $archivableType . '-' . $file->hashName();

                        // Store files first 
                        $path = $file->storeAs($dir, $filename, 'public');

                        $archivableData[$key] = [
                            'path' => $path, #replace original value with URL or storage path 
                            'original_dir' => $dir # save the original directory
                        ];

                        # delete old file only if it exists for this key/field 
                        if (isset($oldData[$key])) {
                            $oldPath = is_array($oldData[$key]) ? $oldData[$key]['path'] : $oldData[$key];
                            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                                Storage::disk('public')->delete($oldPath);
                            }
                        }
                    }
                } else {
                    // if no new file, keep the old files 
                    $archivableData[$key] = $oldData[$key] ?? $value;
                }
            }
        }

        $updateData = []; # batch of updated data 

        // Field whitelist
        foreach (['title', 'archivable_type', 'archivable_id'] as $field) {
            # if field exists in $validatedArchive it copies to the $updateData
            if (isset($validatedArchive[$field])) {
                $updateData[$field] = $validatedArchive[$field];
            }
        }

        // Check if data field is submitted 
        if (isset($validatedArchive['data'])) {
            $updateData['data'] = $archivableData; # fully processed data ready for storage 
        }

        $archive->update($updateData);

        return response()->json([
            'message' => 'Archive successfully updated',
            'data' => new ArchiveResource($archive->fresh())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        $this->authorize('delete', $archive);
        // Convert to array 
        $data = is_string($archive->data)
            # (assumes string contains JSON), convert to assoc array and return array instead of object 
            ? json_decode($archive->data, true)
            : $archive->data;  # if data has value it uses that value 


        $storage = Storage::disk('public');
        $storage->makeDirectory('archives/trash');

        // Recursive helper to walk the structure 
        $moveToTrash = function (&$node) use (&$moveToTrash, $storage) {
            if (is_array($node)) {

                // Case: file info array 
                if (isset($node['path'], $node['original_dir'])) {
                    $currentPath = ltrim($node['path'], '/'); # remove leading slash if any 
                    $newPath = 'archives/trash/' . basename($currentPath);

                    if ($storage->exists($currentPath)) {
                        $storage->move($currentPath, $newPath);
                    }

                    // Update path in db 
                    $node['path'] = $newPath;
                }

                // Recurse into nested arrays 
                foreach ($node as &$child) {
                    $moveToTrash($child);
                }
            }
        };

        $moveToTrash($data);


        // Save updated paths pointing to trash 
        $archive->update(['data' => $data]);

        // Soft delete the archive
        $archive->delete();
        return response()->json([
            'message' => 'Archive deleted successfully'
        ]);
    }

    public function forceDestroy($id)
    {
        $archive = Archive::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $archive);

        // Decode jason if needed 
        $data = is_string($archive->data)
            ? json_decode($archive->data, true)
            : $archive->data;

        $this->processFiles($archive->data ?? [], function ($path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        });

        // Only update if the archive has archivable_id
        if ($archive->archivable_id && $archive->archivable) {      # polymorph relationship
            $archive->archivable()->update(['archived_at' => null]);
        }

        // Permanently delete the archived
        $archive->forceDelete();

        return response()->json([
            'message' => 'Archive was permanently deleted'
        ]);
    }

    public function restore($id)
    {
        // Find soft deleted entries
        $archive = Archive::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $archive);

        // Decode archive data from db as [] 
        $data = is_string($archive->data)
            ? json_decode($archive->data, true)
            : $archive->data;

        $storage = Storage::disk('public');

        // Recursive restore closure function 
        $restoreFiles = function (&$node) use (&$restoreFiles, $storage) {
            if (is_array($node)) {
                if (isset($node['path'], $node['original_dir'])) {
                    $currentPath = $node['path'];
                    $originalDir = $node['original_dir'];

                    if (str_starts_with($currentPath, 'archives/trash/')) {
                        $newPath = $originalDir . '/' . basename($currentPath);

                        if ($storage->exists($currentPath)) {
                            $storage->move($currentPath, $newPath);
                        }

                        $node['path'] = $newPath;
                    }
                }

                # recurse deeper into the child elements 
                foreach ($node as &$child) {
                    $restoreFiles($child);
                }
            }
        };

        $restoreFiles($data);

        // Only update if the archive has archivable_id
        if ($archive->archivable_id && $archive->archivable) {       # polymorph relationship
            $archive->archivable()->update(['archived_at' => now()]);
        }

        // Save back modified as json to database 
        $archive->data = json_encode($data);
        // Restore archive 
        $archive->restore();


        return response()->json([
            'message' => 'Archive was restored',
            'data' => ArchiveResource::make($archive->load('archivable'))
        ]);
    }

    /**
     * Unarchive 
     */
    public function unarchive($id)
    {
        $archive = Archive::findOrFail($id); # find the archived article 
        $this->authorize('unarchive', $archive);

        // Only update if the archive has archivable_id
        if ($archive->archivable_id && $archive->archivable) { # polymorph relationship
            // Set the archived_at in the related model 
            $archive->archivable()->update(['archived_at' => null]);

            $archive->forceDelete(); # permanently delete archived record 

            return response()->json([
                'message' => 'Archive was unarchive successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Cannot unarchive this archive'
            ], 403);
        }
    }

    /**
     * Show soft deleted archives
     */
    public function showTrashed()
    {
        $trashedArchives = Archive::onlyTrashed()->with('archivable')->get();
        return ArchiveResource::collection($trashedArchives);
    }
}
