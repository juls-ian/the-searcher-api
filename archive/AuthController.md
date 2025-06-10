public function login(Request $request)
{
    $request->validate([
        'login' => 'required|string', // accepts email or staff_id
        'password' => 'required|string',
    ]);

    $loginField = $this->getLoginField($request->login);
    
    $credentials = [
        $loginField => $request->login,
        'password' => $request->password,
    ];

    if (Auth::attempt($credentials)) {
        // Success logic
        return response()->json([
            'user' => Auth::user(),
            'token' => Auth::user()->createToken('auth-token')->plainTextToken
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}

private function getLoginField($login)
{
    // Check if it's an email format
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        return 'email';
    }
    
    // Otherwise assume it's staff_id
    return 'staff_id';
}