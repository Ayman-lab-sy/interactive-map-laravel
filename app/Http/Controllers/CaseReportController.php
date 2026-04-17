<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Reports\CaseReportGenerator;
use App\Models\CaseModel; // عدّل الاسم حسب موديلك
use Illuminate\Support\Facades\Auth;

class CaseReportController extends Controller
{
    /**
     * Generate human rights case report (PDF)
     */
    public function generate(Request $request, $caseId)
    {
        // 1️⃣ جلب الحالة
        $case = CaseModel::findOrFail($caseId);

        // 2️⃣ تحقق من حالة Lifecycle
        abort_unless($case->status === 'ready_for_export', 403, 'Case is not ready for export.');

        // 3️⃣ تحقق من الصلاحية
        $user = Auth::user();
        abort_unless($user->role_id === 1, 403, 'Unauthorized action.');

        // 4️⃣ إعداد Generator
        $generator = app(CaseReportGenerator::class);

        // 5️⃣ توليد النسخة العربية
        $reportAR = $generator->generate(
            case: $case,
            user: $user,
            language: 'AR',
            includeIdentity: false
        );

        // 6️⃣ توليد النسخة الإنكليزية
        $reportEN = $generator->generate(
            case: $case,
            user: $user,
            language: 'EN',
            includeIdentity: false
        );

        // 7️⃣ عرض المعاينة (Preview)
        return view('admin.cases.export', [
            'case'      => $case,
            'report_ar' => $reportAR['html'],
            'report_en' => $reportEN['html'],
        ]);
    }

    public function downloadPdf(Request $request, $id)
    {
        abort_unless(Auth::user()->canExport(), 403);

        $case = CaseModel::findOrFail($id);
        abort_unless($case->status === 'ready_for_export', 409);

        $lang = $request->get('lang', 'AR');

        $generator = app(\App\Services\Reports\CaseReportGenerator::class);
        $result = $generator->generate(
            case: $case,
            user: Auth::user(),
            language: $lang,
            includeIdentity: false
        );

        $pdfService = app(\App\Services\Pdf\GotenbergPdfService::class);
        $pdfBinary = $pdfService->generateFromHtml($result['html']);

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="case-'.$case->case_number.'-'.$lang.'.pdf"'
            );
    }
}
