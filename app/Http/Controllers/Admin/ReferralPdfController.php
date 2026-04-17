<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Reports\CaseReportGenerator;
use App\Services\Pdf\GotenbergPdfService;

class ReferralPdfController extends Controller
{
    public function download(Request $request, $id)
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
                'r.humanitarian_type',
                'r.un_accountability_type',
                'c.id as case_id',
                'c.case_number',
                'e.entity_name',
                'e.referral_track',
            ])

            ->where('r.id', $id)
            ->first();

        abort_if(!$referral, 404);

        /*
        |--------------------------------------------------------------------------
        | 🔒 Humanitarian Protection – Hard Gate
        |--------------------------------------------------------------------------
        */
        if ($referral->referral_track === 'HUMANITARIAN_PROTECTION') {

            if (!in_array($referral->humanitarian_type, ['ICRC', 'UNHCR'])) {
                abort(500, 'Unsupported humanitarian referral type');
            }
        }        

        abort_unless(
            in_array($referral->referral_status, ['generated','exported']),
            403
        );

        // تحقق من متطلبات ما قبل التحميل حسب نوع الإحالة
        if (
            $referral->referral_track === 'NGO_LEGAL'
            && $referral->entity_name !== 'Amnesty International'
        ) {


            $hasNarrative = DB::connection('cases')
                ->table('case_referral_narratives')
                ->where('referral_id', $referral->referral_id)
                ->where('language', 'en')
                ->exists();

            if (!$hasNarrative) {
                return redirect()
                    ->back()
                    ->withErrors('لا يمكن تحميل التقرير قبل إدخال الصياغة القانونية باللغة الإنكليزية.');
            }

        }
        /*
        |--------------------------------------------------------------------------
        | Humanitarian Protection – ICRC (PDF Gate)
        |--------------------------------------------------------------------------
        */
        if (
            $referral->referral_track === 'HUMANITARIAN_PROTECTION'
            && $referral->humanitarian_type === 'ICRC'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_humanitarian_icrc')
                ->where('referral_id', $referral->referral_id)
                ->first();

            if (
                empty($data?->source_account_en) ||
                empty($data?->general_location_en) ||
                empty($data?->incident_timeframe_en) ||
                empty($data?->humanitarian_needs_en) ||
                empty($data?->immediate_risks_en) ||
                empty($data?->mandate_relevance_en) ||
                empty($data?->assistance_requested_en)
            ) {
                return back()->withErrors(
                    'لا يمكن تحميل PDF قبل استكمال جميع الحقول الإنسانية المطلوبة لإحالة ICRC.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Humanitarian Protection – UNHCR (PDF Gate)
        |--------------------------------------------------------------------------
        */
        if (
            $referral->referral_track === 'HUMANITARIAN_PROTECTION'
            && $referral->humanitarian_type === 'UNHCR'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_humanitarian_unhcr')
                ->where('referral_id', $referral->referral_id)
                ->first();

            if (
                empty($data?->source_account_en) ||
                empty($data?->general_location_en) ||
                empty($data?->incident_timeframe_en) ||
                empty($data?->humanitarian_needs_en) ||
                empty($data?->immediate_risks_en) ||
                empty($data?->mandate_relevance_en) ||
                empty($data?->assistance_requested_en)
            ) {
                return back()->withErrors(
                    'لا يمكن تحميل PDF قبل استكمال جميع الحقول الإنسانية المطلوبة لإحالة UNHCR.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UN Accountability – OHCHR (PDF Gate)
        |--------------------------------------------------------------------------
        */
        if (
            $referral->referral_track === 'UN_ACCOUNTABILITY'
            && $referral->un_accountability_type === 'OHCHR'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_un_accountability_ohchr')
                ->where('referral_id', $referral->referral_id)
                ->first();

            if (
                empty($data?->source_context_en) ||
                empty($data?->methodology_note_en) ||
                empty($data?->general_location_en) ||
                empty($data?->incident_timeframe_en) ||
                empty($data?->documented_information_en) ||
                empty($data?->identified_concerns_en) ||
                empty($data?->mandate_relevance_en)
            ) {
                return back()->withErrors(
                    '⚠️ لا يمكن تحميل PDF لإحالة OHCHR قبل استكمال جميع الحقول المطلوبة للمساءلة الأممية.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 🔹 UN Special Procedures
        |--------------------------------------------------------------------------
        */
        if ($referral->referral_track === 'SPECIAL_PROCEDURES') {

            // 🔹 UN SP – Torture
            if ($referral->special_procedure_type === 'TORTURE') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_torture')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_profile_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en)
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة التعذيب.'
                    );
                }
            }

            // 🔹 UN SP – Enforced Disappearance
            if ($referral->special_procedure_type === 'ENFORCED_DISAPPEARANCE') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_enforced_disappearance')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->circumstances_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->steps_taken_en)
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة الاختفاء القسري.'
                    );
                }
            }

            // 🔹 UN SP – Arbitrary Detention
            if ($referral->special_procedure_type === 'ARBITRARY_DETENTION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_arbitrary_detention')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->detention_details_en) ||
                    empty($data?->legal_basis_en) ||
                    empty($data?->procedural_violations_en) ||
                    empty($data?->remedies_exhausted_en)
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة الاحتجاز التعسفي.'
                    );
                }
            }

            // 🔹 UN SP – Freedom of Expression
            if ($referral->special_procedure_type === 'FREEDOM_OF_EXPRESSION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_freedom_expression')
                    ->where('referral_id', $referral->referral_id)
                    ->first();
                        
                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->expression_activity_en) ||
                    empty($data?->violations_details_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                   )
                ) {
                   return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة حرية الرأي والتعبير.'
                    );
                }
            }

            // 🔹 UN SP – Human Rights Defenders
            if ($referral->special_procedure_type === 'HUMAN_RIGHTS_DEFENDERS') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_human_rights_defenders')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->defender_role_en) ||
                    empty($data?->activities_description_en) ||
                    empty($data?->targeting_link_en) ||
                    empty($data?->violations_details_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة المدافعين عن حقوق الإنسان.'
                    );
                }
            }

            // 🔹 UN SP – Extrajudicial / Summary / Arbitrary Executions
            if ($referral->special_procedure_type === 'EXTRAJUDICIAL_EXECUTIONS') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_extrajudicial_executions')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->circumstances_of_killing_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة الإعدام خارج نطاق القضاء.'
                    );
                }
            }

            // 🔹 UN SP – Violence against Women and Girls
            if ($referral->special_procedure_type === 'VIOLENCE_AGAINST_WOMEN') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_violence_against_women')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->violence_description_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة العنف ضد النساء والفتيات.'
                    );
                }
            }

            // 🔹 UN SP – Minority Issues
            if ($referral->special_procedure_type === 'MINORITY_ISSUES') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_minority_issues')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->minority_or_religious_identity_en) ||
                    empty($data?->violation_description_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة قضايا الأقليات.'
                    );
                }
            }

            // 🔹 UN SP – Freedom of Religion or Belief
            if ($referral->special_procedure_type === 'FREEDOM_OF_RELIGION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_freedom_of_religion')
                    ->where('referral_id', $referral->referral_id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->minority_or_religious_identity_en) ||
                    empty($data?->violation_description_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                       empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        'لا يمكن تحميل PDF قبل استكمال بيانات إحالة حرية الدين أو المعتقد.'
                    );
                }
            }

            // 🔒 الحارس النهائي (هذا هو المهم)
            if (!in_array($referral->special_procedure_type, [
                'TORTURE',
                'ENFORCED_DISAPPEARANCE',
                'ARBITRARY_DETENTION',
                'FREEDOM_OF_EXPRESSION',
                'HUMAN_RIGHTS_DEFENDERS',
                'EXTRAJUDICIAL_EXECUTIONS',
                'VIOLENCE_AGAINST_WOMEN',
                'MINORITY_ISSUES',
                'FREEDOM_OF_RELIGION',
            ])) {
                abort(500, 'Unsupported UN Special Procedure');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NGO LEGAL – Amnesty International (PDF Gate)
        |--------------------------------------------------------------------------
        */
        if (
            $referral->referral_track === 'NGO_LEGAL'
            && $referral->entity_name === 'Amnesty International'
        ) {
            $data = DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->where('referral_id', $referral->referral_id)
                ->first();

            if (
                empty($data?->source_account_en) ||
                empty($data?->general_location_en) ||
                empty($data?->incident_timeframe_en) ||
                empty($data?->violation_summary_en) ||
                empty($data?->psychosocial_impact_en)
            ) {
                return back()->withErrors(
                    'لا يمكن تحميل PDF قبل استكمال بيانات إحالة Amnesty International.'
                );
            }
        }        

        // تحميل الحالة
        $case = \App\Models\CaseModel::findOrFail($referral->case_id);
        $case->referral_track = $referral->referral_track;
        $case->special_procedure_type = $referral->special_procedure_type;
        $case->entity_name = $referral->entity_name;
        $case->humanitarian_type = $referral->humanitarian_type;
        $case->un_accountability_type = $referral->un_accountability_type;


        // توليد HTML
        $generator = app(CaseReportGenerator::class);

        $result = $generator->generate(
            case: $case,
            referralId: $referral->referral_id,
            user: Auth::user(),
            language: 'EN',
            includeIdentity: false
        );

        // توليد PDF
        $pdfService = app(GotenbergPdfService::class);
        $pdfBinary = $pdfService->generateFromHtml($result['html']);

        $fileName = 'Referral-' . $referral->case_number . '-' . $referral->entity_name . '.pdf';

        DB::connection('cases')
            ->table('case_referral_downloads')
            ->insert([
                'referral_id' => $referral->referral_id,
                'user_id' => auth()->id(),
                'downloaded_at' => now(),
            ]);
        
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referral->referral_id,
            'action' => 'downloaded',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }
}
