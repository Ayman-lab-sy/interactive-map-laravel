<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralIndexController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id',
                'c.case_number',
                'e.entity_name',
                'e.entity_type',
                'e.referral_track as referral_track',
                'r.ngo_type', 
                'r.special_procedure_type',
                'r.humanitarian_type',
                'r.un_accountability_type',
                'r.referral_status',
                'r.created_at',
            ]);

        // ✅ الإضافة الوحيدة (هنا بالضبط)
        if ($request->filled('status')) {
            $query->where('r.referral_status', $request->status);
        }

        $referrals = $query
            ->orderBy('r.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.referrals.index', compact('referrals'));
    }
}

