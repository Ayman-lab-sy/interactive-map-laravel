<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralAmnestyEditorialController extends Controller
{
    public function show(int $referralId)
    {
        // 1️⃣ الإحالة + الحالة + الجهة
        $referral = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id',
                'r.case_id',
                'r.referral_status',
                'r.ngo_type',
                'r.created_at as referral_created_at',

                'c.case_number',
                'c.created_at as case_created_at',
                'c.threat_description',
                'c.location',
                'c.threat_date',
                'c.direct_threat',
                'c.threat_source',
                'c.impact_details',

                'e.entity_name',
                'e.entity_type',
                'e.referral_track',
                'e.accepts_family_data',
            ])
            ->where('r.id', $referralId)
            ->first();

        abort_if(!$referral, 404);

        abort_unless(
            $referral->ngo_type === 'AMNESTY',
            403,
            'Unsupported NGO referral type'
        );

        $allowFamilyData = (bool) $referral->accepts_family_data;

        // 2️⃣ الحالة الكاملة
        $case = \App\Models\CaseModel::findOrFail($referral->case_id);

        // 🔥 إنشاء سجل إذا لم يكن موجود (حل المشكلة نهائياً)
        DB::connection('cases')
            ->table('case_referral_ngo_amnesty')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

        // 3️⃣ بيانات Amnesty
        $data = DB::connection('cases')
            ->table('case_referral_ngo_amnesty')
            ->where('referral_id', $referralId)
            ->first();

        // 4️⃣ جاهزية التوليد
        $readyForGeneration =
            !empty($data?->source_account_en) &&
            !empty($data?->general_location_en) &&
            !empty($data?->incident_timeframe_en) &&
            !empty($data?->violation_summary_en) &&
            !empty($data?->psychosocial_impact_en);

        // 5️⃣ السجل
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
            'admin.referrals.show_amnesty',
            compact('referral', 'case', 'data', 'readyForGeneration', 'auditLogs', 'allowFamilyData')
        );
    }


    public function saveSourceAccount(Request $request, $referralId)
    {
        $request->validate([
            'source_account_en' => [
                'required',
                'string',
                'regex:/^[\x20-\x7E\r\n]+$/'
            ],
        ], [
            'source_account_en.required' => 'شهادة المصدر إلزامية.',
            'source_account_en.regex'    => 'هذا الحقل يقبل اللغة الإنكليزية فقط.',
        ]);

        DB::connection('cases')
            ->table('case_referral_ngo_amnesty')
            ->updateOrInsert(
                ['referral_id' => $referralId],
                [
                    'source_account_en' => $request->source_account_en,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'amnesty_source_account_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ شهادة المصدر بنجاح.');
    }

    public function saveCaseSummary(Request $request, $referralId)
    {
        $request->validate([
            'general_location_en' => ['required','string','max:255'],
            'incident_timeframe_en' => ['required','string','max:255'],
            'violation_summary_en' => ['required','string'],
            'psychosocial_impact_en' => ['required','string'],
        ]);

        // 🔥 جلب السجل الحالي
        $existing = DB::connection('cases')
            ->table('case_referral_ngo_amnesty')
            ->where('referral_id', $referralId)
            ->first();

        $data = [
            'general_location_en' => $request->general_location_en,
            'incident_timeframe_en' => $request->incident_timeframe_en,
            'violation_summary_en' => $request->violation_summary_en,
            'psychosocial_impact_en' => $request->psychosocial_impact_en,
            'updated_at' => now(),
        ];


        if ($existing) {
            // ✅ تحديث فقط بدون لمس باقي الحقول
            DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->where('referral_id', $referralId)
                ->update($data);
        } else {
            // ✅ إنشاء سجل جديد
            $data['referral_id'] = $referralId;
            $data['created_at'] = now();

            DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->insert($data);
        }

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'amnesty_case_summary_saved',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ ملخص الحالة بنجاح.');
    }

    public function saveOptional(Request $request, $referralId)
    {
        $request->validate([
            'human_rights_considerations_en' => ['nullable','string'],
            'verification_level' => ['nullable','in:UNVERIFIED,BASIC_REVIEW,INTERNAL_REVIEW,ENHANCED_REVIEW'],
            'review_summary_en' => ['nullable','string'],
           'review_date' => ['nullable','date'],
            'has_supporting_materials' => ['nullable','boolean'],
        ]);

        $existing = DB::connection('cases')
            ->table('case_referral_ngo_amnesty')
            ->where('referral_id', $referralId)
            ->first();

        $data = [
            'human_rights_considerations_en' => $request->human_rights_considerations_en,
            'verification_level' => $request->verification_level ?? 'UNVERIFIED',
            'review_summary_en' => $request->review_summary_en,
            'review_date' => $request->review_date,
            'has_supporting_materials' => $request->boolean('has_supporting_materials', false),
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->where('referral_id', $referralId)
                ->update($data);
        } else {
            $data['referral_id'] = $referralId;
            $data['created_at'] = now();

            DB::connection('cases')
                ->table('case_referral_ngo_amnesty')
                ->insert($data);
        }

        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $referralId,
            'action' => 'amnesty_internal_notes_updated',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم حفظ الحقول الاختيارية.');
    }

}
