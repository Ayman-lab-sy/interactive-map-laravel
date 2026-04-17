<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssistantKnowledgeAdminController extends Controller
{
    /**
     * Dashboard: عرض جميع Entries (النوايا) مع إحصائياتها
     */
    public function index(Request $request)
    {
        $entries = DB::connection('assistant')
            ->table('assistant_entries as e')
            ->join('assistant_categories as c', 'c.id', '=', 'e.category_id')
            ->leftJoin('assistant_answers as a', 'a.entry_id', '=', 'e.id')
            ->select(
                'e.id',
                'e.tone',
                'c.name as category',

                DB::raw('COUNT(a.id) as answers_count'),
                DB::raw('SUM(CASE WHEN a.is_active = 1 THEN 1 ELSE 0 END) as active_answers_count'),
                DB::raw('SUM(CASE WHEN a.is_active = 0 THEN 1 ELSE 0 END) as inactive_answers_count'),
                DB::raw('MAX(CASE WHEN a.is_primary = 1 THEN 1 ELSE 0 END) as has_primary'),
                DB::raw('MAX(a.updated_at) as last_update')
            )
            ->groupBy('e.id', 'e.tone', 'c.name')
            ->orderBy('c.name')
            ->orderBy('e.tone')
            ->get()
            ->map(function ($entry) {
                if ($entry->answers_count == 0) {
                    $entry->status = 'empty';
                } elseif ($entry->active_answers_count == 0) {
                    $entry->status = 'inactive';
                } elseif (!$entry->has_primary) {
                    $entry->status = 'no_primary';
                } elseif ($entry->answers_count < 2) {
                    $entry->status = 'weak';
                } else {
                    $entry->status = 'healthy';
                }

                return $entry;
            });

        // Filters
        if ($filter = $request->get('filter')) {
            $entries = $entries->filter(function ($entry) use ($filter) {
                return match ($filter) {
                    'no_primary' => !$entry->has_primary,
                    'inactive'   => $entry->active_answers_count == 0,
                    'weak'       => $entry->answers_count < 2,
                    default      => true,
                };
            });
        }

        // KPIs
        $stats = DB::connection('assistant')
            ->table('assistant_entries as e')
            ->leftJoin('assistant_answers as a', 'a.entry_id', '=', 'e.id')
            ->select(
                DB::raw('COUNT(DISTINCT e.id) as total_entries'),
                DB::raw('COUNT(a.id) as total_answers'),
                DB::raw('SUM(CASE WHEN a.is_active = 1 THEN 1 ELSE 0 END) as active_answers'),
                DB::raw('SUM(CASE WHEN NOT EXISTS (
                    SELECT 1 FROM assistant_answers ap
                    WHERE ap.entry_id = e.id AND ap.is_primary = 1
                ) THEN 1 ELSE 0 END) as entries_without_primary')
            )
            ->first();

        return view('admin.assistant.knowledge.index', compact('entries', 'stats'));
    }

    /**
     * صفحة تعديل إجابات Entry واحدة (نية واحدة)
     */
    public function edit(int $entryId)
    {
        $entry = DB::connection('assistant')
            ->table('assistant_entries as e')
            ->join('assistant_categories as c', 'c.id', '=', 'e.category_id')
            ->where('e.id', $entryId)
            ->select(
                'e.id',
                'e.tone',
                'c.name as category'
            )
            ->first();
        
        $keywords = DB::connection('assistant')
            ->table('assistant_keywords')
            ->where('entry_id', $entryId)
            ->orderBy('id')
            ->pluck('keyword')
            ->toArray();

        abort_if(!$entry, 404, 'النية غير موجودة');

        $answers = DB::connection('assistant')
            ->table('assistant_answers')
            ->where('entry_id', $entryId)
            ->orderBy('id')
            ->get();

        return view('admin.assistant.knowledge.edit', [
            'entry'   => $entry,
            'answers' => $answers,
            'keywords' => $keywords,
        ]);   
    }

    /**
     * حفظ تعديلات إجابات Entry واحدة
     */
    public function update(Request $request, int $entryId)
    {
        $primaryId     = $request->input('primary_answer_id');
        $answersText   = $request->input('answers', []);
        $answersActive = $request->input('active', []);

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ تحديث نصوص الإجابات + حالة التفعيل
        |--------------------------------------------------------------------------
        */
        foreach ($answersText as $answerId => $text) {
            DB::connection('assistant')
                ->table('assistant_answers')
                ->where('id', $answerId)
                ->where('entry_id', $entryId)
                ->update([
                    'answer_text' => trim($text),
                    'is_active'   => array_key_exists($answerId, $answersActive),
                    'updated_at'  => now(),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ إعادة تعيين Primary لكل إجابات هذا Entry
        |--------------------------------------------------------------------------
        */
        DB::connection('assistant')
            ->table('assistant_answers')
            ->where('entry_id', $entryId)
            ->update(['is_primary' => 0]);

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ تعيين Primary جديدة (إن وُجدت)
        |--------------------------------------------------------------------------
        */
        if ($primaryId) {
            DB::connection('assistant')
                ->table('assistant_answers')
                ->where('id', $primaryId)
                ->where('entry_id', $entryId)
                ->update([
                    'is_primary' => 1,
                    'updated_at' => now(),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ إضافة إجابة جديدة (اختياري)
        |--------------------------------------------------------------------------
        */
        $newAnswer = trim($request->input('new_answer', ''));

        if ($newAnswer !== '' && mb_strlen($newAnswer) >= 20) {
            $newId = DB::connection('assistant')
                ->table('assistant_answers')
                ->insertGetId([
                    'entry_id'    => $entryId,
                    'answer_text' => $newAnswer,
                    'is_active'   => $request->boolean('new_active', true),
                    'is_primary'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

            if ($request->boolean('new_primary')) {
                DB::connection('assistant')
                    ->table('assistant_answers')
                    ->where('entry_id', $entryId)
                    ->update(['is_primary' => 0]);

                DB::connection('assistant')
                    ->table('assistant_answers')
                    ->where('id', $newId)
                    ->update(['is_primary' => 1]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ تحديث الكلمات المفتاحية — SAFE SYNC
        |--------------------------------------------------------------------------
        */
        $rawKeywords = $request->input('keywords', '');

        $normalizedKeywords = collect(explode(',', $rawKeywords))
            ->map(fn ($k) => mb_strtolower(trim($k)))
            ->filter()
            ->unique()
            ->values();

        // الكلمات الحالية في DB
        $existingKeywords = DB::connection('assistant')
            ->table('assistant_keywords')
            ->where('entry_id', $entryId)
            ->pluck('keyword')
            ->toArray();

        // حذف الكلمات التي أُزيلت من الواجهة
        DB::connection('assistant')
            ->table('assistant_keywords')
            ->where('entry_id', $entryId)
            ->whereNotIn('keyword', $normalizedKeywords)
            ->delete();

        // إضافة الكلمات الجديدة فقط
        foreach ($normalizedKeywords as $kw) {
            if (!in_array($kw, $existingKeywords, true)) {
                DB::connection('assistant')
                    ->table('assistant_keywords')
                    ->insert([
                        'entry_id'   => $entryId,
                        'keyword'    => $kw,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        return redirect()
            ->route('admin.assistant.knowledge.edit', $entryId)
            ->with('success', '✅ تم حفظ التعديلات بنجاح');
    }
}
