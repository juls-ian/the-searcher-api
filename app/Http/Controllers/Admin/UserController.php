<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use App\Models\EditorialBoard;
use Illuminate\Support\Facades\Password;
use App\Notifications\SetPasswordNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $staffs = User::with([
            'writtenArticles',
            'articleCoverContributions',
            'articleThumbnailContributions',
            'editorialBoards',
            'currentEditorialBoard'
        ])
            ->get();
        return UserResource::collection($staffs);
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
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $validatedData = $request->validated();

        /**
         * Ensure password is unset in $validatedData when creating user at first (it will be null if not)
         * because password is set through the email
         */
        unset($validatedData['password']);
        $term = $validatedData['term'] ?? null; # extract 'term' data before creating user
        $boardPositionIds = $validatedData['board_positions_ids'] ?? [];

        unset($validatedData['term']); # remove from user data because it's not a column in the user table
        unset($validatedData['board_position_ids']);

        // Handler 1: profile pic upload
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('users/id-pics', 'public');
            $validatedData['profile_pic'] = $profilePicPath;
        }

        $user = User::create($validatedData);

        // Insert ed board entry when it's provided
        if ($term && !empty($boardPositionIds)) {
            foreach ($boardPositionIds as $positionId) {

                $user->editorialBoards()->create([
                    'term' => $term,
                    'board_position_id' => $positionId,
                    'is_current' => true # make first term current by default
                ]);
            }
        }

        // Password reset token manual generation because we no longer use Password::sendResetLink()
        $token = Password::broker()->createToken($user); #createToken requires CanResetPassword in user Model

        // Send custom set password notification
        $user->notify(new SetPasswordNotification($token));

        return response()->json([
            'message' => 'Successfully registered the staff. An email has been sent to them to set their password. '
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return UserResource::make($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validatedData = $request->validated();
        $storage = Storage::disk('public');

        unset($validatedData['term']); # remove term from user data

        // Handler: profile pic upload
        if ($request->hasFile('profile_pic')) {

            # delete previous pic if it exists
            if ($user->profile_pic && $storage->exists($user->profile_pic)) {
                $storage->delete($user->profile_pic);
            }

            # upload new pic
            $validatedData['profile_pic'] = $request->file('profile_pic')->store('users/id-pics');
        } else {
            // Exclude profile pic in any subsequent db operation
            unset($validatedData['profile_pic']);
        }

        $user->update($validatedData); # update user
        $user->load(['editorialBoards']);

        return UserResource::make($user); # return updated data
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        // Delete profile pic before deleting user
        $storage = Storage::disk('public');

        if ($user->profile_pic && $storage->exists($user->profile_pic)) {
            $storage->delete($user->profile_pic);
        }

        $user->delete();
        return response()->json(['message' => 'User was deleted'], 200);
    }

    /**
     * Add term to user
     */
    public function addTerm(Request $request, User $user)
    {
        $this->authorize('create', $user);

        $request->validate([
            'term' => 'required|string',
            'is_current' => 'boolean',
            'board_position_ids' => 'required|array',
            'board_position_ids.*' => 'exists:board_positions,id'
        ]);

        $isCurrent = $request->input('is_current', false); # if no term is provided defaults to false

        if ($user->editorialBoards()->where('term', $request->term)->exists()) {
            return response()->json([
                'message' => 'The term already exists for the user'
            ], 422);
        }


        // If is_current = true; deactivate other terms
        if ($isCurrent) {
            $user->editorialBoards()->update(['is_current' => false]);
        }

        // Insertion of multiple positions for the term
        $editorialBoards = [];
        foreach ($request->board_position_ids as $positionId) {
            // Insert the data
            $editorialBoards[] = $user->editorialBoards()->create([
                'term' => $request->term,
                'board_position_id' => $positionId,
                'is_current' => $isCurrent
            ]);
        }



        return response()->json([
            'message' => 'Term added successfully',
            'data' => $editorialBoards
        ]);
    }

    /**
     * Hard delete a user's term
     */
    public function deleteTerm(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        $request->validate([
            'editorial_board_id' => 'required|integer|exists:editorial_boards,id' // Change to term instead of editorial_board_id
        ]);

        // Retrieve specific ed board record associated with the $user
        $editorialBoard = $user->editorialBoards()->findOrFail($request->editorial_board_id);
        $term = $editorialBoard->term;
        $isCurrent = $editorialBoard->is_current;

        //  Verify it belongs to this user
        if ($editorialBoard->user_id !== $user->id) {
            return response()->json(['message' => 'Editorial board not found for this user']);
        }



        // Check if it's the only term for the user, hence it cannot be deleted
        $totalBoards = $user->editorialBoards()->count();

        if ($totalBoards === 1) {
            return response()->json([
                'message' => 'Cannot delete the only term for the user'
            ], 422);
        }

        // Delete the specific entry
        $editorialBoard->delete();

        // If current position is deleted, check if there are other positions in the same term
        if ($isCurrent) {
            $remainingInTerm = $user->editorialBoards()->where('term', $term)->count();

            // If no more positions in this term, previous term = current
            if ($remainingInTerm === 0) {
                // Set most recent previous term as current
                $previousTerm = $user->editorialBoards()
                    ->where('term', '<', $term)
                    ->orderBy('term', 'desc')
                    ->first();

                if ($previousTerm) {
                    $user->editorialBoards()
                        ->where('term', $previousTerm->term)
                        ->update(['is_current' => true]);
                }
            }
        }

        return response()->json([
            'message' => 'Editorial board position has been deleted successfully',
            'current_term' => $user->fresh()->editorialBoards()->where('is_current', true)->first()?->term
        ]);
    }

    /**
     * Set an active term
     */
    public function setCurrentTerm(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'term' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!$user->editorialBoards()->where('term', $value)->exists()) {
                        $fail('The selected term does not exist for this user');
                    }
                }
            ]
        ]);

        // Check if the term exists for this user
        if (!$user->editorialBoards()->where('term', $request->term)->exists()) {
            return response()->json(['message' => 'Term not found for this user'], 404);
        }

        // Set all user's terms to inactive
        $user->editorialBoards()->update(['is_current' => false]);

        // Set the selected term as current
        $user->editorialBoards()
            ->where('term', $request->term)
            ->update(['is_current' => true]);

        return response()->json([
            'message' => 'Active term updated successfully',
            'data' => $user->fresh()->editorialBoards()->where('is_current', true)->get()
        ]);
    }

    /**
     * Show ed boards
     */
    public function edBoardIndex()
    {
        $boards = EditorialBoard::with('user') #fetch the data
            ->get()
            ->groupBy('term') # group by terms
            // Map each group | $term = group key, $group = sub collection of ed board that belong to $term
            ->map(function ($group, $term) {
                return [
                    'term' => $term,
                    'current' => $group->first()->is_automatically_current || $group->first()->is_current,
                    'archived' => $group->first()->is_archived,
                    'members' => $group->map(function ($board) { # loop over all members in the group
                        return [
                            'id' => $board->user->id,
                            'full_name' => $board->user->full_name,
                            'pen_name' => $board->user->pen_name,
                            'board_position' => $board->boardPosition->name, // from relationship to BoardPosition
                            'profile_pic' => $board->user->profile_pic,
                            'status' => $board->user->status,
                            'role' => $board->user->role,
                        ];
                    })->values() // array might serialize as an object instead of an array if the collection keys aren't sequential (0, 1, 2...).
                ];
            })
            ->values(); # reset keys so it's clean array 0, 1, 2

        return response()->json([
            'data' => $boards,
        ]);
    }
}
