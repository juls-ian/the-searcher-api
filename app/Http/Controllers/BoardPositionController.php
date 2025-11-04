<?php

namespace App\Http\Controllers;

use App\Models\BoardPosition;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BoardPositionController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', BoardPosition::class);
        $boardPosition = BoardPosition::with('users')
            ->get()
            ->groupBy('name')
            ->map(function ($group, $position) { // group = Collection & position = key
                return [
                    'position' => $position,
                    'holders' => $group->flatMap(function ($boardPos) {
                        // 'users' (plural) and flatMap to handle the collection
                        return $boardPos->users->map(function ($user) { // $boardPos = 1 individual boardpoistion model
                            return [
                                'id' => $user->id,
                                'full_name' => $user->full_name
                            ];
                        });
                    })->values(), // ensure to get array instead of object

                ];
            })
            ->values();  // prevent collection from the string keys
        return response()->json(['data' => $boardPosition]);
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
    public function store(Request $request, User $user)
    {
        $this->authorize('create', BoardPosition::class);

        $validatedData = $request->validate([
            'name' => 'required|string'
        ]);

        $boardPosition = BoardPosition::create($validatedData);

        return response()->json([
            'data' => $boardPosition->only(['id', 'name', 'category'])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BoardPosition $boardPosition)
    {
        $this->authorize('view', $boardPosition);

        $boardPosition->load(['users']);

        $boardPosition = [
            'position' => $boardPosition->name,
            'holders' => $boardPosition->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name
                ];
            })->values()
        ];

        return response()->json([
            'data' => $boardPosition
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BoardPosition $boardPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoardPosition $boardPosition)
    {
        $this->authorize('update', $boardPosition);

        $validatedData = $request->validate([
            'name' => 'required|string'
        ]);

        $boardPosition->update($validatedData);
        return response()->json([
            'data' => $boardPosition->only(['id', 'name', 'category'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoardPosition $boardPosition)
    {
        $this->authorize('delete', $boardPosition);
        $boardPosition->delete();
        return response()->json(['message' => 'Board position deleted successfully'], 200);
    }

    public function forceDestroy(BoardPosition $boardPosition)
    {
        $this->authorize('forceDelete', $boardPosition);
        $boardPosition->forceDelete();
        return response()->json([
            'message' => 'Position was permanently deleted'
        ], 200);
    }

    public function restore(BoardPosition $boardPosition)
    {
        $this->authorize('restore', $boardPosition);
        $boardPosition->restore();
        return  response()->json([
            'message' => 'Board position was restored',
            'data' => $boardPosition->only(['id', 'name', 'category'])
        ]);
    }
}
