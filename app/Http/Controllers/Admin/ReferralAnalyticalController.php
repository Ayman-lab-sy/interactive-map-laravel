<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralAnalyticalController extends Controller
{
    public function store(Request $request, $id)
    {
        // تحقق بسيط (بدون تعقيد)
        $request->validate([
            'general_location_en'      => 'nullable|string|max:255',
            'incident_timeframe_en'    => 'nullable|string|max:255',
            'psychosocial_impact_en'   => 'nullable|string',
        ]);

        // تحديث الإحالة نفسها (case_entity_referrals)
        DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('id', $id)
            ->update([
                'general_location_en'    => $request->general_location_en,
                'incident_timeframe_en'  => $request->incident_timeframe_en,
                'psychosocial_impact_en' => $request->psychosocial_impact_en,
            ]);

        // Audit log
        DB::connection('cases')->table('case_referral_audits')->insert([
            'referral_id' => $id,
            'action'      => 'analytical_content_saved',
            'user_id'     => auth()->id(),
            'created_at'  => now(),
        ]);

        return back()->with('success', 'تم حفظ المحتوى التحليلي بنجاح.');
    }
}
