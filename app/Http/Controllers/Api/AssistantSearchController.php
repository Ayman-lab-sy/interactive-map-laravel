<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Assistant\UnansweredRepository;
use App\Services\Assistant\IntentResolver;

class AssistantSearchController extends Controller
{
    public function search(Request $request)
    {
        \Log::info('SEARCH_DEBUG', [
            'raw' => $request->input('question'),
            'hex' => bin2hex($request->input('question')),
        ]);
        $questionNormalized = $this->normalizeArabic($request->input('question', ''));
        // 0: استقبال وتنظيف السؤال
        $questionRaw = $request->input('question', '');
        $question = $this->normalizeArabic($questionRaw);
        $intentResolver = new IntentResolver();
        $intent = $intentResolver->resolve($question);

        // 🔴 Intent شعوري – خروج فوري
        if ($intent === 'frustration') {
            return response()->json([
                'ok' => true,
                'answer' =>
                    "نسمعك، وشعورك مفهوم تمامًا.\n\n"
                    ."كثير من الناس مرّوا بنفس الإحساس قبل أن يقرروا الكلام، وهذا بحد ذاته خطوة شجاعة.\n\n"
                    ."إذا حابب، اكتب لنا باختصار ماذا حصل معك أو ما الذي تتمنى أن يتغيّر، وسنقرأ رسالتك شخصيًا ونرد عليك حسب الأولوية.\n\n"
                    ."أنت لست وحدك، وصوتك مهم.",
                'meta' => [
                    'matched' => 'frustration'
                ]
            ]);
        }

        $intentToCategory = [
            'emergency'    => 'مساعدة طارئة',
            'legal'        => 'دعم قانوني',
            'humanitarian' => 'مساعدة انسانية',
            'documentation'=> 'توثيق',
            'definition'   => 'تعريف المنظمة',
            'join'         => 'الانضمام',
        ];
        
        $intentCategory = null;

        if ($intent && isset($intentToCategory[$intent])) {
            $intentCategory = $intentToCategory[$intent];
            \Log::info('INTENT_HINT', ['intent' => $intentCategory]);
        }


        if ($question === '') {
            return response()->json([
                'ok' => true,
                'answer' => 'لا يوجد رد',
                'meta' => ['categories' => []]
            ]);
        }

        $words = preg_split('/\s+/u', $question);

        // 🔒 فلتر جودة السؤال (منع الأسئلة العشوائية)
        $meaningfulWords = array_filter($words, function ($w) {
            return mb_strlen($w) >= 3 && !$this->isStopWord($w);
        });

        $isLongQuestion = count($meaningfulWords) >= 6;

        // 1️⃣ Exact Match – SQL is the judge
        $exactMatch = DB::connection('assistant')
            ->table('assistant_keywords as k')
            ->join('assistant_entries as e', 'e.id', '=', 'k.entry_id')
            ->join('assistant_categories as c', 'c.id', '=', 'e.category_id')
            ->join('assistant_answers as a', function ($join) {
                $join->on('a.entry_id', '=', 'e.id')
                     ->where('a.is_active', 1);
            })
            ->select(
                'k.keyword',
                'a.answer_text',
                'c.name as category'
            )
            ->get();

        foreach ($exactMatch as $row) {
            if (
                !$isLongQuestion &&
                $this->normalizeArabic($row->keyword) === $questionNormalized
            ) {
                return response()->json([
                    'ok' => true,
                    'answer' => $row->answer_text,
                    'meta' => [
                        'categories' => [$row->category],
                        'matched' => 'exact'
                    ]
                ]);
            }
        }


        // 🟡 Stage 0.5: Keyword CONTAINS match (SMART)
        $containMatch = DB::connection('assistant')
            ->table('assistant_keywords as k')
            ->join('assistant_entries as e', 'e.id', '=', 'k.entry_id')
            ->join('assistant_categories as c', 'c.id', '=', 'e.category_id')
            ->join('assistant_answers as a', function ($join) {
                $join->on('a.entry_id', '=', 'e.id')
                     ->where('a.is_active', 1);
            })
            ->select('k.keyword', 'a.answer_text', 'c.name as category')
            ->get()
            ->filter(function ($row) use ($question) {

                $kw = $this->normalizeArabic($row->keyword);

                // 1) تجاهل keywords القصيرة أو العامة
                if (mb_strlen($kw) < 6) {
                    return false;
                }

                // 2) تطابق كجملة داخل السؤال (حدود كلمات)
                return preg_match(
                    '/\b' . preg_quote($kw, '/') . '\b/u',
                    $question
                );
            })
            ->sortByDesc(fn ($row) => mb_strlen($row->keyword))
            ->first();

        if ($containMatch && !$isLongQuestion) {
            return response()->json([
                'ok' => true,
                'answer' => $containMatch->answer_text,
                'meta' => [
                    'categories' => [$containMatch->category],
                    'matched' => 'contain'
                ]
            ]);
        }

        if (
            count($meaningfulWords) < 2 &&
            !$this->isIntroductoryQuestion($words)
        ) {

            // تسجيل كسؤال غير مفهوم
            $unansweredRepo = new UnansweredRepository();
            $unansweredRepo->log(
                $questionRaw,
                $question
            );

            return response()->json([
                'ok' => true,
                'answer' => 'لا يوجد رد',
                'meta' => ['categories' => []]
            ]);
        }

        // 2️⃣ جلب كل البيانات اللازمة دفعة واحدة
        $entriesQuery = DB::connection('assistant')
            ->table('assistant_entries as e')
            ->join('assistant_categories as c', 'c.id', '=', 'e.category_id')
            ->leftJoin('assistant_keywords as k', 'k.entry_id', '=', 'e.id')
            ->leftJoin('assistant_answers as a', function ($join) {
                $join->on('a.entry_id', '=', 'e.id')
                     ->where('a.is_active', '=', 1);
            });
        
            if ($intentCategory) {
                $entriesQuery->where(function ($q) use ($intentCategory) {
                    $q->where('c.name', $intentCategory)
                      ->orWhereIn('c.name', [
                          'تعريف المنظمة',
                          'الأهداف',
                          'مساعدة عامة',
                          'الانضمام'
                      ]);
                });
            }

        $entries = $entriesQuery
            ->select(
                'a.is_primary',
                'e.id as entry_id',
                'c.name as category',
                'k.keyword',
                'a.answer_text'
            )
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'ok' => true,
                'answer' => 'لا يوجد رد',
                'meta' => ['categories' => []]
            ]);
        }

        // 3️⃣ حساب النقاط لكل تصنيف
        $categoryScores = [];

        foreach ($entries as $row) {
            if (!$row->keyword) {
                continue;
            }

            $kw = $this->normalizeArabic($row->keyword);

            foreach ($words as $word) {
                if ($word === '') continue;

                // تجاهل الكلمات العامة (Stop Words)
                if ($this->isStopWord($word)) {
                    continue;
                }

                $wordLen = mb_strlen($word);
                $kwLen   = mb_strlen($kw);

                // تجاهل كلمات ضعيفة جدًا
                if ($kwLen < 3 || $wordLen < 3) {
                    continue;
                }

                // تطابق كامل
                if ($word === $kw) {
                    $score = $kwLen >= 6 ? 3 : 2;
                }
                // تطابق جزئي
                else {
                    similar_text($kw, $word, $percent);
                    if ($percent >= 65) {
                        $score = $kwLen >= 6 ? 2 : 1;
                    } else {
                        continue;
                    }
                }

                $categoryScores[$row->category] =
                    ($categoryScores[$row->category] ?? 0) + $score;
            }
        }

        // 4️⃣ Boost إذا ذُكر اسم التصنيف صراحة
        foreach ($categoryScores as $cat => $score) {
            if (mb_stripos($question, $cat) !== false) {
                $categoryScores[$cat] += 3;
            }
        }

        // 🔴 فلتر الثقة: سؤال غير مفهوم
        $MIN_SCORE = 3;

        arsort($categoryScores);
        $categories = array_keys($categoryScores);

        // 🧠 تحديد ما إذا كان السؤال مركّبًا فعليًا
        $forceMulti = false;

        // سؤال طويل + أكثر من نية لغوية
        if (
            count($meaningfulWords) >= 6 &&
            preg_match('/\b(كيف|هل|شو)\b/u', $question)
        ) {
            $forceMulti = true;
        }

        \Log::info('FORCE_MULTI_DEBUG', [
            'forceMulti' => $forceMulti,
            'meaningfulWords' => count($meaningfulWords),
            'question' => $question
        ]);


        // 🔒 استبعاد تصنيفات سياقية من الأسئلة المعلوماتية
        $excludedInInfoQuestions = [
            'شكر وإنهاء',
            'مساعدة طارئة'
        ];

        // إذا السؤال ليس طارئًا
        if (!preg_match('/\b(خطر|طارئ|عاجل|انقذوني|مساعدة فورية)\b/u', $question)) {
            $categories = array_values(array_filter(
                $categories,
                fn ($cat) => !in_array($cat, $excludedInInfoQuestions, true)
            ));
        }

        if (
            empty($categories) ||
            ($categoryScores[$categories[0]] ?? 0) < $MIN_SCORE
        ) {
            // تسجيل السؤال غير المفهوم
            $unansweredRepo = new UnansweredRepository();
            $unansweredRepo->log(
                $question,
                implode(' ', $words)
            );

            return response()->json([
                'ok' => true,
                'answer' => 'لا يوجد رد',
                'meta' => ['categories' => []]
            ]);
        }

        // 🔐 تصنيفات وظيفية حساسة (مساعدة/حقوق)
        $functionalCategories = [
            'مساعدة عامة',
            'دعم قانوني',
            'توثيق',
            'مساعدة طارئة'
        ];

        // 🧠 تحقق: سؤال مساعدة مركّب؟
        $isHelpQuestion = preg_match(
            '/\b(مساعدة|ساعدوني|انتهاك|ظلم|تهديد|خطر|وثق|توثيق)\b/u',
            $question
        );

        // 🧠 استخراج التصنيفات الوظيفية القوية فقط
        $functionalHits = array_values(array_filter(
            $categories,
            fn ($cat) =>
                in_array($cat, $functionalCategories, true) &&
                ($categoryScores[$cat] ?? 0) >= $MIN_SCORE
        ));

        // ✅ القاعدة الذهبية:
        // سؤال مساعدة + أكثر من تصنيف وظيفي → جوابين فقط
        if ($isHelpQuestion && count($functionalHits) >= 2) {
            $topCategories = array_slice($functionalHits, 0, 2); 

       // 5️⃣ اختيار التصنيفات
       } elseif ($forceMulti) {
           $topCategories = array_slice($categories, 0, 3);

       // 🧩 سؤال عادي
       } else {
           $topCategories = [$categories[0]];
       }

       \Log::info('COMPOSITE_CHECK', [
           'question' => $question,
           'forceMulti' => $forceMulti,
           'categories_ranked' => $categories,
           'topCategories' => $topCategories,
           'scores' => $categoryScores,
       ]);

        // 6️⃣ اختيار جواب لكل تصنيف (Primary أولًا)
        $answers = [];

        foreach ($topCategories as $cat) {

            $catRows = $entries
                ->where('category', $cat)
                ->whereNotNull('answer_text');

            if ($catRows->isEmpty()) {
                continue;
            }

            // 1️⃣ نختار أفضل جواب
            $primary = $catRows->first(fn ($r) => !empty($r->is_primary));

            if ($primary) {
                $answerText = $primary->answer_text;
            } else {
                $answerText = $catRows
                    ->pluck('answer_text')
                    ->sortByDesc(fn ($a) => mb_strlen($a))
                    ->first();
            }
            $answers[] = trim($answerText);
        }
        

        // 7️⃣ الرد النهائي
        $cleanAnswers = array_map(function ($text) {
            // إزالة العناوين مثل: — مساعدة عامة —
            $text = preg_replace('/^—.*?—\s*/mu', '', $text);

            // تنظيف المسافات الزائدة
            return trim($text);
        }, $answers);
        
        \Log::info('ANSWERS_BEFORE_COMPOSE', [
            'count' => count($answers),
            'answers' => $answers,
            'topCategories' => $topCategories
        ]);

        // 7️⃣ الرد النهائي (بدون عناوين)
        return response()->json([
            'ok' => true,
            'answer' => $this->composeAnswer($answers, $question),
            'meta' => [
                'categories' => $topCategories
            ]
        ]);
    }
    
    private function composeAnswer(array $answers, string $question): string
    {
        // 1️⃣ إزالة التكرار النصي
        $unique = [];
        foreach ($answers as $a) {
            $key = mb_substr(preg_replace('/\s+/u', '', $a), 0, 120);
            $unique[$key] = trim($a);
        }
        $answers = array_values($unique);

        $count = count($answers);

        // 2️⃣ إذا جواب واحد → نرجعه كما هو
        if ($count === 1) {
            return $answers[0];
        }

        // 3️⃣ دمج ذكي لفقرتين (سؤال مركّب)
        $intro = $this->buildIntroSentence($question);

        $merged = $intro . "\n\n" .
            $answers[0] . "\n\n" .
            "وبالإضافة إلى ذلك:\n\n" .
            $answers[1];

        return trim($merged);
    }
    //دالة الربط بين الجمل
    private function buildIntroSentence(string $question): string
    {
        if (preg_match('/\b(انضم|عضوية)\b/u', $question)) {
            return "سؤالك مفهوم، وقرار الانضمام عادة يبدأ بمعرفة من نحن وما الذي نقوم به فعليًا.";
        }

        if (preg_match('/\b(كيف|شو)\b/u', $question)) {
            return "بشكل عام، عملنا يرتكز على أكثر من محور مترابط.";
        }

        if (preg_match('/\b(قانوني|شرعي)\b/u', $question)) {
            return "من المهم التأكيد أولًا على طبيعة عملنا القانونية والحقوقية.";
        }

        return "نحاول أن نجيبك بشكل واضح ومباشر على كل الجوانب التي تهمك.";
    }

    private function isIntroductoryQuestion(array $words): bool
    {
        $introWords = ['من', 'مين', 'شو', 'ما', 'ماذا'];

        foreach ($words as $w) {
            if (in_array($w, $introWords, true)) {
                return true;
            }
        }

        return false;
    }
    private function isStopWord(string $word): bool
    {
        static $stopWords = [
            'في','على','عن','من','الى','إلى',
            'شو','كيف','ليش','لماذا',
            'انا','أنا','نحن',
            'بدي','عندي','صار','صارلي',
            'هذا','هاي','هيدا','هيدي'
        ];

        return in_array($word, $stopWords, true);
    }
    private function normalizeArabic(string $text): string
    {
        $text = mb_strtolower(trim($text));

        // إزالة التشكيل
        $text = preg_replace('/[\x{064B}-\x{065F}]/u', '', $text);

        // توحيد الألف
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);

        // توحيد الياء
        $text = str_replace(['ى'], 'ي', $text);

        // إزالة الرموز
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // توحيد المسافات
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
