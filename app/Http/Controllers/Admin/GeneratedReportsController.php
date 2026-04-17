<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GeneratedReportsController extends Controller
{
    public function index(Request $request)
    {
        $reports = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->join('cases as c', 'c.id', '=', 'r.case_id')
            ->join('entities as e', 'e.id', '=', 'r.entity_id')
            ->leftJoin('case_referral_downloads as d', 'd.referral_id', '=', 'r.id')
            ->leftJoin(DB::raw('h96737_alawite.users as u'), 'u.id', '=', 'r.generated_by')
            ->whereIn('r.referral_status', ['generated', 'exported'])
            ->select([
                'r.id as referral_id',
                'c.case_number',
                'e.entity_name',
                'e.referral_track as referral_track',
                'r.special_procedure_type',
                'r.humanitarian_type',
                'r.ngo_type',
                'r.un_accountability_type',
                'r.referral_status',
                'r.generated_at',
                'u.name as generated_by_name',
                DB::raw('COUNT(d.id) as downloads_count'),
                DB::raw('MAX(d.downloaded_at) as last_downloaded_at'),
                DB::raw("
                    EXISTS (
                        SELECT 1
                        FROM case_referral_narratives n
                        WHERE n.referral_id = r.id
                          AND n.language = 'en'
                    ) as has_legal_narrative
                "),
            ])
            ->groupBy(
                'r.id',
                'c.case_number',
                'e.entity_name',
                'e.referral_track',
                'r.special_procedure_type',
                'r.humanitarian_type',
                'r.ngo_type',
                'r.un_accountability_type',
                'r.referral_status',
                'r.generated_at',
                'u.name'
            )
            ->orderByDesc('r.generated_at')
            ->paginate(20);

        return view('admin.reports.generated', compact('reports'));
    }
}
