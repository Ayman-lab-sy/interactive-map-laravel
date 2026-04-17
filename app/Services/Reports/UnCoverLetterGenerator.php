<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class UnCoverLetterGenerator
{
    public function generate(int $referralId): array
    {
        // 1) جلب الإحالة + الحالة + الجهة
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id as referral_id',
                'r.referral_track',
                'r.special_procedure_type',
                'c.case_number',
                'e.entity_name',
            ])
            ->where('r.id', $referralId)
            ->first();

        if (!$referral) {
            throw new \RuntimeException('Referral not found');
        }

        // 2) تحديد الـ Mandate حسب نوع الإجراء الخاص
        $mandateTitle = match ($referral->special_procedure_type) {
            'TORTURE' =>
                'Special Rapporteur on torture and other cruel, inhuman or degrading treatment or punishment',

            'ENFORCED_DISAPPEARANCE' =>
                'Working Group on Enforced or Involuntary Disappearances',

            'ARBITRARY_DETENTION' =>
                'Working Group on Arbitrary Detention',

            'FREEDOM_OF_EXPRESSION' =>
                'Special Rapporteur on the promotion and protection of the right to freedom of opinion and expression',

            'HUMAN_RIGHTS_DEFENDERS' =>
                'Special Rapporteur on the situation of human rights defenders',
            
            'EXTRAJUDICIAL_EXECUTIONS' =>
                'Special Rapporteur on extrajudicial, summary or arbitrary executions',

            'VIOLENCE_AGAINST_WOMEN' =>
                'Special Rapporteur on violence against women and girls, its causes and consequences',

            'MINORITY_ISSUES' =>
                'Special Rapporteur on minority issues',

            'FREEDOM_OF_RELIGION' =>
                'Special Rapporteur on freedom of religion or belief',

            default =>
                throw new \RuntimeException('Unsupported UN Special Procedure for cover letter'),
        };

        // 3) المتغيرات المضمونة للتيمبل (لا شيء ضمن Blade بدون تعريف هنا)
        $placeholders = [
            'case_number'   => $referral->case_number,
            'entity_name'   => $referral->entity_name,
            'mandate_title' => $mandateTitle,
            'report_date' => \Carbon\Carbon::now()->format('d F Y'),
        ];

        // 4) اختيار التيمبل
        $viewName = match ($referral->special_procedure_type) {
            'TORTURE' =>
                'reports.templates.un.cover-letters.torture',

            'ENFORCED_DISAPPEARANCE' =>
                'reports.templates.un.cover-letters.enforced-disappearance',

            'ARBITRARY_DETENTION' =>
                'reports.templates.un.cover-letters.arbitrary-detention',

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
                throw new \RuntimeException('Unsupported cover letter template'),
        };

        // 5) توليد HTML
        $html = View::make($viewName, $placeholders)->render();

        return [
            'html' => $html,
        ];
    }
}
