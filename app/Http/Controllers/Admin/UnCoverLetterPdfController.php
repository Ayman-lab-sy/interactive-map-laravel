<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\UnCoverLetterGenerator;
use App\Services\Pdf\GotenbergPdfService;
use Illuminate\Http\Request;

class UnCoverLetterPdfController extends Controller
{
    public function download(Request $request, int $referralId)
    {
        $generator = app(UnCoverLetterGenerator::class);
        $result = $generator->generate($referralId);

        $pdfService = app(GotenbergPdfService::class);
        $pdfBinary = $pdfService->generateFromHtml($result['html']);

        $fileName = 'UN-Cover-Letter-Referral-' . $referralId . '.pdf';

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }
}
