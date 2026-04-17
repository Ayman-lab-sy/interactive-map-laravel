<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;

class ReferralUnSpHumanRightsDefendersController extends Controller
{
    public function show(int $referralId)
    {
        // الإحالة الأساسية
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
                'e.accepts_family_data', // ⭐ الأساس
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);

        $allowFamilyData = (bool) $referral->accepts_family_data;

        $case = CaseModel::findOrFail($referral->case_id);

        $data = DB::connection('cases')
            ->table('case_referral_un_sp_human_rights_defenders')
            ->where('referral_id', $referralId)
            ->first();

        $steps = (object)[
            'summary_saved'      => !empty($data?->incident_summary_en),
            'victim_saved'       => !empty($data?->victim_information_en),
            'role_saved'         => !empty($data?->defender_role_en),
            'activities_saved'   => !empty($data?->activities_description_en),
            'targeting_saved'    => !empty($data?->targeting_link_en),
            'violations_saved'   => !empty($data?->violations_details_en),
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
            'admin.referrals.show_un_special_human_rights_defenders',
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
            'un_sp_hrd_summary_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
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
            'un_sp_hrd_victim_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ معلومات المدافع')
         ->withFragment('block-c');
    }

    public function saveDefenderRole(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'defender_role_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['defender_role_en' => $request->defender_role_en],
            'un_sp_hrd_role_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ دور المدافع الحقوقي')
         ->withFragment('block-d');
    }

    public function saveActivities(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'activities_description_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['activities_description_en' => $request->activities_description_en],
            'un_sp_hrd_activities_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ وصف الانشطة')
         ->withFragment('block-e');
    }

    public function saveTargetingLink(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'targeting_link_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['targeting_link_en' => $request->targeting_link_en],
            'un_sp_hrd_targeting_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ رابط الاستهداف')
         ->withFragment('block-f');
    }

    public function saveViolations(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'violations_details_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['violations_details_en' => $request->violations_details_en],
            'un_sp_hrd_violations_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ الانتهاكات')
         ->withFragment('block-g');
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
            'un_sp_hrd_context_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ السياق')
         ->withFragment('block-h');
    }

    public function skipContext(int $referralId)
    {
        $this->assertEditable($referralId);

        DB::connection('cases')
            ->table('case_referral_un_sp_human_rights_defenders')
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
            'action' => 'un_sp_hrd_context_skipped',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم تخطي قسم السياق')
         ->withFragment('block-h');
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
            'un_sp_hrd_remedies_saved'
        );

        return redirect()->route(
            'admin.referrals.un_sp.human_rights_defenders.show',
            $referralId
        )->with('success', 'تم حفظ سبل الانتصاف')
         ->withFragment('block-h');
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
                ->table('case_referral_un_sp_human_rights_defenders')
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
