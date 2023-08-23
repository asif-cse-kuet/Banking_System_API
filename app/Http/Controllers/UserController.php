<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Transaction;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'account_type' => 'in:Individual,Business',
                'balance' => 'numeric',
                'name' => 'required|string',
                'password' => 'string',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Set default values if not provided
            $name = $request->input('name', 'Default Name');
            $accountType = $request->input('account_type', 'Individual');
            $balance = $request->input('balance', 0);
            $password = Hash::make($request->input('password', 'defaultpassword'));

            // Create user
            $user = new User();
            $user->email = $request->input('email');
            $user->name = $name;
            $user->account_type = $accountType;
            $user->balance = $balance;
            $user->password = $password;
            $user->save();

            // Create initial deposit transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_type = 'Deposit';
            $transaction->amount = $balance;
            $transaction->fee = 0;
            $transaction->date = now();
            $transaction->save();

            // Return a success response
            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}
