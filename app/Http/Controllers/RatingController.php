<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlindRequest;
use App\Models\Rating;
use App\Models\Certificate;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RatingController extends Controller
{
    // Function to allow a blind user to rate a volunteer
    public function rateVolunteer(Request $request)
    {
        // Validate the rating input
        $request->validate([
            'rating' => 'required|integer|min:0|max:5'
        ]);


        $blind = Auth::user();

        // Find the latest request by this blind user that hasn't been rated yet
        $lastRequest = BlindRequest::where('blind_id', $blind->user_id)
            ->whereNotNull('volunteer_id') // Ensure there is a volunteer associated with the request
            ->where('is_rated', false) // Look for unrated requests
            ->orderBy('accepted_at', 'desc') // Order by the accepted time
            ->first();

        // If no eligible request is found, return an error response
        if (!$lastRequest) {
            return response()->json(['message' => 'No completed request found for rating.'], 404);
        }

        // Check if this request has already been rated
        $existingRating = Rating::where('blind_id', $blind->user_id)
            ->where('request_id', $lastRequest->request_id)
            ->first();

        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this request.'], 400);
        }

        // Create a new rating entry
        Rating::create([
            'blind_id' => $blind->user_id,
            'volunteer_id' => $lastRequest->volunteer_id,
            'request_id' => $lastRequest->request_id,
            'rating' => $request->rating
        ]);

        // Mark the request as rated
        $lastRequest->is_rated = true;
        $lastRequest->save();


        // Update average rating
        $ratings = Rating::where('volunteer_id', $lastRequest->volunteer_id)->get();
        $sum = $ratings->sum('rating');
        $count = $ratings->count();
        $average = $count > 0 ? $sum / $count : 0;

        User::where('user_id', $lastRequest->volunteer_id)->update([
            'average_rating' => $average
        ]);

        // Check if the volunteer already has a certificate
        $existingCertificate = Certificate::where('volunteer_id', $lastRequest->volunteer_id)->first();

        if (!$existingCertificate) {
            // Calculate the number of completed requests
            $completedRequests = BlindRequest::where('volunteer_id', $lastRequest->volunteer_id)
                ->where('status', 'accepted') // Count only accepted/completed requests
                ->count();

            // Aggregate the ratings for the volunteer
            $ratings = Rating::where('volunteer_id', $lastRequest->volunteer_id)->get();
            $sum = $ratings->sum('rating'); // Sum of ratings
            $count = $ratings->count(); // Total number of ratings

            // Check criteria (e.g., at least 2 completed requests and a total rating of 5 or more)
            if ($completedRequests >= 2 && $sum >= 5) {
                // Generate a Helper certificate for the volunteer
                $pdfFile = $this->generateCertificatePDF($lastRequest->volunteer_id, 'helper');

                // Save the certificate information
                Certificate::create([
                    'volunteer_id' => $lastRequest->volunteer_id,
                    'certificate_type' => 'helper',
                    'certificate_file' => $pdfFile
                ]);

                // Send a notification to the volunteer
                Notification::create([
                    'volunteer_id' => $lastRequest->volunteer_id,
                    'type' => 'certificate',
                    'message' => "Congratulations! You have earned the Helper certificate. You can download it from the app."
                ]);
            }
        }

        // Return a success message
        return response()->json(['message' => 'Rating submitted successfully.']);
    }

    // Function to generate and store a certificate PDF
    private function generateCertificatePDF($volunteerId, $type)
    {
        // Retrieve volunteer information
        $volunteer = User::find($volunteerId);

        // Load the PDF view and pass data to it
        $pdf = Pdf::loadView('pdf.certificate', [
            'volunteer' => $volunteer,
            'certificate_type' => $type,
            'date' => now()->format('Y-m-d') // Format the current date
        ]);

        // Define the file name and save path
        $fileName = $volunteerId . '_' . $type . '_' . time() . '.pdf';
        $path = public_path('storage/certificates/');

        // Create the directory if it doesn't exist
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Save the generated PDF file
        $pdf->save($path . $fileName);
        return asset('storage/certificates/' . $fileName); // Return the file URL
    }
    /*
    public function downloadCertificate()
    {
        // Get the currently authenticated volunteer
        $volunteer = Auth::user();

        // Retrieve the volunteer's certificate
        $certificate = Certificate::where('volunteer_id', $volunteer->user_id)->first();

        // If no certificate is found, return an error response
        if (!$certificate) {
            return response()->json(['message' => 'You have not received a certificate yet.'], 404);
        }

        // Mark the related notification as read
        Notification::where('volunteer_id', $volunteer->user_id)
            ->where('type', 'certificate')
            ->where('is_read', false) // Only update if it's unread
            ->update(['is_read' => true]);

        // Return the certificate download URL
        return response()->json([
            'message' => 'Certificate download link:',
            'certificate_url' => $certificate->certificate_file
        ]);
    }*/
}
