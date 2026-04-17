<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\Pdf\GotenbergPdfService;
use Illuminate\Support\Facades\View;

class UnCoverLetterController extends Controller
{
    public function generate(Request $request, int $referralId)
    {
        // جلب الإحالة
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id as referral_id',
                'r.referral_status',
                'r.special_procedure_type',
                'c.case_number',
                'e.entity_name',
                'e.referral_track',
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);

        // تحقق المسار
        abort_unless(
            $referral->referral_track === 'SPECIAL_PROCEDURES',
            403,
            'Not a UN Special Procedures referral'
        );

        // تحقق نوع الإجراء الخاص
        abort_if(
            empty($referral->special_procedure_type),
            500,
            'Special Procedure Type missing'
        );

        // اختيار التيمبلت
        $view = match ($referral->special_procedure_type) {
            'TORTURE' =>
                'reports.templates.un.cover-letters.torture',

            'ARBITRARY_DETENTION' =>
                'reports.templates.un.cover-letters.arbitrary-detention',

            'ENFORCED_DISAPPEARANCE' =>
                'reports.templates.un.cover-letters.enforced-disappearance',

            'FREEDOM_OF_EXPRESSION' =>
                'reports.templates.un.cover-letters.freedom-expression',

            'HUMAN_RIGHTS_DEFENDERS' =>
                'reports.templates.un.cover-letters.human-rights-defenders',
            
            'EXTRAJUDICIAL_EXECUTIONS' =>
                'reports.templates.un.cover-letters.extrajudicial-executions',

            'VIOLENCE_AGAINST_WOMEN' =>
                'reports.templates.un.cover-letters.violence-against-women',

            'MINORITY_ISSUES' =>
                'reports.templates.un.cover-letters.minority-issues',

            'FREEDOM_OF_RELIGION' =>
                'reports.templates.un.cover-letters.freedom-of-religion',
                
            default =>
                abort(500, 'Unsupported UN Special Procedure'),
        };

        // بناء البيانات
        $html = View::make($view, [
            'case_number'  => $referral->case_number,
            'entity_name'  => $referral->entity_name,
            'report_date'  => \Carbon\Carbon::now()->format('d F Y'),
        ])->render();

        // توليد PDF
        $pdfService = app(GotenbergPdfService::class);
        $pdfBinary = $pdfService->generateFromHtml($html);

        // تسجيل لوج
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referral->referral_id,
            'action' => 'un_cover_letter_generated',
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);

        $fileName = 'Cover-Letter-' . $referral->case_number . '.pdf';

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }
}
