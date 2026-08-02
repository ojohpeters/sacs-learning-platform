<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;

class CertificateVerificationController extends Controller
{
    /**
     * Public certificate verification — anyone with the code can confirm that
     * SACS Computers issued this certificate to this person for this course.
     */
    public function show(string $code)
    {
        $enrollment = Enrollment::with(['user', 'course'])
            ->where('certificate_code', $code)
            ->first();

        return view('certificate.verify', [
            'enrollment' => $enrollment,
            'code' => $code,
        ]);
    }
}
