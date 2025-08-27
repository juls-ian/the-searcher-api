# Scrapped codes in the UserController 

## addTerm()
### 1.0: simpler version
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

### 1.1: prevents term duplication
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

### 1.3: with logs to debug
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


## setCurrentTerm()
### 1.0: creation/update of term depends if it exist
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


## update()
## 1.0: also includes the term in the update
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

## archiveTerm()
### 1.0: initial code
public function archiveTerm(Request $request, User $user)
{
    $this->authorize('update', $user);

    $request->validate([
        'editorial_board_id' => 'required|exists:editorial_boards,id'
    ]);

    $editorialBoard = $user->editorialBoards()->findOrFail($request->editorial_board_id);

    if ($user->editorialBoards()->count() === 1) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot archive the only term for the user'
        ], 422);
    }

    $isCurrentTerm = $user->currentEditorialBoard?->id === $editorialBoard->id;

    $editorialBoard->archive();

    $message = $isCurrentTerm
        ? 'Current term was archived, previous term is now the current'
        : 'Term archived';

    return response()->json([
        'success' => true,
        'message' => $message,
        'current_term' => $user->fresh()->currentTerm()
    ]);
}

## restoreTerm()
### 1.0: initial code
public function restoreTerm(Request $request, User $user)
{
    $this->authorize('update', $user);

    $request->validate([
        'editorial_board_id' => 'required|exists:editorial_boards,id'
    ]);

    $editorialBoard = $user->editorialBoards()->withTrashed()->findOrFail($request->editorial_board_id);

    $editorialBoard->restoreFromArchive();

    return response()->json([
        'success' => true,
        'message' => 'Term restored successfully',
        'data' => $editorialBoard
    ]);
}

## edBoardIndex()
### 1.0: initial code
    public function edBoardIndex(User $user)
    {
        $boards = $user->editorialBoards->map(function ($board) {
            return [
                'term' => $board->term,
                'is_current' => $board->is_automatically_current || $board->is_current,
                'is_archived' => $board->is_archived,
            ];
        });

        return response()->json([
            'data' => $boards,
        ]);
    }
### 1.1: right code
    public function edBoardIndex()
    {
        $boards = EditorialBoard::with('user')
            ->get()
            ->map(function ($board) {
                return [
                    'term' => $board->term,
                    'is_current' => $board->is_automatically_current || $board->is_current,
                    'is_archived' => $board->is_archived,
                    'user' => $board
                ];
            });

        return response()->json([
            'data' => $boards,
        ]);
    }