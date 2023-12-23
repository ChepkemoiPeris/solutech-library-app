<?php

namespace App\Http\Controllers;
 
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validatedData = $request->validate([
            'name' => 'required',
            'password' => 'required|min:5',
            'email' => 'required|email|unique:users'
        ], [
            'name.required' => 'Name field is required.',
            'password.required' => 'Password field is required.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email field must be email address.'
        ]);
  
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
          
        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response($response, Response::HTTP_OK); 
        
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string',
        ]);
 
        $user = User::where('email', $fields['email'])->first(); 
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'User not found or Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete(); 
        return response(['message' => 'Logged out'], Response::HTTP_OK);
    }
}
