<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{



    public function register(Request $request)
    {
        Log::info('Register function started');


        Log::info('Validating request data...');
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username|max:255',
            'phone' => 'required|string|unique:users,phone|max:15',
            'password' => 'required|string|min:6',
            'role' => 'required|in:blind,volunteer,admin',
            'location' => 'nullable|string',
            'identity_image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('identity_image')) {
            Log::info('Uploading identity image...');
            $file = $request->file('identity_image');
            try {
                $imagePath = $file->store('identity_images', 'public');
                Log::info('Image uploaded successfully', ['path' => $imagePath]);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['exception' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to upload image'], 500);
            }
        }


        Log::info('Creating user...');
        try {
            $user = User::create([
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'location' => $request->location,
                'identity_image' => $imagePath,
            ]);

            Log::info('User created successfully', ['user_id' => $user->user_id]);

            return response()->json([
                'message' => 'User registered successfully!',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'User registration failed'], 500);
        }
    }

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

    public function login(Request $request)
{
    try {
        Log::info('Attempting to log in a user.', ['request' => $request->except('password')]);


        $validateUser = Validator::make($request->all(), [
            'phone_or_username' => 'required',
            'password' => 'required',
        ]);

        if ($validateUser->fails()) {
            Log::warning('Validation failed for login.', ['errors' => $validateUser->errors()]);

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
            ], 401);
        }


        $login_type = is_numeric($request->phone_or_username) ? 'phone' : 'username';

        $credentials = [
            $login_type => $request->phone_or_username,
            'password' => $request->password,
        ];


        if (!Auth::attempt($credentials)) {
            Log::warning('Authentication failed. Phone/Username and password do not match.', ['phone_or_username' => $request->phone_or_username]);

            return response()->json([
                'status' => false,
                'message' => 'Phone/Username and password do not match our records',
            ], 401);
        }

        $user = User::where($login_type, $request->phone_or_username)->first();
        Log::info('User logged in successfully.', ['user' => $user]);

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'role' => $user->role,
            'token' => $user->createToken("API TOKEN")->plainTextToken,
        ], 200);
    } catch (\Throwable $th) {
        Log::error('Error occurred while logging in user.', ['exception' => $th]);

        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
public function logout()
{
    try {
        Log::info('Attempting to log out a user.', ['user' => Auth::user()]);

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found for logout.');

            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ], 401);
        }

        $user->tokens()->delete();
        Log::info('User logged out successfully.', ['user' => $user]);

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ], 200);
    } catch (\Throwable $th) {
        Log::error('Error occurred while logging out user.', ['exception' => $th]);

        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}


}
