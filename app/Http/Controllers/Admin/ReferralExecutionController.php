<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\CaseDecision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReferralExecutionController extends Controller
{
    public function execute(Request $request, $case)
    {
        $caseId = (int) $case;

        // 1) جلب الحالة
        $caseModel = CaseModel::findOrFail($caseId);

        // 2) جلب القرار
        $decision = CaseDecision::where('case_id', $caseId)->first();

        if (!$decision || empty($decision->decision_payload)) {
            return back()->withErrors('لا يوجد قرار صالح لتنفيذ الإحالات.');
        }

        // منع إعادة التنفيذ
        if (!empty($decision->executed_at)) {
            return back()->withErrors('⚠️ تم تنفيذ الإحالات لهذه الحالة مسبقًا.');
        }

        // 3) استخراج القرار
        $payload = $decision->decision_payload;

        $mandatory  = $payload['mandatory']  ?? [];
        $supporting = $payload['supporting'] ?? [];

        if (empty($mandatory) && empty($supporting)) {
            return back()->withErrors('القرار لا يحتوي على أي إحالات.');
        }

        $createdCount = 0;

        // 4) تنفيذ داخل Transaction (أمان)
        DB::connection('cases')->transaction(function () use (
            $caseId,
            $mandatory,
            $supporting,
            &$createdCount
        ) {

            $entitiesToCreate = array_merge($mandatory, $supporting);

            foreach ($entitiesToCreate as $entityName) {

                // 1) نبحث عن entity بنفس منطق النظام الحالي
                $entity = DB::connection('cases')
                    ->table('entities')
                    ->where('is_active', 1)
                    ->where('entity_name', $entityName)
                    ->first();

                if (!$entity) {
                    continue; // الجهة غير موجودة أو غير مفعّلة
                }

                // 2) منع التكرار
                $exists = DB::connection('cases')
                    ->table('case_entity_referrals')
                    ->where('case_id', $caseId)
                    ->where('entity_id', $entity->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // 3) نفس منطق الكنترولر اليدوي
                $specialProcedureType = null;
                $ngoType = null;
                $humanitarianType = null;
                $unAccountabilityType = null;

                if ($entity->referral_track === 'SPECIAL_PROCEDURES') {
                    $specialProcedureType = $entity->special_procedure_type;
                }

                if ($entity->referral_track === 'NGO_LEGAL') {
                    if ($entity->entity_name === 'Amnesty International') {
                        $ngoType = 'AMNESTY';
                    } elseif ($entity->entity_name === 'Human Rights Watch') {
                        $ngoType = 'HRW';
                    }
                }

                if ($entity->referral_track === 'HUMANITARIAN_PROTECTION') {
                    if (in_array($entity->entity_name, ['ICRC', 'UNHCR'])) {
                        $humanitarianType = $entity->entity_name;
                    }
                }

                if ($entity->referral_track === 'UN_ACCOUNTABILITY') {
                    if ($entity->entity_name === 'OHCHR') {
                        $unAccountabilityType = 'OHCHR';
                    }
                }

                // 4) إنشاء الإحالة (نفس الأعمدة تمامًا)
                DB::connection('cases')
                    ->table('case_entity_referrals')
                    ->insert([
                        'case_id' => $caseId,
                        'entity_id' => $entity->id,
                        'referral_status' => 'prepared',
                        'special_procedure_type' => $specialProcedureType,
                        'ngo_type' => $ngoType,
                        'humanitarian_type' => $humanitarianType,
                        'un_accountability_type' => $unAccountabilityType,
                        'created_at' => now(),
                    ]);

                $createdCount++;
            }
        });

        // 5) وسم القرار كمنفّذ (اختياري الآن)
        $decision->executed_at = now();
        $decision->executed_by = Auth::id() ?? 1;
        $decision->save();

        return redirect()
            ->route('admin.cases.show', $caseId)
            ->with('success', 
                " تم إنشاء {$createdCount} إحالة تلقائيًا.
                يمكنك الآن العودة للحالة أو الانتقال إلى قائمة الإحالات لاستكمال العمل."
            );
    }
}
