<?php

namespace App\Services\Assistant;

use Illuminate\Support\Facades\DB;

class AssistantKnowledgeService
{
    public function convert(
        int $unansweredId,
        string $categoryName,
        string $categoryMode,
        string $tone,
        string $tags,
        array $keywords,
        ?string $answer,
        string $adminEmail
    ): void {
        DB::connection('assistant')->transaction(function () use (
            $unansweredId,
            $categoryName,
            $categoryMode,
            $tone,
            $tags,
            $keywords,
            $answer,
            $adminEmail
        ) {

            // 1) Category (FIXED LOGIC)
            if ($categoryMode === 'existing') {
                $categoryId = DB::connection('assistant')
                    ->table('assistant_categories')
                    ->where('name', $categoryName)
                    ->value('id');

                abort_if(!$categoryId, 409, 'التصنيف المختار غير موجود');
            }

            if ($categoryMode === 'new') {
                $exists = DB::connection('assistant')
                    ->table('assistant_categories')
                    ->where('name', $categoryName)
                    ->exists();

                abort_if($exists, 409, 'التصنيف موجود مسبقًا');

                $categoryId = DB::connection('assistant')
                    ->table('assistant_categories')
                    ->insertGetId([
                        'name' => $categoryName,
                        'slug' => \Str::slug($categoryName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // 2) Entry (Tone / Intent) — Arabic Normalized
            $normalizedTone = $this->normalizeArabic($tone);

            // 🔍 البحث عن Entry موجودة بنفس النية (لغويًا)
            $existingEntry = DB::connection('assistant')
                ->table('assistant_entries')
                ->where('category_id', $categoryId)
                ->get()
                ->first(function ($entry) use ($normalizedTone) {
                    return $this->normalizeArabic($entry->tone) === $normalizedTone;
                });

            if ($existingEntry) {
                // ✅ استخدم Entry الموجودة
                $entryId = $existingEntry->id;
            } else {
                // ➕ أنشئ Entry جديدة
                $entryId = DB::connection('assistant')
                    ->table('assistant_entries')
                    ->insertGetId([
                        'category_id' => $categoryId,
                        'tone'        => $tone,
                        'tags'        => json_encode(
                            array_values(array_filter(array_map('trim', preg_split('/[،,]+/u', $tags)))),
                            JSON_UNESCAPED_UNICODE
                        ),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
            }

            // 🔍 حفظ الكلمات المفتاحية القديمة (لأجل Audit فقط)
            $oldKeywords = DB::connection('assistant')
                ->table('assistant_keywords')
                ->where('entry_id', $entryId)
                ->pluck('keyword')
                ->toArray();

            // 3) Keywords — FINAL SAFE LOGIC (REPLACE)
            $normalizedKeywords = collect($keywords)
                ->map(fn ($k) => $this->normalizeArabic($k))
                ->filter()
                ->unique()
                ->values();

            // 🧮 حساب الفروقات (للسجل فقط)
            $addedKeywords   = array_values(array_diff($normalizedKeywords->toArray(), $oldKeywords));
            $removedKeywords = array_values(array_diff($oldKeywords, $normalizedKeywords->toArray()));


            // حذف كل الكلمات القديمة لهذا entry
            DB::connection('assistant')
                ->table('assistant_keywords')
                ->where('entry_id', $entryId)
                ->delete();

            // إدخال الحالة النهائية فقط
            foreach ($normalizedKeywords as $kw) {
                DB::connection('assistant')
                    ->table('assistant_keywords')
                    ->insert([
                        'entry_id'   => $entryId,
                        'keyword'    => $kw,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // 4) Answer (اختياري)
            if ($answer) {
                DB::connection('assistant')
                    ->table('assistant_answers')
                    ->insert([
                        'entry_id'   => $entryId,
                        'answer_text'=> trim($answer),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // 5) Update unanswered
            DB::connection('assistant')
                ->table('assistant_unanswered')
                ->where('id', $unansweredId)
                ->update([
                    'status'     => 'converted',
                    'updated_at' => now(),
                ]);

            // 6) Audit
            DB::connection('assistant')
                ->table('assistant_audit_logs')
                ->insert([
                    'action'        => 'convert',
                    'question_id'   => $unansweredId,
                    'entry_id'      => $entryId,
                    'category_name' => $categoryName,
                    'payload' => json_encode([
                        'category' => $categoryName,
                        'tone'     => $tone,
                        'tags'     => array_values(array_filter(array_map(
                            'trim',
                            preg_split('/[،,]+/u', $tags)
                        ))),
                        // 🔑 تغييرات الكلمات المفتاحية
                        'keywords_added'   => $addedKeywords,
                        'keywords_removed' => $removedKeywords,
                        // 💬 هل أضيفت إجابة؟
                        'answer_added' => $answer ? true : false,
                        // 🔗 ربط بالسؤال الأصلي
                        'source_question_id' => $unansweredId,
                    ], JSON_UNESCAPED_UNICODE),
                    'admin_email'  => $adminEmail,
                    'created_at'   => now(),
                ]);
        });
    }
    private function normalizeArabic(string $text): string
    {
        $text = preg_replace('/[ًٌٍَُِّْ]/u', '', $text); // إزالة التشكيل
        $text = str_replace(['إ','أ','آ'], 'ا', $text);
        $text = str_replace('ى', 'ي', $text);
        $text = str_replace('ؤ', 'و', $text);
        $text = str_replace('ئ', 'ي', $text);
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return mb_strtolower($text);
    }
}
