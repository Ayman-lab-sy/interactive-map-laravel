<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralDetailsController extends Controller
{
    public function show(Request $request, $id)
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->leftJoin(
                DB::raw('h96737_alawite.users as u'),
                'u.id',
                '=',
                'r.generated_by'
            )
            ->leftJoin(
                'case_referral_downloads as d',
                'd.referral_id',
                '=',
                'r.id'
            )
            ->select([
                'r.id',
                'r.case_id',
                'r.referral_status',
                'r.created_at',
                'c.case_number',

                // 🧠 بيانات الحالة للمحرر
                'c.location',
                'c.threat_date',
                'c.case_sensitivity',
                'c.is_pattern_case',
                'c.violation_type',
                'c.direct_threat',
                'c.threat_source',
                'c.threat_locations',
                'c.threat_description',
                'c.impact_details',

                'r.violation_classification',
                'r.summary_alignment_note',


                'e.entity_name',
                'e.entity_type',
                'e.referral_track',
                'r.generated_at',
                'r.generated_by',
                'u.name as generated_by_name',
                'e.accepts_family_data',
                DB::raw('COUNT(d.id) as downloads_count'),
                DB::raw('MAX(d.downloaded_at) as last_downloaded_at'),
            ])
            ->where('r.id', $id)
            ->groupBy(
                'r.id',
                'r.case_id',
                'r.referral_status',
                'r.created_at',
                'c.case_number',
                'c.location',
                'c.threat_date',
                'c.case_sensitivity',
                'c.is_pattern_case',
                'c.violation_type',
                'c.direct_threat',
                'c.threat_source',
                'c.threat_locations',
                'c.threat_description',
                'c.impact_details',
                'e.entity_name',
                'e.entity_type',
                'e.referral_track',
                'e.accepts_family_data',
                'r.generated_at',
                'r.generated_by',
                'r.violation_classification',
                'r.summary_alignment_note',
                'u.name'
            )
            ->first();

        abort_if(!$referral, 404);

        $allowFamilyData = (bool) $referral->accepts_family_data;

        $case = CaseModel::findOrFail($referral->case_id);

        $narrativeEn = DB::connection('cases')
            ->table('case_referral_narratives')
            ->where('referral_id', $id)
            ->where('language', 'en')
            ->value('content');

        // Block 1: Legal Narrative (EN)
        $hasEnglishNarrative = DB::connection('cases')
            ->table('case_referral_narratives')
            ->where('referral_id', $id)
            ->where('language', 'en')
            ->exists();

        // Block 2: Summary Alignment
        $hasSummaryAlignment =
            !empty($referral->violation_classification) &&
            !empty($referral->summary_alignment_note);

        // Block 3: Analytical Content (stored with EN narrative)
        $hasAnalyticalContent = DB::connection('cases')
            ->table('case_referral_narratives')
            ->where('referral_id', $id)
            ->where('language', 'en')
            ->whereNotNull('general_location_en')
            ->whereNotNull('incident_timeframe_en')
            ->whereNotNull('psychosocial_impact_en')
            ->exists();


        $auditLogs = DB::connection('cases')
            ->table('case_referral_audits as a')
            ->leftJoin(
                DB::raw('h96737_alawite.users as u'),
                'u.id',
                '=',
                'a.user_id'
            )
            ->select([
                'a.action',
                'a.created_at',
                'u.name as user_name',
            ])
            ->where('a.referral_id', $id)
            ->orderBy('a.created_at', 'desc')
            ->limit(10)
            ->get();

        $view = match (true) {

            // ✅ Amnesty International
            $referral->entity_name === 'Amnesty International'
                => 'admin.referrals.show_amnesty',

            // NGO (HRW وغيره)
            $referral->referral_track === 'NGO_LEGAL'
                => 'admin.referrals.show',

            // UN Special Procedures
            $referral->referral_track === 'SPECIAL_PROCEDURES'
                => 'admin.referrals.show_un_special',

            // Humanitarian
            $referral->referral_track === 'HUMANITARIAN_PROTECTION'
                => 'admin.referrals.show_humanitarian',

            // OHCHR
            $referral->referral_track === 'UN_ACCOUNTABILITY'
                => 'admin.referrals.show_ohchr',

            default
                => 'admin.referrals.show',
        };

        return view($view, compact(
            'referral',
            'case',
            'auditLogs',
            'hasEnglishNarrative',
            'hasSummaryAlignment',
            'hasAnalyticalContent', 
            'allowFamilyData',
            'narrativeEn'
        ));
    }

    /**
     * حفظ الصياغة القانونية (Human Legal Narrative)
     */
    public function saveNarrative(Request $request, $id)
    {
        // تحقق أساسي فقط (بدون تعقيد)
        $request->validate([
            'narrative_en' => 'required|string',
        ]);

        // نحذف أي صياغة سابقة لنفس الإحالة (نفس اللغة)
        DB::connection('cases')->transaction(function () use ($request, $id) {

            DB::connection('cases')
                ->table('case_referral_narratives')
                ->where('referral_id', $id)
                ->delete();

            // إنكليزي (إجباري)
            DB::connection('cases')
                ->table('case_referral_narratives')
                ->insert([
                    'referral_id' => $id,
                    'language' => 'en',
                    'content' => $request->narrative_en,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // عربي (اختياري)
            if ($request->filled('narrative_ar')) {
                DB::connection('cases')
                    ->table('case_referral_narratives')
                    ->insert([
                        'referral_id' => $id,
                        'language' => 'ar',
                        'content' => $request->narrative_ar,
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            DB::connection('cases')->table('case_referral_audits')->insert([
                'referral_id' => $id,
                'action' => 'legal_narrative_saved',
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.referrals.show', $id)
            ->with('success', 'تم حفظ الصياغة القانونية بنجاح.');
    }
}
