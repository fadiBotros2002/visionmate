<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{


    public function updateUserInfo(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        $input = $request->all(); // Retrieve all inputs from the request

        // Update `username` if provided
        if (isset($input['username'])) {
            $request->validate(['username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id']);
            $user->username = $input['username'];
        }

        // Update `phone` if provided
        if (isset($input['phone'])) {
            $request->validate(['phone' => 'required|string|max:20|unique:users,phone,' . $user->user_id . ',user_id']);
            $user->phone = $input['phone'];
        }

        // Update `location` if provided
        if (isset($input['location'])) {
            $request->validate(['location' => 'nullable|string|max:255']);
            $user->location = $input['location'];
        }

        // Update `password` if provided
        if (isset($input['new_password'])) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if (!Hash::check($input['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 400);
            }

            $user->password = bcrypt($input['new_password']);
        }

        // Save the updated user info
        $user->save();

        return response()->json(['message' => 'User info updated successfully', 'user' => $user], 200);
    }
}
