<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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

        }

        $user->update($validatedData); # update user 
        return UserResource::make($user); # return updated data


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}