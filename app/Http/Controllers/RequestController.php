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
     * Function to allow a blind user to create a new help request and notify nearby volunteers.
     */
    public function store(Request $request)
    {
        $blind = Auth::user(); // Retrieve the currently authenticated blind user

        // Validate latitude and longitude input
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90', // Latitude must be a valid numeric value within range
            'longitude' => 'required|numeric|between:-180,180', // Longitude must be a valid numeric value within range
        ]);

        // Convert coordinates into a text-based address using reverse geocoding
        $locationString = $this->reverseGeocode($request->latitude, $request->longitude);

        // Create a new help request
        $blindRequest = BlindRequest::create([
            'blind_id' => $blind->user_id, // Associate request with the blind user
            'blind_latitude' => $request->latitude, // Store latitude
            'blind_longitude' => $request->longitude, // Store longitude
            'blind_location' => $locationString, // Store reverse-geocoded location string
            'status' => 'pending', // Set the initial status to "pending"
            'created_at' => now() // Record the timestamp
        ]);

        // Retrieve all volunteers
        $volunteers = User::where('role', 'volunteer')->get();
        $matchingVolunteers = []; // Array to hold matching volunteers within a specific range

        foreach ($volunteers as $volunteer) {
            // Calculate the distance between the blind user and the volunteer
            $distance = $this->haversine(
                $request->latitude, $request->longitude,
                $volunteer->latitude, $volunteer->longitude
            );

            // Notify volunteers within a 5 km radius
            if ($distance <= 5) {
                $matchingVolunteers[] = $volunteer;

                // Create a notification for nearby volunteers
                Notification::create([
                    'volunteer_id' => $volunteer->user_id, // Associate notification with the volunteer
                    'request_id'   => $blindRequest->request_id, // Link to the blind request
                    'message'      => 'There is a request nearby from a blind person. Can you help?' // Notification message
                ]);
            }
        }

        // Return a response with details of the created request
        return response()->json([
            'message' => 'Request created, and nearby volunteers have been notified.',
            'request_id' => $blindRequest->request_id, // ID of the created request
            'matched_volunteers' => count($matchingVolunteers) // Count of volunteers notified
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

    /**
     * Function to get unread notifications for the currently authenticated volunteer.
     */
    public function notifications()
    {
        $volunteer = Auth::user(); // Retrieve the currently authenticated volunteer

        // Fetch unread notifications sorted by creation time
        $notifications = $volunteer->notifications()
            ->where('is_read', 0) // Filter for unread notifications
            ->orderBy('created_at', 'desc') // Sort by creation time in descending order
            ->get();

        return response()->json($notifications); // Return notifications as JSON response
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
            ->where('is_read', false) // Filter for unread notifications
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found or already read.'
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
        if ($blindRequest->created_at->diffInMinutes(now()) >= 10) {
            $blindRequest->update(['status' => 'expired']); // Mark the request as expired
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
