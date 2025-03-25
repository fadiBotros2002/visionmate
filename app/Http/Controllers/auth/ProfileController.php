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
        $user = Auth::user();
        $input = $request->all();

        if (isset($input['username'])) {
            $request->validate(['username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id']);
            $user->username = $input['username'];
        }

        if (isset($input['phone'])) {
            $request->validate(['phone' => 'required|string|max:20|unique:users,phone,' . $user->user_id . ',user_id']);
            $user->phone = $input['phone'];
        }

        if (isset($input['latitude'])) {
            $request->validate(['latitude' => 'required|numeric|between:-90,90']);
            $user->latitude = $input['latitude'];
        }

        if (isset($input['longitude'])) {
            $request->validate(['longitude' => 'required|numeric|between:-180,180']);
            $user->longitude = $input['longitude'];
        }

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

        $user->save();

        return response()->json(['message' => 'User info updated successfully', 'user' => $user], 200);
    }

}
