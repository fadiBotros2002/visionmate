<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function processDonation(Request $request)
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create a Stripe charge
            $charge = Charge::create([
                "amount" => $request->amount * 100, // Amount in cents
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Donation to VisionMate"
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your donation!',
                'charge' => $charge
            ], 200);

        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}
