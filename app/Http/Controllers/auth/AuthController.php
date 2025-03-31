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

    // Function to register a new user
    public function register(Request $request)
    {
        Log::info('Register function started');

        // Validate the incoming request data
        Log::info('Validating request data...');
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username|max:255', // Username is required and must be unique
            'phone' => 'required|string|unique:users,phone|max:15', // Phone number is required and must be unique
            'password' => 'required|string|min:6', // Password is required and must have a minimum length
            'role' => 'required|in:blind,volunteer,admin', // Role is required and must be one of the allowed values
            'latitude' => 'required|numeric|between:-90,90', // Latitude is required and must be within the valid range
            'longitude' => 'required|numeric|between:-180,180', // Longitude is required and must be within the valid range
            'identity_image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Identity image is optional but must meet specified requirements
        ]);

        // If validation fails
        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        // Handle identity image upload if provided
        if ($request->hasFile('identity_image')) {
            Log::info('Uploading identity image...');
            $file = $request->file('identity_image');
            try {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('storage/identity_images');

                // Create the directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $file->move($destinationPath, $imageName);
                $imagePath = 'identity_images/' . $imageName;
                Log::info('Image uploaded successfully', ['path' => $imagePath]);

            } catch (\Exception $e) {
                Log::error('Image upload failed', ['exception' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to upload image'], 500);
            }
        }

        // Create the user in the database
        Log::info('Creating user...');
        try {
            $user = User::create([
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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

    // Function to log in a user
    public function login(Request $request)
    {
        try {
            Log::info('Attempting to log in a user.', ['request' => $request->except('password')]);

            // Validate the input data
            $validateUser = Validator::make($request->all(), [
                'phone_or_username' => 'required', // Phone or username is required
                'password' => 'required', // Password is required
            ]);

            if ($validateUser->fails()) {
                Log::warning('Validation failed for login.', ['errors' => $validateUser->errors()]);

                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            // Determine the type of credential (phone or username)
            $login_type = is_numeric($request->phone_or_username) ? 'phone' : 'username';

            // Prepare credentials for authentication
            $credentials = [
                $login_type => $request->phone_or_username,
                'password' => $request->password,
            ];

            // Attempt to authenticate the user
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

    // Function to log out a user
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

            // Revoke all tokens for the user
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
