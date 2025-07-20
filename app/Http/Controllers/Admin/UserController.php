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
        $staffs = User::with(['writtenArticles', 'coverContributions', 'thumbnailContributions'])->get();
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

        // Ensuring password is unset in $validatedData when creating user at first 
        unset($validatedData['password']);

        // Handler 1: profile pic upload
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('users/id-pics', 'public');
            $validatedData['profile_pic'] = $profilePicPath;
        }

        $user = User::create($validatedData);

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
}