<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{

    // Function to update user information
    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();
        $input = $request->all(); // Get all input data from the request

        // Update username if provided
        if (isset($input['username'])) {
            $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            ]);
            $user->username = $input['username'];
        }

        // Update phone number if provided
        if (isset($input['phone'])) {
            $request->validate([
                'phone' => 'required|string|max:20|unique:users,phone,' . $user->user_id . ',user_id',
            ]);
            $user->phone = $input['phone'];
        }

        // Update latitude if provided
        if (isset($input['latitude'])) {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
            ]);
            $user->latitude = $input['latitude'];
        }

        // Update longitude if provided
        if (isset($input['longitude'])) {
            $request->validate([
                'longitude' => 'required|numeric|between:-180,180',
            ]);
            $user->longitude = $input['longitude'];
        }

        // Update password if new password is provided
        if (isset($input['new_password'])) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Check if the current password matches the user's existing password
            if (!Hash::check($input['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 400);
            }

            // Hash and update the new password
            $user->password = bcrypt($input['new_password']);
        }

        // Save the updated user information
        $user->save();

        return response()->json(['message' => 'User info updated successfully', 'user' => $user], 200);
    }

    // Function to view the profile of the authenticated user
    public function viewProfile()
    {
        $user = Auth::user();

        // Check if the user exists
        if ($user) {
            return response()->json([
                'user_id' => $user->user_id,
                'username' => $user->username,
                'phone' => $user->phone,
                'role' => $user->role,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'identity_image' => $user->identity_image,
                'created_at' => $user->created_at,
                'average_rating' => $user->average_rating,
            ]);
        }

        // If user not found, return error message
        return response()->json(['message' => 'User not found'], 404);
    }
}
