<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralUnSpEnforcedDisappearanceController extends Controller
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
                'e.accepts_family_data',
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);

        $allowFamilyData = (bool) $referral->accepts_family_data;

        // الحالة الأصلية (عرض فقط)
        $case = \App\Models\CaseModel::findOrFail($referral->case_id);

        // بيانات UN SP Enforced Disappearance
        $data = DB::connection('cases')
            ->table('case_referral_un_sp_enforced_disappearance')
            ->where('referral_id', $referralId)
            ->first();

        // حالة الخطوات
        $steps = (object)[
            'summary_saved'      => !empty($data?->incident_summary_en),
            'victim_saved'       => !empty($data?->victim_information_en),
            'circumstances_saved'=> !empty($data?->circumstances_en),
            'perpetrators_saved' => !empty($data?->alleged_perpetrators_en),
            'context_done'       => !empty($data?->context_pattern_en) || ($data?->context_skipped == 1),
            'remedies_saved'     => !empty($data?->steps_taken_en),
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
            'admin.referrals.show_un_special_enforced_disappearance',
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
            'un_sp_enforced_disappearance_summary_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم حفظ ملخص الوقائع بنجاح.')
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
            'un_sp_enforced_disappearance_victim_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم حفظ معلومات الضحية بنجاح')
            ->withFragment('block-c');
    }

    public function saveCircumstances(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'circumstances_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['circumstances_en' => $request->circumstances_en],
            'un_sp_enforced_disappearance_circumstances_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم حفظ ظروف الاختفاء بنجاح')
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
            'un_sp_enforced_disappearance_perpetrators_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم حفظ الجهات المشتبه بها')
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
            'un_sp_enforced_disappearance_context_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم حفظ السياق')
            ->withFragment('block-f');
    }

    public function skipContext(int $referralId)
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('id', $referralId)
            ->first();

        abort_if(!$referral, 404);

        abort_if(
            in_array($referral->referral_status, ['generated','exported']),
            403,
            'Referral is locked'
        );

        DB::connection('cases')
            ->table('case_referral_un_sp_enforced_disappearance')
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
            'action' => 'un_sp_enforced_disappearance_context_skipped',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.referrals.un_sp.enforced_disappearance.show', $referralId)
            ->with('success', 'تم تخطي قسم السياق والمتابعة')
            ->withFragment('block-f');
    }

    public function saveRemedies(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'steps_taken_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['steps_taken_en' => $request->steps_taken_en],
            'un_sp_enforced_disappearance_remedies_saved'
        );

        return back()->with('success', 'تم حفظ الخطوات المتخذة.');
    }

    /* =======================
       Helpers (نفس منطق Torture)
       ======================= */

    private function assertEditable(int $referralId): void
    {
        $referral = DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('id', $referralId)
            ->first();

        abort_if(!$referral, 404, 'Referral not found');

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
                ->table('case_referral_un_sp_enforced_disappearance')
                ->updateOrInsert(
                    ['referral_id' => $referralId],
                    array_merge($data, [
                        'created_by' => auth()->id(),
                        'created_at' => now(),
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
