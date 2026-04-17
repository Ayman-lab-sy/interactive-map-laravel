<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $casesCount = \App\Models\CaseModel::count();

        $newCasesCount = CaseModel::where('status', 'new')->count();

        $underReviewCount = CaseModel::where('status', 'under_review')->count();

        $overdueCasesCount = DB::connection('cases')
            ->table('cases as c')
            ->where('c.status', 'under_review')
            ->whereRaw("
                DATEDIFF(
                    NOW(),
                    COALESCE(
                        (
                            SELECT ce.created_at
                            FROM case_events ce
                            WHERE ce.case_id = c.id
                              AND ce.event_type = 'moved_to_under_review'
                            ORDER BY ce.created_at DESC
                            LIMIT 1
                        ),
                        c.updated_at
                    )
                ) > 7
            ")
            ->count();

        $referralsCount = DB::connection('cases')
            ->table('case_entity_referrals')
            ->count();

        $referralsByStatus = DB::connection('cases')
            ->table('case_entity_referrals')
            ->whereIn('referral_status', [
                'prepared',
                'ready_for_generation',
                'generated'
           ])
            ->select('referral_status', DB::raw('COUNT(*) as total'))
            ->groupBy('referral_status')
            ->pluck('total', 'referral_status');

        $recentItems = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->select([
                'r.id as referral_id',
                'e.entity_name',
                'e.referral_track',
                'r.ngo_type',
                'r.special_procedure_type',
                'r.humanitarian_type',
                'r.un_accountability_type',
                'r.referral_status',
                'r.created_at',
                'r.generated_at',
            ])
            ->orderByDesc(DB::raw('COALESCE(r.generated_at, r.created_at)'))
            ->limit(10)
            ->get();

        $reportsCount = DB::connection('cases')
            ->table('case_entity_referrals')
            ->where('referral_status', 'generated')
            ->count();

        return view('admin.dashboard', compact(
            'casesCount',
            'newCasesCount',
            'underReviewCount',
            'overdueCasesCount', 
            'referralsCount',
            'reportsCount',
            'referralsByStatus',
            'recentItems'
        ));
    }
}
