<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;

class ReferralUNOHCHRController extends Controller
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
                'c.created_at as case_created_at',
                'e.entity_name',
                'e.entity_type',
                'e.referral_track',
                'e.accepts_family_data',
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);
        abort_if($referral->referral_track !== 'UN_ACCOUNTABILITY', 403);

        $allowFamilyData = (bool) $referral->accepts_family_data;

        $case = CaseModel::findOrFail($referral->case_id);

        $data = DB::connection('cases')
            ->table('case_referral_un_accountability_ohchr')
            ->where('referral_id', $referralId)
            ->first();

        $steps = (object)[
            'context_saved'     => !empty($data?->source_context_en),
            'methodology_saved' => !empty($data?->methodology_note_en),
            'location_saved'    => !empty($data?->general_location_en),
            'timeframe_saved'   => !empty($data?->incident_timeframe_en),
            'documented_saved'  => !empty($data?->documented_information_en),
            'concerns_saved'    => !empty($data?->identified_concerns_en),
            'pattern_saved'     => !empty($data?->pattern_observation_en),
            'mandate_saved'     => !empty($data?->mandate_relevance_en),
        ];

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
            'admin.referrals.show_un_ohchr',
            compact('referral', 'case', 'data', 'steps', 'auditLogs', 'allowFamilyData')
        );
    }

    /* =====================================================
       Save Blocks
       ===================================================== */

    public function saveContext(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'source_context_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['source_context_en' => $request->source_context_en],
            'ohchr_context_saved'
        );

        return back()->with('success', 'تم حفظ سياق المعلومات.')->withFragment('block-a');
    }

    public function saveMethodology(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'methodology_note_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['methodology_note_en' => $request->methodology_note_en],
            'ohchr_methodology_saved'
        );

        return back()->with('success', 'تم حفظ المنهجية.')->withFragment('block-b');
    }

    public function saveLocationTime(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'general_location_en'   => 'required|string',
            'incident_timeframe_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            [
                'general_location_en'   => $request->general_location_en,
                'incident_timeframe_en' => $request->incident_timeframe_en,
            ],
            'ohchr_location_time_saved'
        );

        return back()->with('success', 'تم حفظ الموقع والإطار الزمني.')->withFragment('block-c');
    }

    public function saveDocumentedInfo(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'documented_information_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['documented_information_en' => $request->documented_information_en],
            'ohchr_documented_info_saved'
        );

        return back()->with('success', 'تم حفظ المعلومات الموثقة.')->withFragment('block-d');
    }

    public function saveConcerns(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'identified_concerns_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['identified_concerns_en' => $request->identified_concerns_en],
            'ohchr_concerns_saved'
        );

        return back()->with('success', 'تم حفظ مجالات القلق الحقوقي.')->withFragment('block-e');
    }

    public function savePattern(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'pattern_observation_en' => 'nullable|string',
        ]);

        $this->saveField(
            $referralId,
            ['pattern_observation_en' => $request->pattern_observation_en],
            'ohchr_pattern_saved'
        );

        return back()->with('success', 'تم حفظ ملاحظات النمط.')->withFragment('block-f');
    }

    public function saveMandate(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'mandate_relevance_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['mandate_relevance_en' => $request->mandate_relevance_en],
            'ohchr_mandate_saved'
        );

        return back()->with('success', 'تم حفظ ارتباط التفويض.')->withFragment('block-g');
    }

    public function saveInternalNotes(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'additional_notes_internal' => 'nullable|string',
        ]);

        $this->saveField(
            $referralId,
            ['additional_notes_internal' => $request->additional_notes_internal],
            'ohchr_internal_notes_saved'
        );

        return back()->with('success', 'تم حفظ الملاحظات الداخلية.');
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

    private function saveField(int $referralId, array $data, string $auditAction): void
    {
        DB::connection('cases')->transaction(function () use ($referralId, $data, $auditAction) {

            DB::connection('cases')
                ->table('case_referral_un_accountability_ohchr')
                ->updateOrInsert(
                    ['referral_id' => $referralId],
                    array_merge($data, [
                        'updated_at' => now(),
                    ])
                );

            DB::connection('cases')
                ->table('case_referral_audits')
                ->insert([
                    'referral_id' => $referralId,
                    'action' => $auditAction,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
        });
    }
}
