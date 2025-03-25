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
        // تحقق من صحة التقييم
        $request->validate([
            'rating' => 'required|integer|min:0|max:5'
        ]);

        // الحصول على المستخدم (الأعمى) الحالي
        $blind = Auth::user();

        // البحث عن أحدث طلب (غير تم تقييمه بعد)
        $lastRequest = BlindRequest::where('blind_id', $blind->user_id)
            ->whereNotNull('volunteer_id')
            ->where('is_rated', false) // نبحث فقط عن الطلبات التي لم يتم تقييمها بعد
            ->orderBy('accepted_at', 'desc')
            ->first();

        if (!$lastRequest) {
            return response()->json(['message' => 'No completed request found for rating.'], 404);
        }

        // تحقق إذا كان هذا الطلب قد تم تقييمه من قبل
        $existingRating = Rating::where('blind_id', $blind->user_id)
            ->where('request_id', $lastRequest->request_id)
            ->first();

        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this request.'], 400);
        }

        // إنشاء التقييم الجديد
        Rating::create([
            'blind_id' => $blind->user_id,
            'volunteer_id' => $lastRequest->volunteer_id,
            'request_id' => $lastRequest->request_id,
            'rating' => $request->rating
        ]);

        // تحديث حالة الطلب ليصبح "تم تقييمه"
        $lastRequest->is_rated = true;
        $lastRequest->save();

        // تحقق إذا كان المتطوع لديه شهادة من قبل
        $existingCertificate = Certificate::where('volunteer_id', $lastRequest->volunteer_id)->first();

        if (!$existingCertificate) {
            // احسب عدد الطلبات المكتملة
            $completedRequests = BlindRequest::where('volunteer_id', $lastRequest->volunteer_id)
                ->where('status', 'accepted')
                ->count();

            // اجمع التقييمات لهذا المتطوع
            $ratings = Rating::where('volunteer_id', $lastRequest->volunteer_id)->get();
            $sum = $ratings->sum('rating');
            $count = $ratings->count();

            // تحقق من الشروط: على سبيل المثال، 2 طلبات مكتملة ومجموع التقييمات فوق 5
            if ($completedRequests >= 2 && $sum >= 5) {
                // إنشاء شهادة Helper
                $pdfFile = $this->generateCertificatePDF($lastRequest->volunteer_id, 'helper');

                Certificate::create([
                    'volunteer_id' => $lastRequest->volunteer_id,
                    'certificate_type' => 'helper',
                    'certificate_file' => $pdfFile
                ]);

                // إرسال إشعار للمتطوع
                Notification::create([
                    'volunteer_id' => $lastRequest->volunteer_id,
                    'message' => "تهانينا! لقد حصلت على شهادة Helper. يمكنك تحميلها من التطبيق."
                ]);
            }
        }

        // إرجاع رسالة تأكيد
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



    public function downloadCertificate()
{
    $volunteer = Auth::user();

    $certificate = Certificate::where('volunteer_id', $volunteer->user_id)->first();

    if (!$certificate) {
        return response()->json(['message' => 'لم تحصل على شهادة بعد.'], 404);
    }

    return response()->json([
        'message' => 'رابط تحميل الشهادة:',
        'certificate_url' => $certificate->certificate_file
    ]);
}

}
