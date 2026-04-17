<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;

class ReferralHumanitarianICRCController extends Controller
{
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

        $allowFamilyData = (bool) $referral->accepts_family_data;

        $case = CaseModel::findOrFail($referral->case_id);

        $data = DB::connection('cases')
            ->table('case_referral_humanitarian_icrc')
            ->where('referral_id', $referralId)
            ->first();

        $steps = (object)[
            'source_saved'        => !empty($data?->source_account_en),
            'location_saved'      => !empty($data?->general_location_en),
            'timeframe_saved'     => !empty($data?->incident_timeframe_en),
            'needs_saved'         => !empty($data?->humanitarian_needs_en),
            'risks_saved'         => !empty($data?->immediate_risks_en),
            'mandate_saved'       => !empty($data?->mandate_relevance_en),
            'snapshot_saved'      => !empty($data?->case_snapshot_en),
            'assistance_saved'    => !empty($data?->assistance_requested_en),
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
            'admin.referrals.show_humanitarian_icrc',
            compact('referral', 'case', 'data', 'steps', 'auditLogs', 'allowFamilyData')
        );
    }

    /* =======================
       Save Blocks
       ======================= */

    public function saveSource(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'source_account_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['source_account_en' => $request->source_account_en],
            'humanitarian_icrc_source_saved'
        );

        return back()->with('success', 'تم حفظ مصدر المعلومات.')->withFragment('block-a');
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
            'humanitarian_icrc_location_time_saved'
        );

        return back()->with('success', 'تم حفظ الموقع والإطار الزمني.')->withFragment('block-b');
    }

    public function saveNeeds(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'humanitarian_needs_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['humanitarian_needs_en' => $request->humanitarian_needs_en],
            'humanitarian_icrc_needs_saved'
        );

        return back()->with('success', 'تم حفظ الاحتياجات الإنسانية.')->withFragment('block-c');
    }

    public function saveRisks(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'immediate_risks_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['immediate_risks_en' => $request->immediate_risks_en],
            'humanitarian_icrc_risks_saved'
        );

        return back()->with('success', 'تم حفظ المخاطر المباشرة.')->withFragment('block-d');
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
            'humanitarian_icrc_mandate_saved'
        );

        return back()->with('success', 'تم حفظ فقرة اختصاص ICRC.')->withFragment('block-e');
    }

    public function saveSnapshot(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'case_snapshot_en' => 'nullable|string',
        ]);

        $this->saveField(
            $referralId,
            ['case_snapshot_en' => $request->case_snapshot_en],
            'humanitarian_icrc_snapshot_saved'
        );

        return back()->with('success', 'تم حفظ لمحة الحالة.')->withFragment('block-f');
    }

    public function saveAssistance(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'assistance_requested_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['assistance_requested_en' => $request->assistance_requested_en],
            'humanitarian_icrc_assistance_saved'
        );

        return back()->with('success', 'تم حفظ طلب المساعدة.')->withFragment('block-g');
    }

    /* =======================
       Helpers
       ======================= */

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
                ->table('case_referral_humanitarian_icrc')
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
