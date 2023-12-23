<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
class UsersController extends Controller
{ 
    
    public function index(Request $request)
    {
        $page = $request->input('page') ?? 1;
        $limit = $request->input('length') ?? 10;
    
        $users = User::paginate($limit, ['*'], 'page', $page);
    
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $users->items(),
        ], Response::HTTP_OK);
    }
 

   
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required', 
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users'
        ], [
            'name.required' => 'Name field is required.',
            'password.required' => 'Password field is required.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email field must be email address.'
        ]);
         if($request->role){
            $validatedData['role'] = $request->role;
         }
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
          
        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response($response, Response::HTTP_OK); 
    }

    

    
    public function update(Request $request, string $id)
    { 
        $user = User::findOrFail($id); 
        $validatedData = $request->validate([
            'name' => 'required', 
            'email' => 'required|email|unique:users'
        ], [
            'name.required' => 'Name field is required.', 
            'email.required' => 'Email field is required.',
            'email.email' => 'Email field must be email address.'
        ]); 
       $user->update($validatedData); 
 
        return response(['message'=>'Updated successfully'], Response::HTTP_OK);  
    }

    
    public function destroy(string $id)
    {
   
        $user = User::findOrFail($id);
        $user->delete(); 
 
        return response(['message'=>'deleted successfully'], Response::HTTP_OK);    
    }

    public function getUser(string $id)
    {
        $user = User::findOrFail($id);  
        return response()->json($user);
    }
}
