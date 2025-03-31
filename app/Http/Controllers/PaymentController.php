<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function processDonation(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));


        try {
            $charge = Charge::create([
                "amount" => $request->amount * 100, // تحويل إلى سنتات
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Donation to VisionMate"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your donation!',
                'charge' => $charge
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}
