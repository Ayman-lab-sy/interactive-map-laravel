<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Assistant\UnansweredRepository;
use App\Services\Assistant\AssistantKnowledgeService;
use App\Services\Assistant\AssistantAuditService;
use Illuminate\Support\Facades\DB;

class AssistantAdminController extends Controller
{
    protected UnansweredRepository $unanswered;

    public function __construct(UnansweredRepository $unanswered)
    {
        $this->unanswered = $unanswered;
    }

    /**
     * عرض قائمة الأسئلة غير المفهومة
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $entries = match ($status) {
            'new'      => $this->unanswered->byStatus('new'),
            'ignored'  => $this->unanswered->byStatus('ignored'),
            default    => $this->unanswered->all(),
        };

        return view('admin.assistant.index', compact('entries', 'status'));
    }
    
    public function convert(string $id)
    {
        $entry = $this->unanswered->findById($id);

        abort_if(!$entry, 404, 'السؤال غير موجود');

        $categories = DB::connection('assistant')
            ->table('assistant_categories')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($c) {

                // Entries (Tone + Tags)
                $entries = DB::connection('assistant')
                    ->table('assistant_entries')
                    ->where('category_id', $c->id)
                    ->select('id', 'tone', 'tags')
                    ->get()
                    ->map(fn ($e) => [
                        'id'   => $e->id,
                        'tone' => $e->tone,
                        'tags' => json_decode($e->tags, true) ?? [],
                    ])
                    ->values();

                // Keywords (كما كانت)
                $keywords = DB::connection('assistant')
                    ->table('assistant_keywords')
                    ->join('assistant_entries', 'assistant_entries.id', '=', 'assistant_keywords.entry_id')
                    ->where('assistant_entries.category_id', $c->id)
                    ->pluck('assistant_keywords.keyword')
                    ->unique()
                    ->values()
                    ->all();

                // Answers (كما كانت)
                $answers = DB::connection('assistant')
                    ->table('assistant_answers')
                    ->join('assistant_entries', 'assistant_entries.id', '=', 'assistant_answers.entry_id')
                    ->where('assistant_entries.category_id', $c->id)
                    ->pluck('assistant_answers.answer_text')
                    ->unique()
                    ->values()
                    ->all();

                return [
                    $c->name => [
                        'entries'  => $entries,
                        'keywords' => $keywords,
                        'answers'  => $answers,
                    ]
                ];
            })
            ->all();

        return view('admin.assistant.convert', compact('entry', 'categories'));
    }

    /**
     * صفحة اعتماد سؤال
     */
    public function approve(string $id)
    {
        $ok = $this->unanswered->approve($id);

        abort_if(!$ok, 404, 'السؤال غير موجود');

        DB::connection('assistant')->table('assistant_audit_logs')->insert([
            'action'       => 'approve',
            'question_id'  => $id,
            'admin_email'  => auth()->user()->email ?? 'system',
            'created_at'   => now(),
        ]);

        return redirect()
            ->route('admin.assistant.index')
            ->with('success', 'تم اعتماد السؤال بنجاح');
    }

    /**
     * حفظ التعديلات
     */
    public function storeConvert(
        Request $request,
        AssistantKnowledgeService $service
    ) {
        $request->validate([
            'draft_id' => 'required|integer',
            'category_existing' => 'nullable|string',
            'category_new'      => 'nullable|string',

            // 🧠 Entry / Tone إلزامي
            'tone' => 'required|string|min:2',

            // 🏷️ Tags إلزامية (نص بسيط، نفصله لاحقًا)
            'tags' => 'required|string|min:2',

            // 🔑 Keywords (موجودة مسبقًا)
            'keywords' => ['required', 'string', function ($attr, $value, $fail) {
                $list = preg_split('/[،,]+/u', $value);
                $list = array_filter(array_map('trim', $list));

                if (count($list) < 1) {
                    $fail('يجب إدخال كلمة مفتاحية واحدة على الأقل.');
                }
            }],

            'answer' => 'nullable|string',
        ]);


        // 🟢 تحديد التصنيف
        $category = $request->category_existing === '__new__'
            ? trim($request->category_new)
            : trim($request->category_existing);

        abort_if(!$category, 422, 'اسم التصنيف مطلوب');

        // 🟢 تحديد نمط التصنيف (موجود / جديد)
        $categoryMode = $request->category_existing === '__new__'
            ? 'new'
            : 'existing';

        // 🟢 استخراج الكلمات المفتاحية (عربي + إنجليزي)
        $keywords = array_values(array_unique(
            array_filter(array_map('trim',
                preg_split('/[،,]+/u', $request->keywords)
            ))
        ));

        // 🧼 Normalization للـ Tone (منع المسافات الزائدة)
        $tone = preg_replace('/\s+/u', ' ', trim($request->tone));


        // 🟢 تنفيذ التحويل (DB Native)
        $service->convert(
            (int) $request->draft_id,
            $category,
            $categoryMode,
            $tone,
            trim($request->tags),
            $keywords,
            trim($request->answer ?? ''),
            auth()->user()->email ?? 'system'
        );

        return redirect()
            ->route('admin.assistant.index')
            ->with('success', '✅ تم تحويل السؤال إلى معرفة بنجاح');
    }
    
    /**
     * تنفيذ التجاهل
     */
    public function ignore(Request $request)
    {
        $id = $request->input('id');

        $ok = $this->unanswered->ignore($id);

        abort_if(!$ok, 404, 'السؤال غير موجود');

        $entry = $this->unanswered->findById($id);

        DB::connection('assistant')->table('assistant_audit_logs')->insert([
            'action'       => 'ignore',
            'question_id'  => $id,
            'payload'      => json_encode([
                'question' => $entry['question'] ?? null,
                'expanded' => $entry['expanded'] ?? null,
                'previous_status' => $entry['status'] ?? null,
            ], JSON_UNESCAPED_UNICODE),
            'admin_email'  => auth()->user()->email ?? 'system',
            'created_at'   => now(),
        ]);

        return redirect()
            ->route('admin.assistant.index')
            ->with('success', 'تم تجاهل السؤال');
    }

    public function audit(AssistantAuditService $audit)
    {
        $logs = $audit->all();

        return view('admin.assistant.audit', compact('logs'));
    }
}
