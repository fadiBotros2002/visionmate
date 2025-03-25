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
        // Validate the incoming rating request
        $request->validate([
            'rating' => 'required|integer|min:0|max:5'
        ]);

        // Get the authenticated blind user
        $blind = Auth::user();

        // Get the latest request that was accepted and completed by a volunteer
        $lastRequest = BlindRequest::where('blind_id', $blind->user_id)
            ->whereNotNull('volunteer_id')
            ->orderBy('accepted_at', 'desc')
            ->first();

        // If no completed request is found, return an error message
        if (!$lastRequest) {
            return response()->json(['message' => 'No completed request found for rating.'], 404);
        }

        // Check if the request has already been rated
        if ($lastRequest->is_rated) {
            return response()->json(['message' => 'This request has already been rated.'], 400);
        }

        // Store the rating in the database
        Rating::create([
            'blind_id' => $blind->user_id,
            'volunteer_id' => $lastRequest->volunteer_id,
            'request_id' => $lastRequest->request_id,
            'rating' => $request->rating
        ]);

        // Mark the request as rated
        $lastRequest->is_rated = true;
        $lastRequest->save();

        // Calculate the sum and count of all ratings for that volunteer
        $volunteerRatings = Rating::where('volunteer_id', $lastRequest->volunteer_id)->get();
        $count = $volunteerRatings->count();
        $sum = $volunteerRatings->sum('rating');

        // Check if the volunteer qualifies for a certificate
        if ($count >= 2 && $sum >= 7) {
            // Define certificate type based on total points
            $type = 'helper';
            if ($sum >= 30) $type = 'supporter';
            if ($sum >= 40) $type = 'champion';
            if ($sum >= 50) $type = 'legend';

            // Generate a PDF certificate
            $pdfFile = $this->generateCertificatePDF($lastRequest->volunteer_id, $type);

            // Store the certificate record in the database
            Certificate::create([
                'volunteer_id' => $lastRequest->volunteer_id,
                'certificate_type' => $type,
                'certificate_file' => $pdfFile
            ]);

            // Send a notification to the volunteer
            Notification::create([
                'volunteer_id' => $lastRequest->volunteer_id,
                'message' => "Congratulations! You have been awarded a $type certificate. Download it from your app."
            ]);
        }

        // Return success message
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
            'date' => now()->format('Y-m-d')
        ]);

        $fileName = $volunteerId . '_' . $type . '_' . time() . '.pdf';
        $path = public_path('storage/certificates/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $pdf->save($path . $fileName);
        return asset('storage/certificates/' . $fileName);

    }
}
