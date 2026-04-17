<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReferralStatusController extends Controller
{
    public function markReady($id)
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select(
                'r.id',
                'r.referral_status',
                'r.special_procedure_type',
                'e.referral_track',
                'e.entity_name',
                'r.humanitarian_type',
                'r.un_accountability_type',
                'r.ngo_type'

            )

            ->where('r.id', $id)
            ->first();

        abort_if(!$referral, 404);

        // 🔒 قفل الإحالة بعد التوليد
        if (in_array($referral->referral_status, ['generated','exported'])) {
            return back()->withErrors(
                '⚠️ هذه الإحالة مقفلة ولا يمكن تغيير حالتها.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | مسار UN Special Procedures
        |--------------------------------------------------------------------------
        */
        if ($referral->referral_track === 'SPECIAL_PROCEDURES') {

            // 🔹 UN SP – Torture
            if ($referral->special_procedure_type === 'TORTURE') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_torture')
                    ->where('referral_id', $id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_profile_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->remedies_exhausted_en)
                ) {
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Torture) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Enforced Disappearance
            if ($referral->special_procedure_type === 'ENFORCED_DISAPPEARANCE') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_enforced_disappearance')
                    ->where('referral_id', $id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->circumstances_en) ||
                    empty($data?->alleged_perpetrators_en) ||
                    empty($data?->steps_taken_en)
                ) {
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Enforced Disappearance) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Arbitrary Detention
            if ($referral->special_procedure_type === 'ARBITRARY_DETENTION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_arbitrary_detention')
                    ->where('referral_id', $id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->detention_details_en) ||
                    empty($data?->legal_basis_en) ||
                    empty($data?->procedural_violations_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Arbitrary Detention) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Freedom of Expression
            if ($referral->special_procedure_type === 'FREEDOM_OF_EXPRESSION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_freedom_expression')
                    ->where('referral_id', $id)
                    ->first();

                if (
                    empty($data?->incident_summary_en) ||
                    empty($data?->victim_information_en) ||
                    empty($data?->expression_activity_en) ||
                    empty($data?->violations_details_en) ||
                    empty($data?->legal_basis_en) ||
                    empty($data?->remedies_exhausted_en) ||
                    (
                        empty($data?->context_pattern_en)
                        && (int)($data?->context_skipped) !== 1
                    )
                ) {
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Freedom of Expression) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Human Rights Defenders
            if ($referral->special_procedure_type === 'HUMAN_RIGHTS_DEFENDERS') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_human_rights_defenders')
                    ->where('referral_id', $id)
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
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Human Rights Defenders) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Extrajudicial / Summary / Arbitrary Executions
            if ($referral->special_procedure_type === 'EXTRAJUDICIAL_EXECUTIONS') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_extrajudicial_executions')
                    ->where('referral_id', $id)
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
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Extrajudicial Executions) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Violence against Women and Girls
            if ($referral->special_procedure_type === 'VIOLENCE_AGAINST_WOMEN') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_violence_against_women')
                    ->where('referral_id', $id)
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
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Violence against Women and Girls) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Minority Issues
            if ($referral->special_procedure_type === 'MINORITY_ISSUES') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_minority_issues')
                    ->where('referral_id', $id)
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
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Minority Issues) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔹 UN SP – Freedom of Religion or Belief
            if ($referral->special_procedure_type === 'FREEDOM_OF_RELIGION') {

                $data = DB::connection('cases')
                    ->table('case_referral_un_sp_freedom_of_religion')
                    ->where('referral_id', $id)
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
                        '⚠️ لا يمكن تجهيز إحالة UN Special Procedures (Freedom of Religion or Belief) قبل استكمال جميع الحقول المطلوبة.'
                    );
                }
            }

            // 🔒 الحارس النهائي – يمنع أي UN Special Procedure غير معرّف صراحة
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
            
        /*
        |--------------------------------------------------------------------------
        | Humanitarian Protection – ICRC
        |--------------------------------------------------------------------------
        */
        } elseif (
            $referral->referral_track === 'HUMANITARIAN_PROTECTION'
            && $referral->humanitarian_type === 'ICRC'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_humanitarian_icrc')
                ->where('referral_id', $id)
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
                    '⚠️ لا يمكن تجهيز إحالة ICRC قبل استكمال جميع الحقول الإنسانية المطلوبة.'
                );
            }

        /*
        |--------------------------------------------------------------------------
        | Humanitarian Protection – UNHCR
        |--------------------------------------------------------------------------
        */
        } elseif (
            $referral->referral_track === 'HUMANITARIAN_PROTECTION'
            && $referral->humanitarian_type === 'UNHCR'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_humanitarian_unhcr')
                ->where('referral_id', $id)
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
                    '⚠️ لا يمكن تجهيز إحالة UNHCR قبل استكمال جميع الحقول الإنسانية المطلوبة.'
                );
            }

        /*
        |--------------------------------------------------------------------------
        | UN Accountability – OHCHR
        |--------------------------------------------------------------------------
        */
        } elseif (
            $referral->referral_track === 'UN_ACCOUNTABILITY'
            && $referral->un_accountability_type === 'OHCHR'
        ) {

            $data = DB::connection('cases')
                ->table('case_referral_un_accountability_ohchr')
                ->where('referral_id', $id)
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
                    '⚠️ لا يمكن تجهيز إحالة OHCHR قبل استكمال جميع الحقول المطلوبة للمساءلة الأممية.'
                );
            }
        
        /*
        |--------------------------------------------------------------------------
        | NGO LEGAL TRACK
        |--------------------------------------------------------------------------
        */
        } elseif ($referral->referral_track === 'NGO_LEGAL') {

            if ($referral->ngo_type === 'AMNESTY') {

                $data = DB::connection('cases')
                    ->table('case_referral_ngo_amnesty')
                    ->where('referral_id', $id)
                    ->first();

                if (
                    empty($data?->source_account_en) ||
                    empty($data?->general_location_en) ||
                    empty($data?->incident_timeframe_en) ||
                    empty($data?->violation_summary_en) ||
                    empty($data?->psychosocial_impact_en)
                ) {            
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز إحالة Amnesty International قبل استكمال: شهادة المصدر وملخص الحالة التحريري.'
                    );
                }        

            } else {

                $hasEnglishNarrative = DB::connection('cases')
                    ->table('case_referral_narratives')
                    ->where('referral_id', $id)
                    ->where('language', 'en')
                    ->exists();

                if (!$hasEnglishNarrative) {
                    return back()->withErrors(
                        '⚠️ لا يمكن تجهيز الإحالة للتوليد قبل إدخال الصياغة القانونية باللغة الإنكليزية.'
                    );
                }
            }
        }
        // ✅ تغيير الحالة
        DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('id', $id)
            ->where('referral_status', 'prepared')
            ->update([
                'referral_status' => 'ready_for_generation',
            ]);
    
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $id,
            'action' => 'marked_ready',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    
        return back()->with('success', 'تم تجهيز الإحالة للتوليد.');
    }
}
