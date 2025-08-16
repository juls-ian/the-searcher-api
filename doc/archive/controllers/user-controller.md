# Scrapped codes in the UserController 

## v.1: addTerm - simpler version
    public function addTerm(Request $request, User $user)
    {

        $this->authorize('create', $user);

        $request->validate([
            'term' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $isActive = $request->input('is_active', false); # if no term is provided defaults to false 

        // If this term is made active, deactivate others 
        if ($isActive) {
            $user->editorialBoards()->update(['is_active' => false]);
        }

        // Insert the data 
        $editorialBoard = $user->editorialBoards()->create([
            'term' => $request->term,
            'is_active' => $isActive
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Term added successfully',
            'data' => $editorialBoard
        ]);
    }

## v.1.1: addTerm - prevents term duplication
    public function addTerm(Request $request, User $user)
    {
        $this->authorize('create', $user);

        $request->validate([
            'term' => 'required|string',
            'is_current' => 'required|boolean'
        ]);

        $isCurrent = $request->input('is_current', false); # if no term is provided defaults to false 
        $existingTerm = $user->editorialBoards()
            ->where('term', $request->term)
            ->first();

        if ($existingTerm) {
            # if term exists and we want to make it current 
            if ($isCurrent) {
                # set all is_current = false first 
                $user->editorialBoards()->where('id', '!=', $existingTerm->id)->update(['is_current' => false]);

                # set this one as current 
                $existingTerm->update(['is_current' => true]);
            }
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

## v.1.2: addTerm - with logs to debug
    public function addTerm(Request $request, User $user)
    {
        $this->authorize('create', $user);

        $request->validate([
            'term' => 'required|string',
            'is_current' => 'boolean'
        ]);

        $isCurrent = $request->input('is_current', false); # if no term is provided defaults to false 

        Log::info('Request data:', [
            'is_current_raw' => $request->is_current,
            'is_current_boolean' => $isCurrent
        ]);

        if ($user->editorialBoards()->where('term', $request->term)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'The term already exists for the user'
            ], 422);
        }


        // If is_current = true; deactivate other terms 
        if ($isCurrent) {
            $affected = $user->editorialBoards()->update(['is_current' => false]);
            $user->editorialBoards()->update(['is_current' => false]);
            Log::info('Set other terms to false, affected rows:' . $affected);
        }

        // Insert the data 
        $editorialBoard = $user->editorialBoards()->create([
            'term' => $request->term,
            'is_current' => $isCurrent
        ]);

        Log::info('Created editorial board:', $editorialBoard->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Term added successfully',
            'data' => $editorialBoard
        ]);
    }


## v.2: setCurrentTerm - creation/update of term depends if it exist
    public function setCurrentTerm(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'editorial_board_id' => 'required|exists:editorial_boards,id'
        ]);

        // Retrieves record from editorial_boards
        $selectedBoard = EditorialBoard::findOrFail($request->editorial_board_id);

        // Check if User already has the term
        $existingBoard = $user->editorialBoards()
            ->where('term', $selectedBoard->term)
            ->first();

        if ($existingBoard) {
            # if term exists, just touch the updated_at to make it latest 
            $existingBoard->touch();
            $editorialBoard = $existingBoard; # final value 
        } else {
            # else create new term 
            $editorialBoard = $user->editorialBoards()->create([
                'term' => $selectedBoard->term
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Active term updated successfully',
            'data' => $editorialBoard,
        ]);
    }

## v.3: setActiveTerm - sets an active term (simple)
    public function setActiveTerm(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'editorial_board_id' => 'required|exists:editorial_boards,id'
        ]);

        // Deactivate all terms for this user 
        $user->editorialBoards()->update(['is_active' => false]);

        // Activate selected term 
        $editorialBoard = $user->editorialBoards()->findOrFail($request->editorial_boards_id);
        $editorialBoard->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Active term updated successfully',
            'data' => $editorialBoard,
        ]);
    }

## v.4: update() - also includes the term in the update
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validatedData = $request->validated();
        $storage = Storage::disk('public');

        // Extract term data 
        $term = $validatedData['term'] ?? null;
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

        //  Add term it doesn't exist (don't update existing terms)
        if ($term && !$user->editorialBoards()->where('term', $term)->exists()) {

            // If user doesn't have any terms, make this term current 
            // $isFirst = $user->editorialBoards()->count() === 0;

            // $user->editorialBoards()->create([
            //     'term' => $term,
            //     'is_current' => $isFirst
            // ]);

            $user->editorialBoards()->update(['term' => $term]);
        }

        return UserResource::make($user); # return updated data
    }