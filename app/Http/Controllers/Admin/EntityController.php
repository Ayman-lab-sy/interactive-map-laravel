<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntityController extends Controller
{
    /**
     * عرض قائمة الجهات
     */
    public function index(Request $request)
    {
        $tracks = [
            'UN_ACCOUNTABILITY'        => 'المساءلة الأممية',
            'SPECIAL_PROCEDURES'       => 'الإجراءات الخاصة',
            'NGO_LEGAL'                => 'المسار القانوني',
            'HUMANITARIAN_PROTECTION'  => 'الحماية الإنسانية',
        ];

        $entityTypes = [
            'UN'           => 'جهة أممية',
            'HumanRights'  => 'منظمة حقوقية',
            'Humanitarian' => 'جهة إنسانية',
            'NGO'          => 'منظمة غير حكومية',
        ];

        $entities = DB::connection('cases')
            ->table('entities')
            ->when($request->filled('track'), fn ($q) =>
                $q->where('referral_track', $request->track)
            )
            ->orderBy('entity_name')
            ->get();

        return view('admin.entities.index', [
            'entities'       => $entities,
            'tracks'         => $tracks,
            'entityTypes'    => $entityTypes,
            'selectedTrack'  => $request->track,
            'totalEntities'  => $entities->count(),
            'activeEntities' => $entities->where('is_active', true)->count(),
        ]);
    }

    /**
     * صفحة تعديل جهة
     */
    public function edit(int $entityId)
    {
        $entity = DB::connection('cases')
            ->table('entities')
            ->where('id', $entityId)
            ->first();

        abort_if(!$entity, 404);

        return view('admin.entities.edit', compact('entity'));
    }

    /**
     * حفظ التعديلات
     */
    public function update(Request $request, int $entityId)
    {
        $request->validate([
            'default_template'      => 'nullable|string|max:255',
            'accepts_family_data'  => 'required|boolean',
            'notes_internal'       => 'nullable|string',
        ]);

        DB::connection('cases')
            ->table('entities')
            ->where('id', $entityId)
            ->update([
                'default_template'     => $request->default_template,
                'accepts_family_data' => $request->accepts_family_data,
                'notes_internal'      => $request->notes_internal,
            ]);

        return redirect()
            ->route('admin.entities.index')
            ->with('success', 'تم تحديث إعدادات الجهة بنجاح.');
    }

    /**
     * تفعيل / تعطيل جهة
     */
    public function toggleActive(int $entityId)
    {
        $entity = DB::connection('cases')
            ->table('entities')
            ->where('id', $entityId)
            ->first();

        abort_if(!$entity, 404);

        DB::connection('cases')
            ->table('entities')
            ->where('id', $entityId)
            ->update([
                'is_active'  => !$entity->is_active,
            ]);

        return back()->with(
            'success',
            $entity->is_active
                ? 'تم تعطيل الجهة.'
                : 'تم تفعيل الجهة.'
        );
    }

}
