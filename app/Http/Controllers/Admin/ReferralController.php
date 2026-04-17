<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    public function create($caseId)
    {
        // جلب الحالة يدويًا (تفادي مشكلة Route Model Binding)
        $case = CaseModel::findOrFail($caseId);

        // جلب الجهات الفعّالة فقط
        $entities = DB::connection('cases')
            ->table('entities')
            ->where('is_active', 1)
            ->orderBy('entity_name')
            ->get();

        return view('admin.referrals.create', compact('case', 'entities'));
    }

    public function store(Request $request, $caseId)
    {
        $case = CaseModel::findOrFail($caseId);

        if ($case->status !== 'documented') {
            abort(403);
        }

        $request->validate([
            'entity_id' => 'required|integer',
        ]);

        // جلب الجهة مرة واحدة فقط
        $entity = DB::connection('cases')
            ->table('entities')
            ->where('id', $request->entity_id)
            ->where('is_active', 1)
            ->first();

        abort_if(!$entity, 404, 'Entity not found');

        // منع تكرار الإحالة لنفس الجهة
        $exists = DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('case_id', $case->id)
            ->where('entity_id', $entity->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'entity_id' => '⚠️ هذه الحالة أُحيلت سابقًا إلى نفس الجهة ولا يمكن تكرار الإحالة.'
            ]);
        }


        // تحديد نوع الإجراء الخاص
        $specialProcedureType = null;

        if ($entity->referral_track === 'SPECIAL_PROCEDURES') {

            // الجديد (المصدر الصحيح)
            if (!empty($entity->special_procedure_type)) {
                $specialProcedureType = $entity->special_procedure_type;

            // fallback للقديم (حتى لا ينكسر شيء)
            } else {
                switch ($entity->entity_name) {
                    case 'UN Special Rapporteur – Torture':
                        $specialProcedureType = 'TORTURE';
                        break;
                    case 'UN Working Group on Enforced or Involuntary Disappearances':
                        $specialProcedureType = 'ENFORCED_DISAPPEARANCE';
                        break;
                    case 'UN Working Group on Arbitrary Detention':
                        $specialProcedureType = 'ARBITRARY_DETENTION';
                        break;
                    case 'UN Special Rapporteur – Freedom of Opinion and Expression':
                        $specialProcedureType = 'FREEDOM_OF_EXPRESSION';
                        break;
                }
            }
        }
        
        $ngoType = null;

        if ($entity->referral_track === 'NGO_LEGAL') {

            if ($entity->entity_name === 'Amnesty International') {
                $ngoType = 'AMNESTY';

            } elseif ($entity->entity_name === 'Human Rights Watch') {
                $ngoType = 'HRW';
            }
        }
        
        $humanitarianType = null;
        $unAccountabilityType = null;

        if ($entity->referral_track === 'HUMANITARIAN_PROTECTION') {
            // نربط النوع حسب الجهة
            if (in_array($entity->entity_name, ['ICRC', 'UNHCR'])) {
                $humanitarianType = $entity->entity_name; 
                // ICRC أو UNHCR
            }        
        }

        if ($entity->referral_track === 'UN_ACCOUNTABILITY') {
            if ($entity->entity_name === 'OHCHR') {
                $unAccountabilityType = 'OHCHR';
            }
        }

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

        return redirect()
            ->route('admin.referrals.index')
            ->with('success', 'تم إنشاء الإحالة بنجاح.');
    }

}
