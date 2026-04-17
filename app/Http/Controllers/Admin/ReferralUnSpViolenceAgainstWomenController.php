<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;

class ReferralUnSpViolenceAgainstWomenController extends Controller
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
            ->table('case_referral_un_sp_violence_against_women')
            ->where('referral_id', $referralId)
            ->first();

        $steps = (object)[
            'summary_saved'      => !empty($data?->incident_summary_en),
            'victim_saved'       => !empty($data?->victim_information_en),
            'violence_saved'     => !empty($data?->violence_description_en),
            'perpetrators_saved' => !empty($data?->alleged_perpetrators_en),
            'context_done'       => !empty($data?->context_pattern_en) || ($data?->context_skipped == 1),
            'remedies_saved'     => !empty($data?->remedies_exhausted_en),
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
            'admin.referrals.show_un_special_violence_against_women',
            compact('referral', 'case', 'data', 'steps', 'auditLogs', 'allowFamilyData')
        );
    }

    public function saveSummary(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'incident_summary_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['incident_summary_en' => $request->incident_summary_en],
            'un_sp_vaw_summary_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ ملخص الوقائع.')
         ->withFragment('block-b');
    }

    public function saveVictim(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'victim_information_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['victim_information_en' => $request->victim_information_en],
            'un_sp_vaw_victim_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ معلومات الضحية.')
         ->withFragment('block-c');
    }

    public function saveViolence(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'violence_description_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['violence_description_en' => $request->violence_description_en],
            'un_sp_vaw_violence_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ وصف العنف.')
         ->withFragment('block-d');
    }

    public function savePerpetrators(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'alleged_perpetrators_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['alleged_perpetrators_en' => $request->alleged_perpetrators_en],
            'un_sp_vaw_perpetrators_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ الجهة المتورطة.')
         ->withFragment('block-e');
    }

    public function saveContext(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'context_pattern_en' => 'nullable|string',
        ]);

        $this->saveField(
            $referralId,
            ['context_pattern_en' => $request->context_pattern_en],
            'un_sp_vaw_context_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ السياق.')
         ->withFragment('block-f');
    }

    public function skipContext(int $referralId)
    {
        $this->assertEditable($referralId);

        DB::connection('cases')
            ->table('case_referral_un_sp_violence_against_women')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'context_pattern_en' => null,
                    'context_skipped' => 1,
                    'updated_at' => now(),
                ]
            );

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_vaw_context_skipped',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم تخطي قسم السياق.')
         ->withFragment('block-f');
    }

    public function saveRemedies(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'remedies_exhausted_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['remedies_exhausted_en' => $request->remedies_exhausted_en],
            'un_sp_vaw_remedies_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.violence_against_women.show',
            $referralId
        )->with('success', 'تم حفظ سبل الانتصاف.')
         ->withFragment('block-f');
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
                ->table('case_referral_un_sp_violence_against_women')
                ->updateOrInsert(
                    ['referral_id' => $referralId],
                    array_merge($data, [
                        'created_by' => auth()->id(),
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
