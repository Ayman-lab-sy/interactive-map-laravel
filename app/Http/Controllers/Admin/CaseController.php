<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\AuditLog;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseModel::query()
            ->select('cases.*')
            ->addSelect([
            'review_days' => DB::connection('cases')
                    ->table('case_events as ce')
                    ->selectRaw("DATEDIFF(NOW(), ce.created_at)")
                    ->whereColumn('ce.case_id', 'cases.id')
                    ->where('ce.event_type', 'moved_to_under_review')
                    ->orderBy('ce.created_at', 'desc')
                    ->limit(1)
            ])
            ->addSelect([
                'referrals_count' => DB::connection('cases')
                    ->table('case_entity_referrals')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('case_entity_referrals.case_id', 'cases.id')
            ])
            ->orderBy('cases.created_at', 'desc');


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('overdue')) {
            $query->where('status', 'under_review')
                ->whereRaw("
                    DATEDIFF(
                        NOW(),
                        COALESCE(
                            (
                                SELECT ce.created_at
                                FROM case_events ce
                                WHERE ce.case_id = cases.id
                                    AND ce.event_type = 'moved_to_under_review'
                                ORDER BY ce.created_at DESC
                                LIMIT 1
                            ),
                            cases.updated_at
                        )
                    ) > 7
                ");
        }

        if ($request->filled('q')) {
            $search = trim($request->q);

            $normalized = mb_strtolower($search);
            $hash = hash('sha256', $normalized);

            $query->where(function ($q) use ($search, $hash) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('full_name_hash', $hash);
            });
        }


        $cases = $query->paginate(20)->withQueryString();

        $stats = [
            'all' => CaseModel::count(),
            'new' => CaseModel::where('status', 'new')->count(),
            'under_review' => CaseModel::where('status', 'under_review')->count(),
            'ready_for_export' => CaseModel::where('status', 'ready_for_export')->count(),
            'archived' => CaseModel::where('status', 'archived')->count(),
        ];

        return view('admin.cases.index', compact('cases', 'stats'));
    }

    /**
     * عرض صفحة مراجعة الحالة
     */
    public function show($id)
    {
        $case = CaseModel::with('events.user')->findOrFail($id);
    
        $files = DB::connection('cases')
            ->table('case_files')
            ->where('case_id', $case->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $updates = DB::connection('cases')
            ->table('case_updates')
            ->where('case_id', $case->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $referrals = DB::connection('cases')
            ->table('case_entity_referrals as r')
            ->leftJoin('entities as e', 'e.id', '=', 'r.entity_id')
            ->select(
                'r.*',
                'e.entity_name as entity_name',
                'e.referral_track as referral_track'
            )
            ->where('r.case_id', $case->id)
            ->orderBy('r.created_at', 'desc')
            ->get();

        $securityLogs = \App\Models\AuditLog::with('user')
            ->where('case_id', $case->id)
            ->where('action_type', 'FIELD_REVEALED')
            ->orderBy('created_at', 'desc')
            ->get();
             
        return view('admin.cases.show', [
            'case' => $case,
            'files' => $files,
            'updates' => $updates,
            'referrals' => $referrals,
            'securityLogs' => $securityLogs,
        ]);
    }

    public function submitReview(Request $request, $id)
    {
        abort_unless(Auth::user()->canReview(), 403);

        $request->validate([
            'verification_level' => 'required|in:unverified,partial,verified',
            'approved_for_export' => 'nullable|boolean',
            'internal_note' => 'nullable|string',
        ]);

        $case = CaseModel::findOrFail($id);

        DB::connection('cases')->transaction(function () use ($request, $case) {

            // القرار القانوني الوحيد
            $canBeExported =
                in_array($request->verification_level, ['partial', 'verified'])
                && $request->boolean('approved_for_export');

            $case->status = $canBeExported
                ? 'ready_for_export'
                : 'under_review';

            $case->save();

            // ملاحظة داخلية
            if ($request->filled('internal_note')) {
                DB::connection('cases')->table('case_internal_notes')->insert([
                    'case_id' => $case->id,
                    'added_by_user_id' => Auth::id(),
                    'note_text' => $request->internal_note,
                    'created_at' => now(),
                ]);
            }

            // Audit Log
            DB::connection('cases')->table('audit_logs')->insert([
                'case_id'   => $case->id,
                'user_id'   => Auth::id(),
                'user_role' => Auth::user()->systemRole(),
                'action_type' => 'CASE_REVIEW_DECISION',
                'action_context' => json_encode([
                    'verification_level' => $request->verification_level,
                    'approved_for_export' => $request->boolean('approved_for_export'),
                    'final_status' => $case->status,
                ]),
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.cases.index', ['status' => $case->status])
            ->with('success', 'تم حفظ قرار المراجعة بنجاح.');
    }

    public function revealField(Request $request, $id)
    {
        if (!auth()->user()->canRevealSensitiveFields()) {
            abort(403);
        }
        
        $request->validate([
            'field' => 'required|string'
        ]);

        $allowedFields = [
            'full_name',
            'email',
            'spouse_name',
            'children',
            'threat_description',
            'impact_details',
        ];

        if(!in_array($request->field, $allowedFields)) {
            abort(403);
        }

        $case = CaseModel::findOrFail($id);

        $value = $case->{$request->field};

        if ($request->field === 'children' && $value) {
            $decoded = json_decode($value, true);
            $value = collect($decoded)
                ->map(fn($c) => ($c['name'] ?? '—') . ' (' . ($c['age'] ?? '?') . ')')
                ->implode('، ');
        }

        DB::connection('cases')->table('audit_logs')->insert([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->systemRole(),
            'action_type' => 'FIELD_REVEALED',
            'action_context' => json_encode([
                'field' => $request->field,
                'case_number' => $case->case_number,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]),
            'previous_value' => null,
            'new_value' => null,
            'reason' => null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'value' => $value,
        ]);
    }

}
