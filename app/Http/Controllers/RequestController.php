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

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // تحويل الإحداثيات إلى عنوان نصي باستخدام Nominatim
        $locationString = $this->reverseGeocode($request->latitude, $request->longitude);

        $blindRequest = BlindRequest::create([
            'blind_id' => $blind->user_id,
            'blind_latitude' => $request->latitude,
            'blind_longitude' => $request->longitude,
            'blind_location' => $locationString, // تخزين النص
            'status' => 'pending'
        ]);

        $volunteers = User::where('role', 'volunteer')->get();
        $matchingVolunteers = [];

        foreach ($volunteers as $volunteer) {
            $distance = $this->haversine(
                $request->latitude, $request->longitude,
                $volunteer->latitude, $volunteer->longitude
            );

            if ($distance <= 5) {
                $matchingVolunteers[] = $volunteer;

                Notification::create([
                    'volunteer_id' => $volunteer->user_id,
                    'message' => 'There is a request nearby from a blind person. Can you help?'
                ]);
            }
        }

        return response()->json([
            'message' => 'Request created, and nearby volunteers have been notified.',
            'request_id' => $blindRequest->request_id,
            'matched_volunteers' => count($matchingVolunteers)
        ]);
    }

    // دالة تحويل الإحداثيات إلى نص باستخدام Nominatim (مجاني)
    private function reverseGeocode($lat, $lon)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=$lat&lon=$lon";

        $opts = [
            "http" => [
                "header" => "User-Agent: MyApp/1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        $response = file_get_contents($url, false, $context);
        $data = json_decode($response);

        return $data->display_name ?? "Unknown location";  // إرجاع الموقع النصي
    }

    // دالة حساب المسافة
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // المسافة بالكيلومترات
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
