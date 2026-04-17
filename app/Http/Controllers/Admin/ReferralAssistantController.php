<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Services\ReferralDecisionService;
use App\Models\CaseDecision;
use Illuminate\Support\Facades\Auth;

class ReferralAssistantController extends Controller
{
    /**
     * Analyze case and return referral decision
     *
     * @param int $caseId
     * @return \Illuminate\Http\Response
     */
    public function analyze(Request $request, $case)
    {
        $caseId = (int) $case;

        // ضع هذا السطر أولًا للاختبار
        // dd('POST reached', $caseId);

        // 1️⃣ التحقق من الإدخال
        $validated = $request->validate([
            'legal_violation_type' => 'required|string',
        ]);

        // 2️⃣ جلب القرار المرتبط بالحالة
        $decision = CaseDecision::firstOrCreate(
            ['case_id' => $caseId],
            [
                'assistant_version' => 'v1.0',
                'decided_by' => Auth::id() ?? 1,
            ]
        );

        // 3️⃣ تحديث التصنيف القانوني (داخلي فقط)
        $decision->legal_violation_type = $validated['legal_violation_type'];

        // 4️⃣ تحضير مدخلات المساعد
        $violations = [$decision->legal_violation_type];

        $decisionInputs = $request->input('decision_inputs', []);

        // تحويل القيم إلى true/false
        $decisionInputs = collect($decisionInputs)
            ->map(fn ($v) => (bool) $v)
            ->toArray();

        $input = array_merge(
            ['violations' => $violations],
            $decisionInputs
        );


        // 5️⃣ تشغيل محرك القرار
        $result = app(ReferralDecisionService::class)->analyze($input);

        // 6️⃣ حفظ نتيجة المساعد
        $decision->decision_payload   = $result;
        $decision->decision_priority  = $result['priority'] ?? 'normal';
        $decision->assistant_version  = 'v1.0';
        $decision->decided_at         = now();
        $decision->decided_by         = Auth::id() ?? 1;
        $decision->decision_inputs = $decisionInputs;

        $decision->save();

        // 7️⃣ إعادة التوجيه لصفحة المساعد مع النتيجة
        return redirect()
            ->route('admin.case_assistant.show', $caseId)
            ->with('assistant_result', $result);
    }

    public function show($case)
    {
        $caseId = (int) $case;

        $case = CaseModel::findOrFail($caseId);

        $decision = CaseDecision::where('case_id', $caseId)->first();

        return view('admin.cases.assistant', [
            'case' => $case,
            'decision' => $decision,
        ]);
    }
}
