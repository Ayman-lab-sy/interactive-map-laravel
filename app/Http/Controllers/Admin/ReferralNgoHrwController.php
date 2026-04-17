<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralNgoHrwController extends Controller
{
    /* =====================================================
       Show Referral Page
       ===================================================== */

    public function show(int $referralId)
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id',
                'r.case_id',
                'r.referral_status',
                'r.created_at as referral_created_at',

                'c.case_number',

                'e.entity_name',
                'e.entity_type',
                'e.referral_track',
                'e.accepts_family_data',

                // HRW editorial fields
                'r.violation_classification',
                'r.summary_alignment_note',
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);
        abort_if(
            $referral->referral_track !== 'NGO_LEGAL'
            || $referral->entity_name !== 'Human Rights Watch',
            403
        );

        $allowFamilyData = (bool) $referral->accepts_family_data;

        $case = CaseModel::findOrFail($referral->case_id);

        // Narrative + Analytical content stored together
        $narrative = DB::connection('cases')
            ->table('case_referral_narratives')
            ->where('referral_id', $referralId)
            ->where('language', 'en')
            ->first();

        /* =======================
           Steps – HRW Unified Logic
           ======================= */

        $steps = (object)[
            // Block A – Legal Narrative
            'narrative_saved' => !empty($narrative?->content),

            // Block B – Summary Alignment
            'summary_saved' =>
                !empty($referral->violation_classification)
                && !empty($referral->summary_alignment_note),

            // Block C – Analytical Content
            'analysis_saved' =>
                !empty($narrative?->general_location_en)
                && !empty($narrative?->incident_timeframe_en)
                && !empty($narrative?->psychosocial_impact_en),
        ];

        /* =======================
           Ready for Generation
           ======================= */

        $readyForGeneration =
            $steps->narrative_saved
            && $steps->summary_saved
            && $steps->analysis_saved;


        $auditLogs = DB::connection('cases')
            ->table('case_referral_audits as a')
            ->leftJoin('h96737_alawite.users as u', 'u.id', '=', 'a.user_id')
            ->where('a.referral_id', $referralId)
            ->orderBy('a.created_at', 'desc')
            ->select([
                'a.action',
                'a.created_at',
                'u.name as user_name',
            ])
            ->get();

        return view(
            'admin.referrals.show_hrw',
            compact(
                'referral',
                'case',
                'narrative',
                'steps',
                'readyForGeneration',
                'auditLogs',
                'allowFamilyData'
            )
        );

    }

    /* =====================================================
       Block A – Legal Narrative
       ===================================================== */

    public function saveNarrative(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'narrative_en' => 'required|string',
        ]);

        DB::connection('cases')->transaction(function () use ($request, $referralId) {

            DB::connection('cases')
                ->table('case_referral_narratives')
                ->updateOrInsert(
                    [
                        'referral_id' => $referralId,
                        'language'    => 'en',
                    ],
                    [
                        'content'     => $request->narrative_en,
                        'created_by'  => auth()->id(),
                        'updated_at'  => now(),
                    ]
                );

            DB::connection('cases')
                ->table('case_referral_audits')
                ->insert([
                    'referral_id' => $referralId,
                    'action'      => 'hrw_narrative_saved',
                    'user_id'     => auth()->id(),
                    'created_at'  => now(),
                ]);
        });

        return back()
            ->with('success', 'تم حفظ الصياغة القانونية.')
            ->withFragment('block-b');
    }

    /* =====================================================
       Block B – Summary Alignment
       ===================================================== */

    public function saveSummaryAlignment(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'violation_classification' => 'required|string|max:120',
            'summary_alignment_note'   => 'required|string|min:10',
        ]);

        DB::connection('cases')->transaction(function () use ($request, $referralId) {

            DB::connection('cases')
                ->table('case_entity_referrals')
                ->where('id', $referralId)
                ->update([
                    'violation_classification' => $request->violation_classification,
                    'summary_alignment_note'   => $request->summary_alignment_note,
                    'created_at'               => now(),
                ]);

            DB::connection('cases')
                ->table('case_referral_audits')
                ->insert([
                    'referral_id' => $referralId,
                    'action'      => 'hrw_summary_alignment_saved',
                    'user_id'     => auth()->id(),
                    'created_at'  => now(),
                ]);
        });

        return back()
            ->with('success', 'تم حفظ مواءمة ملخص القضية.')
            ->withFragment('block-c');
    }

    /* =====================================================
       Block C – Analytical Content
       ===================================================== */

    public function saveAnalytical(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'general_location_en' => [
                'required', 'string', 'max:255', 'regex:/^[\x00-\x7F]+$/'
            ],
            'incident_timeframe_en' => [
                'required', 'string', 'max:255', 'regex:/^[\x00-\x7F]+$/'
            ],
            'psychosocial_impact_en' => [
                'required', 'string', 'regex:/^[\x00-\x7F]+$/'
            ],
        ]);

        DB::connection('cases')->transaction(function () use ($request, $referralId) {

            DB::connection('cases')
                ->table('case_referral_narratives')
                ->updateOrInsert(
                    [
                        'referral_id' => $referralId,
                        'language'    => 'en',
                    ],
                    [
                        'general_location_en'    => $request->general_location_en,
                        'incident_timeframe_en'  => $request->incident_timeframe_en,
                        'psychosocial_impact_en' => $request->psychosocial_impact_en,
                        'created_by'             => auth()->id(),
                        'updated_at'             => now(),
                    ]
                );

            DB::connection('cases')
                ->table('case_referral_audits')
                ->insert([
                    'referral_id' => $referralId,
                    'action'      => 'hrw_analytical_content_saved',
                    'user_id'     => auth()->id(),
                    'created_at'  => now(),
                ]);
        });

        return back()
            ->with('success', 'تم حفظ المحتوى التحليلي.')
            ->withFragment('block-d');
    }

    /* =====================================================
       Helpers
       ===================================================== */

    private function assertEditable(int $referralId): void
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('id', $referralId)
            ->first();

        abort_if(!$referral, 404);
        abort_if(
            in_array($referral->referral_status, ['generated', 'exported']),
            403,
            'Referral is locked'
        );
    }
}
