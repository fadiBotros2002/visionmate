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

        // تحويل الإحداثيات إلى عنوان نصي
        $locationString = $this->reverseGeocode($request->latitude, $request->longitude);

        $blindRequest = BlindRequest::create([
            'blind_id' => $blind->user_id,
            'blind_latitude' => $request->latitude,
            'blind_longitude' => $request->longitude,
            'blind_location' => $locationString,
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

                // إضافة request_id عند إنشاء الإشعار
                Notification::create([
                    'volunteer_id' => $volunteer->user_id,
                    'request_id'   => $blindRequest->request_id,
                    'message'      => 'There is a request nearby from a blind person. Can you help?'
                ]);
            }
        }

        return response()->json([
            'message' => 'Request created, and nearby volunteers have been notified.',
            'request_id' => $blindRequest->request_id,
            'matched_volunteers' => count($matchingVolunteers)
        ]);
    }

    /**
     * Reverse geocode using Nominatim.
     */
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

        return $data->display_name ?? "Unknown location";
    }

    /**
     * Haversine formula for distance calculation.
     */
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Volunteer accepts a request.
     */
    public function acceptRequest($id)
    {
        $volunteer = Auth::user();

        $blindRequest = BlindRequest::where('request_id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$blindRequest) {
            return response()->json([
                'message' => 'Request not found or has already been accepted.',
            ], 404);
        }

        $blindRequest->update([
            'volunteer_id' => $volunteer->user_id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $blind = $blindRequest->blinds;

        return response()->json([
            'message' => 'Request accepted.',
            'blind_phone' => $blind->phone,
            'blind_location' => $blindRequest->blind_location
        ]);
    }

    /**
     * Get unread notifications for the volunteer.
     */
    public function notifications()
    {
        $volunteer = Auth::user();

        $notifications = $volunteer->notifications()
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
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

    /**
     * Get all pending requests related to volunteer notifications.
     */
    public function getPendingRequestsForVolunteer()
    {
        $volunteer = Auth::user();

        $pendingRequestIds = Notification::where('volunteer_id', $volunteer->user_id)
            ->where('is_read', false)
            ->pluck('request_id')
            ->unique();

        $pendingRequests = BlindRequest::whereIn('request_id', $pendingRequestIds)
            ->where('status', 'pending')
            ->get();

        return response()->json($pendingRequests);
    }
}
