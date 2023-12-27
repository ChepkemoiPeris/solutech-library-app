<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{ 
    
    public function index(Request $request)
    {  
        $query = $request->query();

        $page = $query['page'] ?? 1;
        $limit = $query['limit'] ?? 10;

        $searchTerm = $query['email'] ?? null;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $queryBuilder = User::query();

        if ($searchTerm) {
            $queryBuilder->where('email', 'like', "%{$searchTerm}%");
        }

        $users = $queryBuilder->paginate($limit); 

        return response()->json($users);
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
        $email_exist =  User::where('id','!=',$id)->where('email',$request->email)->first();
        if ($email_exist) {
            return response()->json(['message' => 'User with that email already exist'], Response::HTTP_CONFLICT);
        }
        $fillableFields = ['name', 'email', 'role', 'password'];
        $dataToUpdate = $request->only($fillableFields);
    
        $user->update($dataToUpdate);
    
        return response(['message' => 'Updated successfully'], Response::HTTP_OK); 
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
    public function changePassword(Request $request){
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => 'required|min:6|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], Response::HTTP_CONFLICT);
        }
    
        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);
    
        return response()->json(['message' => 'Password changed successfully']);
    }
}
