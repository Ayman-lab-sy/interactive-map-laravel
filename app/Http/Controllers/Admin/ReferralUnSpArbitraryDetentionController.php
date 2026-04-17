<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;

class ReferralUnSpArbitraryDetentionController extends Controller
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
        $case = CaseModel::findOrFail($referral->case_id);

        // بيانات Arbitrary Detention
        $data = DB::connection('cases')
            ->table('case_referral_un_sp_arbitrary_detention')
            ->where('referral_id', $referralId)
            ->first();

        // حالة الخطوات
        $steps = (object)[
            'summary_saved'     => !empty($data?->incident_summary_en),
            'victim_saved'      => !empty($data?->victim_information_en),
            'detention_saved'   => !empty($data?->detention_details_en),
            'legal_basis_saved' => !empty($data?->legal_basis_en),
            'procedural_saved'  => !empty($data?->procedural_violations_en),
            'context_done'      => !empty($data?->context_pattern_en) || ($data?->context_skipped == 1),
            'remedies_saved'    => !empty($data?->remedies_exhausted_en),
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
            'admin.referrals.show_un_special_arbitrary_detention',
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
            'un_sp_ad_summary_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ ملخص الوقائع بنجاح.')
            ->withFragment('block-b'); // ← يفتح بلوك Victim مباشرة
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
            'un_sp_ad_victim_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ معلومات الضحية بنجاح')
            ->withFragment('block-c'); 
    }

    public function saveDetentionDetails(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'detention_details_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['detention_details_en' => $request->detention_details_en],
            'un_sp_ad_detention_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ تفاصيل الاجتجاز بنجاح')
            ->withFragment('block-d'); 
    }

    public function saveLegalBasis(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

            $request->validate([
        'legal_basis_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['legal_basis_en' => $request->legal_basis_en],
            'un_sp_ad_legal_basis_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ الاساس القانوني للاحتجاز بنجاح')
            ->withFragment('block-e'); 
    }

    public function saveProceduralViolations(Request $request, int $referralId)
    {
        $this->assertEditable($referralId);

        $request->validate([
            'procedural_violations_en' => 'required|string',
        ]);

        $this->saveField(
            $referralId,
            ['procedural_violations_en' => $request->procedural_violations_en],
            'un_sp_ad_procedural_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ الانتهاكات الاجرائية بنجاح ')
            ->withFragment('block-f'); 
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
            'un_sp_ad_context_saved'
        );

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم حفظ السياق ')
            ->withFragment('block-g'); 
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
            ->table('case_referral_un_sp_arbitrary_detention')
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
            'action' => 'un_sp_ad_context_skipped',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.referrals.un_sp.arbitrary_detention.show', $referralId)
            ->with('success', 'تم تخطي قسم السياق والمتابعة')
            ->withFragment('block-g'); 
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
            'un_sp_ad_remedies_saved'
        );

        return back()->with('success', 'تم حفظ استنفاد سبل الانتصاف بنجاح.');
    }


    /* =======================
       Helpers (نفس منطق المسارات الأخرى)
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
                ->table('case_referral_un_sp_arbitrary_detention')
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
