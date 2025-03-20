<?php

namespace App\Http\Controllers;

use App\Models\BlindRequest;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Blind user creates a new help request and notifies nearby volunteers.
     */
    public function store(Request $request)
    {
        $blind = Auth::user();

        $blindRequest = BlindRequest::create([
            'blind_id' => $blind->user_id,
            'blind_location' => $request->location,
            'status' => 'pending'
        ]);

        $inputLocation = strtolower($request->location);

        $volunteers = User::where('role', 'volunteer')->get();

        $matchingVolunteers = [];

        foreach ($volunteers as $volunteer) {
            $volunteerLocation = strtolower($volunteer->location);
            $distance = levenshtein($inputLocation, $volunteerLocation);


            if ($distance <= 3) {
                $matchingVolunteers[] = $volunteer;


                Notification::create([
                    'volunteer_id' => $volunteer->user_id,
                    'message' => 'There is a request in your area from a blind person. Can you help?'
                ]);
            }
        }

        return response()->json([
            'message' => 'Request created, and flexible search found volunteers who have been notified.',
            'request_id' => $blindRequest->request_id,
            'matched_volunteers' => count($matchingVolunteers)
        ]);
    }

    public function acceptRequest($id)
    {
        $volunteer = Auth::user();

        // Retrieve the pending request
        $blindRequest = BlindRequest::where('request_id', $id)
            ->where('status', 'pending')  // Check if the status is still 'pending'
            ->first();

        // If no request is found or if the request is already accepted, return an error message
        if (!$blindRequest) {
            return response()->json([
                'message' => 'Request not found or has already been accepted.',
            ], 404);  // 404 if request is not found or already accepted
        }

        // Update the request with the volunteer details
        $blindRequest->update([
            'volunteer_id' => $volunteer->user_id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Get the blind person associated with the request
        $blind = $blindRequest->blinds;

        // Return the response with blind's phone and location
        return response()->json([
            'message' => 'Request accepted.',
            'blind_phone' => $blind->phone,
            'blind_location' => $blindRequest->blind_location
        ]);
    }


    /**
     * Get notifications for the volunteer.
     */
    public function notifications()
    {
        $volunteer = Auth::user();  // Get the authenticated volunteer

        // Fetch only unread notifications
        $notifications = $volunteer->notifications()
            ->where('is_read', 0)  // Add filter to get only unread notifications
            ->orderBy('created_at', 'desc')  // Sort by creation date (newest first)
            ->get();  // Execute the query and get the results

        return response()->json($notifications);  // Return the notifications as JSON
    }


    /**
     * Mark notification as read.
     */
    public function markAsRead($notificationId)
    {
        $volunteer = Auth::user();

        $notification = $volunteer->notifications()
            ->where('notification_id', $notificationId)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notification marked as read.'
        ]);
    }
}
