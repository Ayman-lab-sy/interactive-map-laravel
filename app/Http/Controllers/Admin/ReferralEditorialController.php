<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralEditorialController extends Controller
{
    public function saveSummaryControls(Request $request, $referralId)
    {
        $request->validate([
            'violation_classification' => 'required|string|max:100',
            'summary_alignment_note'   => 'required|string|min:10',
        ]);

        DB::connection('cases')->table('case_entity_referrals')
            ->where('id', $referralId)
            ->update([
                'violation_classification' => $request->violation_classification,
                'summary_alignment_note'   => $request->summary_alignment_note,
            ]);

        // Audit log
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action'      => 'summary_controls_saved',
            'user_id'     => auth()->id(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'تم حفظ مواءمة ملخص القضية بنجاح.');
    }

    public function saveAnalyticalContent(Request $request, $referralId)
    {
        $request->validate([
            'general_location_en' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x00-\x7F]+$/'
            ],
            'incident_timeframe_en' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x00-\x7F]+$/'
            ],
            'psychosocial_impact_en' => [
                'required',
                'string',
                'regex:/^[\x00-\x7F]+$/'
            ],
        ], [
            'general_location_en.required' => 'حقل الموقع العام إلزامي.',
            'incident_timeframe_en.required' => 'حقل الإطار الزمني إلزامي.',
            'psychosocial_impact_en.required' => 'حقل الأثر النفسي إلزامي.',

            'general_location_en.regex' => 'هذا الحقل يقبل اللغة الإنكليزية فقط.',
            'incident_timeframe_en.regex' => 'هذا الحقل يقبل اللغة الإنكليزية فقط.',
            'psychosocial_impact_en.regex' => 'هذا الحقل يقبل اللغة الإنكليزية فقط.',
        ]);

        DB::connection('cases')
            ->table('case_referral_narratives')
            ->where('referral_id', $referralId)
            ->where('language', 'en')
            ->update([
                'general_location_en'    => $request->general_location_en,
                'incident_timeframe_en'  => $request->incident_timeframe_en,
                'psychosocial_impact_en' => $request->psychosocial_impact_en,
                'updated_at'             => now(),
            ]);

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action'      => 'analytical_content_saved',
            'user_id'     => auth()->id(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'تم حفظ المحتوى التحليلي بنجاح.');
    }
}
