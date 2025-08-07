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
        unset($validatedData['term']); # remove from user data because it's not a column in the user table  

        // Handler 1: profile pic upload
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('users/id-pics', 'public');
            $validatedData['profile_pic'] = $profilePicPath;
        }

        $user = User::create($validatedData);

        // Insert ed board entry when it's provided 
        if ($term) {
            $user->editorialBoards()->create([
                'term' => $term,
                'is_current' => true # make first term current by default
            ]);
        }

        // Password reset token manual generation because we no longer use Password::sendResetLink()
        $token = Password::broker()->createToken($user); #createToken requires CanResetPassword in user Model 

        // Send custom set password notification 
        $user->notify(new SetPasswordNotification($token));

        return response()->json([
            'success' => true,
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
     * Add term to user 
     */
    public function addTerm(Request $request, User $user)
    {
        $this->authorize('create', $user);

        $request->validate([
            'term' => 'required|string',
            'is_current' => 'boolean'
        ]);

        $isCurrent = $request->input('is_current', false); # if no term is provided defaults to false 

        if ($user->editorialBoards()->where('term', $request->term)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'The term already exists for the user'
            ], 422);
        }


        // If is_current = true; deactivate other terms 
        if ($isCurrent) {
            $user->editorialBoards()->update(['is_current' => false]);
        }

        // Insert the data 
        $editorialBoard = $user->editorialBoards()->create([
            'term' => $request->term,
            'is_current' => $isCurrent
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Term added successfully',
            'data' => $editorialBoard
        ]);
    }

    /**
     * Delete a user's term 
     */
    public function deleteTerm(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        $request->validate([
            'editorial_board_id' => 'required|exists:editorial_boards,id'
        ]);

        // Retrieve specific ed board record associated with the $user 
        $editorialBoard = $user->editorialBoards()->findOrFail($request->editorial_board_id);

        // Check if it's the only term for the user, hence it cannot be deleted 
        if ($user->editorialBoards()->count() === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the only term for the user'
            ], 422);
        }

        // Check if we're trying to delete the current term 
        $isCurrentTerm = $user->currentEditorialBoard->id === $editorialBoard->id;

        $editorialBoard->delete();

        $message = $isCurrentTerm
            ?   'Current term was deleted, previous term is now the current'
            : 'Term deleted';

        return response()->json([
            'success' => true,
            'message' => $message,
            'current_term' => $user->fresh()->currentTerm()
        ]);
    }

    /**
     * Set an active term 
     */
    public function setCurrentTerm(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'editorial_board_id' => 'required|exists:editorial_boards,id'
        ]);

        // Retrieves record from editorial_boards
        $selectedBoard = EditorialBoard::findOrFail($request->editorial_board_id);

        // Update existing or create new one 

        // Set all user's term to inactive
        $user->editorialBoards()->update(['is_current' => false]);

        $editorialBoard = $user->editorialBoards()->updateOrCreate(
            ['term' => $selectedBoard->term], # search criteria 
            ['term' => $selectedBoard->term, 'is_current' => true] # value to update/create
        );

        return response()->json([
            'success' => true,
            'message' => 'Active term updated successfully',
            'data' => $editorialBoard,
        ]);
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
}
