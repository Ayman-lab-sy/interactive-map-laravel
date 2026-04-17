<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralUnSpTortureController extends Controller
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

        // جلب الحالة الأصلية كاملة (للعرض المرجعي فقط)
        $case = \App\Models\CaseModel::findOrFail($referral->case_id);

        // بيانات UN SP Torture (قد تكون NULL)
        $data = DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->where('referral_id', $referralId)
            ->first();

        // حالة الخطوات (عرض فقط)
        $steps = (object)[
            'summary_saved'        => !empty($data?->incident_summary_en),
            'victim_saved'         => !empty($data?->victim_profile_en),
            'perpetrators_saved'   => !empty($data?->alleged_perpetrators_en),
            'context_done'         => !empty($data?->context_pattern_en) || ($data?->context_skipped == 1),
            'remedies_saved'       => !empty($data?->remedies_exhausted_en),
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
            'admin.referrals.show_un_special',
            compact('referral', 'case', 'data', 'steps', 'auditLogs', 'allowFamilyData')
        );
    }

    public function saveSummary(Request $request, int $referralId)
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

        $request->validate([
            'incident_summary_en' => 'required|string',
        ]);

        DB::connection('cases')->transaction(function () use ($request, $referralId) {

            DB::connection('cases')
                ->table('case_referral_un_sp_torture')
                ->updateOrInsert(
                    ['referral_id' => $referralId],
                    [
                        'incident_summary_en' => $request->incident_summary_en,
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                    ]
                );

            DB::connection('cases')->table('case_referral_audits')->insert([
                'referral_id' => $referralId,
                'action' => 'un_sp_torture_summary_saved',
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'تم حفظ ملخص الوقائع بنجاح');
    }

    public function saveVictim(Request $request, int $referralId)
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

        $request->validate([
            'victim_profile_en' => 'required|string',
        ]);

        DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'victim_profile_en' => $request->victim_profile_en,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ]
            );
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_torture_victim_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ وصف الضحية بنجاح');
    }

    public function savePerpetrators(Request $request, int $referralId)
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

        $request->validate([
            'alleged_perpetrators_en' => 'required|string',
        ]);

        DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'alleged_perpetrators_en' => $request->alleged_perpetrators_en,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ]
            );
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_torture_perpetrators_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ معلومات الاطراف المزعومة');
    }
    
    public function saveContext(Request $request, int $referralId)
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

        $request->validate([
            'context_pattern_en' => 'nullable|string',
        ]);

        DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'context_pattern_en' => $request->context_pattern_en,
                    'context_skipped' => 0,
                    'created_at' => now(),
                ]
            );

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_torture_context_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ السياق ');
    }

    public function skipContext(int $referralId)
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

        DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'context_pattern_en' => null,
                    'context_skipped' => 1,
                    'created_at' => now(),
                ]
            );

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_torture_context_skipped',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم تخطي قسم السياق.');
    }

    public function saveRemedies(Request $request, int $referralId)
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

        $request->validate([
            'remedies_exhausted_en' => 'required|string',
        ]);

        DB::connection('cases')
            ->table('case_referral_un_sp_torture')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'remedies_exhausted_en' => $request->remedies_exhausted_en,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ]
            );
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'un_sp_torture_remedies_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم استكمال بيانات احالة UN Special Pordedures');
    }    

}
