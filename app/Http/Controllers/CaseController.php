<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StoreCaseRequest;
use App\Models\CaseModel;

class CaseController extends Controller
{
    public function store(StoreCaseRequest $request)
    {
        // ????? ??? ??????
        $year = date('Y');
        $lastId = DB::connection('cases')->table('cases')->max('id') ?? 0;
        $caseNumber = sprintf('CASE-%s-%06d', $year, $lastId + 1);

        // ????? ??? ????????
        $followupToken = strtoupper(Str::random(4)) . '-' . rand(100, 999);

        // ????? ??????? ?? JSON
        $children = [];
        if ($request->children_names && $request->children_ages) {
            foreach ($request->children_names as $i => $name) {
                if ($name) {
                    $children[] = [
                        'name' => $name,
                        'age' => $request->children_ages[$i] ?? null,
                    ];
                }
            }
        }

        // ??? ??????
        $case = CaseModel::create([
            'case_number' => $caseNumber,
            'followup_token' => $followupToken,
            'full_name' => $request->full_name,
            'name_type' => $request->input('name_type'),
            'violation_type' => $request->input('violation_type'),
            'is_pattern_case' => (int) $request->input('is_pattern_case'),
            'case_sensitivity' => $request->input('case_sensitivity'),
            'birth_date' => $request->birth_date,
            'component' => $request->component,
            'location' => $request->location,
            'phone' => $request->phone, // 🔐 هذا سيتشفّر الآن
            'email' => $request->email,
            'spouse_name' => $request->spouse_name,
            'children' => json_encode($children),
            'direct_threat' => $request->boolean('direct_threat'),
            'threat_description' => $request->threat_description,
            'threat_source' => $request->threat_source,
            'threat_date' => $request->threat_date,
            'threat_locations' => $request->threat_locations,
            'psychological_impact' => $request->boolean('psychological_impact'),
            'impact_details' => $request->impact_details,
            'status' => 'new',
        ]);

        $caseId = $case->id;

        // تسجيل حدث إنشاء الحالة في case_events
        DB::connection('cases')->table('case_events')->insert([
            'case_id'       => $caseId,
            'user_id'       => null, // لأن الحالة أُنشئت من الزائر
            'event_type'    => 'created',
            'status_before' => null,
            'status_after'  => 'new',
            'description'   => 'تم إنشاء الحالة عبر نموذج الموقع',
            'metadata'      => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::connection('cases')->table('case_audit_logs')->insert([
            'action' => 'case_created',
            'case_id' => $caseId,
            'ip_hash' => hash('sha256', $request->ip()),
            'user_agent' => substr($request->userAgent(), 0, 255),
            'locale' => app()->getLocale(),
        ]);


        // تسجيل الموافقات في جدول case_consents
        $consents = [
            'documentation' => $request->has('agreed_to_document'),
            'external_referral' => $request->has('agreed_to_share'),
            'campaign_use' => $request->has('agreed_to_campaign'),
        ];

        foreach ($consents as $type => $granted) {
            DB::connection('cases')->table('case_consents')->insert([
                'case_id'    => $caseId,
                'consent_type' => $type,
                'granted'    => $granted ? 1 : 0,
                'granted_at' => $granted ? now() : null,
                'revoked_at' => null,
            ]);
        }

        // ??? ??????? (?? ?????)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('case_files');

                DB::connection('cases')->table('case_files')->insert([
                    'case_id' => $caseId,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        session(['case_created' => true]);
        // ????? ????? ?????? (??????? ??? ????)
        return redirect()
            ->route('case.success', app()->getLocale())
            ->with([
                'case_number' => $caseNumber,
                'followup_token' => $followupToken,
            ]);
    }

    public function followupForm()
    {
        return view(
            app()->getLocale() === 'en'
                ? 'case.followup_en'
                : 'case.followup'
        );
    }

    public function followupStore(Request $request)
    {
        $request->validate([
            'case_number' => 'required',
            'followup_token' => 'required',
            'update_description' => 'required',
        ]);

        $case = DB::connection('cases')->table('cases')
            ->where('case_number', $request->case_number)
            ->where('followup_token', $request->followup_token)
            ->first();

        if (!$case) {
            return back()->withErrors(['invalid' => 'بيانات المتابعة غير صحيحة']);
        }

        // إنشاء تحديث جديد
        $updateId = DB::connection('cases')->table('case_updates')->insertGetId([
            'case_id' => $case->id,
            'update_description' => $request->update_description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // حفظ ملفات التحديث
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('case_files');

                DB::connection('cases')->table('case_files')->insert([
                    'case_id' => $case->id,
                    'update_id' => $updateId,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        session(['case_updated' => true]);
        return redirect()->route('case.followup.success', app()->getLocale());
    }
}
