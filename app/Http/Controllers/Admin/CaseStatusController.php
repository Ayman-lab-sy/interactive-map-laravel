<?php

namespace App\Http\Controllers\Admin;

use App\Models\CaseModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\CaseEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class CaseStatusController extends Controller
{
    public function toUnderReview(CaseModel $case)
    {
        $this->authorize('moveToUnderReview', $case);

        $statusBefore = $case->status;

        $case->update([
            'status' => 'under_review'
        ]);

        CaseEvent::create([
            'case_id'      => $case->id,
            'user_id'      => Auth::id(),
            'event_type'   => 'moved_to_under_review',
            'status_before'=> $statusBefore,
            'status_after' => 'under_review',
            'description'  => null,
            'metadata'     => null,
        ]);

        return back()->with('success', 'تم نقل الحالة للمراجعة');
    }


    public function toDocumented(Request $request, CaseModel $case)
    {
        $this->authorize('markAsDocumented', $case);

        // لا يمكن التوثيق إلا من قيد المراجعة
        if ($case->status !== 'under_review') {
            return back()->with('error', 'لا يمكن توثيق حالة ليست قيد المراجعة.');
        }

        // التحقق من وجود الملاحظة
        $request->validate([
            'decision_note' => 'required|string|min:10',
        ]);

        $statusBefore = $case->status;

        $case->update([
            'status' => 'documented'
        ]);

        CaseEvent::create([
            'case_id'       => $case->id,
            'user_id'       => Auth::id(),
            'event_type'    => 'documented',
            'status_before' => $statusBefore,
            'status_after'  => 'documented',
            'description'   => $request->decision_note,
            'metadata'      => null,
        ]);

        return back()->with('success', 'تم توثيق الحالة بنجاح.');
    }


    public function archive(Request $request, CaseModel $case)
    {
        $this->authorize('archive', $case);

        // لا يمكن الأرشفة إلا من قيد المراجعة
        if ($case->status !== 'under_review') {
            return back()->with('error', 'لا يمكن أرشفة حالة ليست قيد المراجعة.');
        }

        // تحقق من وجود سبب
        $request->validate([
            'archive_reason' => 'required|string|min:10',
        ]);

        $statusBefore = $case->status;

        $case->update([
            'status' => 'archived'
        ]);

        CaseEvent::create([
            'case_id'       => $case->id,
            'user_id'       => Auth::id(),
            'event_type'    => 'archived',
            'status_before' => $statusBefore,
            'status_after'  => 'archived',
            'description'   => $request->archive_reason,
            'metadata'      => null,
        ]);

        return back()->with('success', 'تمت أرشفة الحالة بنجاح.');
    }

    public function addNote(Request $request, CaseModel $case)
    {
        if (!Auth::user()->canReview()) {
            abort(403);
        }

        // يسمح فقط أثناء قيد المراجعة
        if ($case->status !== 'under_review') {
            return back()->with('error', 'يمكن إضافة ملاحظات فقط أثناء قيد المراجعة.');
        }

        $request->validate([
            'note_text' => 'required|string|min:5',
        ]);

        CaseEvent::create([
            'case_id'       => $case->id,
            'user_id'       => Auth::id(),
            'event_type'    => 'note_added',
            'status_before' => $case->status,
            'status_after'  => $case->status,
            'description'   => $request->note_text,
            'metadata'      => null,
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة بنجاح.');
    }

}
