<?php

namespace App\Http\Controllers;

use App\Models\BlindRequest;
use App\Models\User;
use App\Models\Certificate;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Function to allow a blind user to create a new help request and notify nearby volunteers.
     */
    /**
     * Function to allow a blind user to create a new help request and notify nearby volunteers.
     */
    public function store(Request $request)
    {
        $blind = Auth::user(); // Retrieve the currently authenticated blind user

        // Validate incoming request
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'text_request' => 'nullable|string|max:1000', // Validate text request if present
        ]);

        // Convert coordinates into a text-based address using reverse geocoding
        $locationString = $this->reverseGeocode($request->latitude, $request->longitude);

        // Create a new help request
        $blindRequest = BlindRequest::create([
            'blind_id'        => $blind->user_id,
            'blind_latitude'  => $request->latitude,
            'blind_longitude' => $request->longitude,
            'blind_location'  => $locationString,
            'status'          => 'pending',
            'text_request'    => $request->input('text_request'), // Add the text request
            'created_at'      => now()
        ]);

        // Retrieve all volunteers
        $volunteers = User::where('role', 'volunteer')->get();
        $matchingVolunteers = [];

        foreach ($volunteers as $volunteer) {
            $distance = $this->haversine(
                $request->latitude,
                $request->longitude,
                $volunteer->latitude,
                $volunteer->longitude
            );

            if ($distance <= 5) {
                $matchingVolunteers[] = $volunteer;

                // Create personalized notification message
                $message = 'Request from ' . $blind->username;
                $message .= '. Can you help?';

                // Create the notification
                Notification::create([
                    'volunteer_id' => $volunteer->user_id,
                    'request_id'   => $blindRequest->request_id,
                    'message'      => $message,
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
     * Function to reverse geocode latitude and longitude into a textual address.
     */
    private function reverseGeocode($lat, $lon)
    {
        // Use Nominatim API for reverse geocoding
        $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=$lat&lon=$lon";
        $opts = [
            "http" => [
                "header" => "User-Agent: MyApp/1.0\r\n" // Custom user-agent header
            ]
        ];
        $context = stream_context_create($opts); // Create the HTTP context
        $response = file_get_contents($url, false, $context); // Fetch the API response
        $data = json_decode($response); // Decode the JSON response

        // Return the location name or "Unknown location" if not found
        return $data->display_name ?? "Unknown location";
    }

    /**
     * Function to calculate the distance between two coordinates using the Haversine formula.
     */
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        $dLat = deg2rad($lat2 - $lat1); // Difference in latitude in radians
        $dLon = deg2rad($lon2 - $lon1); // Difference in longitude in radians

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); // Haversine formula calculation
        return $earthRadius * $c; // Distance in kilometers
    }


    /*
    Fetch all notifications sorted by creation time (newest first)
*/
    public function notifications()
    {
        $volunteer = Auth::user(); // Retrieve the authenticated volunteer

        // Fetch notifications with related request and blind user
        $notifications = Notification::with('blindRequest.blinds')
            ->where('is_read', 0)
            ->where(function ($query) use ($volunteer) {
                $query->where('volunteer_id', $volunteer->user_id)
                    ->orWhere('type', 'admin');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the separated data
        $formatted = $notifications->map(function ($notification) {
            $formattedNotification = [
                'notification_id' => $notification->notification_id,
                'volunteer_id'    => $notification->volunteer_id,
                'request_id'      => $notification->request_id,
                'message'         => $notification->message,
                'text_request'    => optional($notification->blindRequest)->text_request,
                'blind_username'  => optional(optional($notification->blindRequest)->blinds)->username,
                'type'            => $notification->type,
                'is_read'         => $notification->is_read,
                'created_at'      => $notification->created_at,
            ];

            // If the notification type is 'certificate', add the certificate URL
            if ($notification->type === 'certificate') {
                $certificate = Certificate::where('volunteer_id', $notification->volunteer_id)->first();
                $formattedNotification['certificate_url'] = $certificate ? $certificate->certificate_file : null;
            }

            return $formattedNotification;
        });

        return response()->json($formatted);
    }




    /**
     * Function to handle notification click and process associated blind request.
     */
    public function handleNotificationClick($notificationId)
    {
        $volunteer = Auth::user(); // Retrieve the currently authenticated volunteer

        // Find the unread notification by ID
        $notification = $volunteer->notifications()
            ->where('notification_id', $notificationId) // Match notification ID
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        // Mark the notification as read
        $notification->update(['is_read' => true]);

        // Fetch the associated blind request
        $blindRequest = BlindRequest::where('request_id', $notification->request_id)
            ->where('status', 'pending') // Ensure the request status is "pending"
            ->first();

        if (!$blindRequest) {
            return response()->json([
                'message' => 'Request not found or has already been accepted.'
            ], 404);
        }

        // Check if the request has expired (e.g., created more than 10 minutes ago)
        if ($blindRequest->created_at->diffInMinutes(now()) >= 120) { //120 just for testing ,it should be edit
            $blindRequest->update(['status' => 'expired']);
            return response()->json([
                'message' => 'This request has expired and cannot be accepted.'
            ], 400);
        }

        // Accept the request
        $blindRequest->update([
            'volunteer_id' => $volunteer->user_id, // Associate the volunteer with the request
            'status' => 'accepted', // Update the status to "accepted"
            'accepted_at' => now(), // Record the acceptance timestamp
        ]);

        $blind = $blindRequest->blinds; // Fetch blind user's information

        // Return details of the accepted request
        return response()->json([
            'message' => 'Request accepted via notification.',
            'blind_phone' => $blind->phone, // Blind user's phone number
            'blind_location' => $blindRequest->blind_location // Blind user's location
        ]);
    }
}
