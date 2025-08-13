<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulletinRequest;
use App\Http\Requests\UpdateBulletinRequest;
use App\Http\Resources\BulletinResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Types\Relations\Car;

class BulletinController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Bulletin::class);
        $bulletins = Bulletin::with(['writer', 'coverArtist'])->get();
        return BulletinResource::collection($bulletins);
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
    public function store(StoreBulletinRequest $request)
    {
        $this->authorize('create', Bulletin::class);
        $validatedBulletin = $request->validated();

        // Handler 1: cover_photo upload 
        if ($request->hasFile('cover_photo')) {
            $coverPath = $request->file('cover_photo')->store('bulletins/covers', 'public');
            $validatedBulletin['cover_photo'] = $coverPath;
        }

        // Handler 2: date/time for published_at
        if (isset($validatedBulletin['published_at']) && $validatedBulletin['published_at']) {

            # if date is provided parse it into carbon format 
            $validatedBulletin['published_at'] = Carbon::parse($validatedBulletin['published_at']);
        } else {

            $validatedBulletin['published_at'] = Carbon::now();
        }

        $bulletin = Bulletin::create($validatedBulletin);
        # lazy eager loading 
        $bulletin->load(['writer', 'coverArtist']);
        return BulletinResource::make($bulletin);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bulletin $bulletin)
    {
        $this->authorize('view', $bulletin);
        # lazy eager loading 
        $bulletin->load(['writer', 'coverArtist']);
        return BulletinResource::make($bulletin);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bulletin $bulletin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBulletinRequest $request, Bulletin $bulletin)
    {
        $this->authorize('update', $bulletin);
        $validatedBulletin = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: cover_photo upload 
        if ($request->hasFile('cover_photo')) {

            # delete existing cover_photo 
            if ($bulletin->cover_photo && $storage->exists($bulletin->cover_photo)) {
                $storage->delete($bulletin->cover_photo);
            }
            $validatedBulletin['cover_photo'] = $request->file('cover_photo')->store('bulletins/covers', 'public');
        } else {
            # exclude cover_photo 
            unset($validatedBulletin['cover_photo']);
        }

        $bulletin->update($validatedBulletin);
        # lazy eager loading 
        $bulletin->load(['writer', 'coverArtist']);
        return BulletinResource::make($bulletin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bulletin $bulletin)
    {
        $this->authorize('delete', $bulletin);
        // Handler 1: cover_photo deletion 
        if ($bulletin->cover_photo && Storage::disk('public')->exists($bulletin->cover_photo)) {
            Storage::disk('public')->delete($bulletin->cover_photo);
        }

        $bulletin->delete();
        return response()->json([
            'message' => 'Bulletin has been deleted'
        ]);
    }
}
