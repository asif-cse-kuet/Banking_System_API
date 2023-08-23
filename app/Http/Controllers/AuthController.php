<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades;
use App\Models\User;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Attempt to authenticate the user
            if (!Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = User::where('email', $request->input('email'))->firstOrFail();

            return response()->json(['message' => 'Login successful', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 401);
        }
    }

}
