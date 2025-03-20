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
    public function rateVolunteer(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:0|max:5'
        ]);

        $blind = Auth::user();

        // الحصول على آخر طلب تمت مساعدته
        $lastRequest = BlindRequest::where('blind_id', $blind->user_id)
            ->whereNotNull('volunteer_id')
            ->orderBy('accepted_at', 'desc')
            ->first();

        if (!$lastRequest) {
            return response()->json(['message' => 'No completed request found for rating.'], 404);
        }

        // التأكد إذا كانت الـ request قد تم تقييمها مسبقًا
        if ($lastRequest->is_rated) {
            return response()->json(['message' => 'This request has already been rated.'], 400);
        }

        // تسجيل التقييم
        Rating::create([
            'blind_id' => $blind->user_id,
            'volunteer_id' => $lastRequest->volunteer_id,
            'request_id' => $lastRequest->request_id,
            'rating' => $request->rating
        ]);

        // تحديث حالة التقييم في جدول requests
        $lastRequest->is_rated = true;
        $lastRequest->save();

        // حساب مجموع التقييمات وعددها
        $volunteerRatings = Rating::where('volunteer_id', $lastRequest->volunteer_id)->get();
        $count = $volunteerRatings->count();
        $sum = $volunteerRatings->sum('rating');

        // التحقق إذا كان يستحق شهادة
        if ($count >= 2 && $sum >= 7) { // مثال: حصل على 5 تقييمات بإجمالي 20 نقطة أو أكثر
            $type = 'helper';
            if ($sum >= 30) $type = 'supporter';
            if ($sum >= 40) $type = 'champion';
            if ($sum >= 50) $type = 'legend';

            // توليد شهادة PDF (مكان توليدها يعتمد عليك، ممكن use DomPDF)
            $pdfFile = $this->generateCertificatePDF($lastRequest->volunteer_id, $type);

            // حفظ الشهادة
            Certificate::create([
                'volunteer_id' => $lastRequest->volunteer_id,
                'certificate_type' => $type,
                'certificate_file' => $pdfFile
            ]);

            // إرسال إشعار
            Notification::create([
                'volunteer_id' => $lastRequest->volunteer_id,
                'message' => "Congratulations! You have been awarded a $type certificate. Download it from your app."
            ]);
        }

        return response()->json(['message' => 'Rating submitted successfully.']);
    }



    private function generateCertificatePDF($volunteerId, $type)
    {
        $volunteer = User::find($volunteerId);
        $pdf = Pdf::loadView('pdf.certificate', [
            'volunteer' => $volunteer,
            'certificate_type' => $type,
            'date' => now()->format('Y-m-d')
        ]);

        $fileName = 'certificates/' . $volunteerId . '_' . $type . '_' . time() . '.pdf';
        Storage::put($fileName, $pdf->output());
        return Storage::url($fileName); // هذا بيرجع لك رابط قابل للوصول من Flutter
    }
}


