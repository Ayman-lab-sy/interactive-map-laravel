@extends('voyager::master')

@section('content')
<div class="page-content container-fluid assistant-knowledge-editor">

    <h1 class="mb-4">
        ✏️ إدارة إجابات المساعد
        <small class="text-muted">
            — {{ $entry->category }} / <strong>{{ $entry->tone }}</strong>
        </small>
    </h1>

    @php
        $hasPrimary  = $answers->contains('is_primary', 1);
        $activeCount = $answers->where('is_active', 1)->count();
        $totalCount  = $answers->count();
    @endphp

    {{-- Alerts --}}
    @if(!$hasPrimary && $totalCount > 0)
        <div class="alert alert-warning">
            ⚠️ لا توجد إجابة مرجعية (Primary) لهذه النية.
        </div>
    @endif

    @if($activeCount === 0 && $totalCount > 0)
        <div class="alert alert-danger">
            ❗ جميع الإجابات غير مفعّلة — هذه النية لن تعطي أي رد.
        </div>
    @endif

    @if($totalCount === 1)
        <div class="alert alert-info">
            ℹ️ توجد إجابة واحدة فقط. يُفضّل إضافة تنويع.
        </div>
    @endif

    <div class="alert alert-info">
        ℹ️ عند عدم وجود إجابة مرجعية، يتم اختيار إجابة عشوائيًا ضمن هذه النية.
    </div>

    <form method="POST" action="{{ route('admin.assistant.knowledge.update', $entry->id) }}">
        @csrf

        {{-- No Primary Option --}}
        <div class="card mb-4">
            <div class="card-body">
                <label style="font-weight:600;">
                    <input
                        type="radio"
                        name="primary_answer_id"
                        value=""
                        {{ !$hasPrimary ? 'checked' : '' }}
                    >
                    🔁 بدون إجابة مرجعية (اختيار عشوائي)
                </label>

                <div class="text-muted mt-1" style="font-size:14px;">
                    سيتم اختيار إجابة عشوائيًا من الإجابات المفعّلة لهذه النية.
                </div>
            </div>
        </div>

        {{-- Existing Answers --}}
        @foreach($answers as $i => $answer)
            <div class="card mb-3 answer-card {{ $answer->is_primary ? 'primary-answer' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        🧠 إجابة رقم {{ $i + 1 }}
                        <span class="text-muted">(ID: {{ $answer->id }})</span>
                    </div>

                    @if($answer->is_primary)
                        <span class="badge badge-success">
                            ⭐ الإجابة المرجعية
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 gap-3">
                        <label class="mb-0">
                            <input
                                type="radio"
                                name="primary_answer_id"
                                value="{{ $answer->id }}"
                                {{ $answer->is_primary ? 'checked' : '' }}
                            >
                            تعيين كإجابة مرجعية
                        </label>

                        <label class="mb-0">
                            <input
                                type="checkbox"
                                name="active[{{ $answer->id }}]"
                                {{ $answer->is_active ? 'checked' : '' }}
                            >
                            مفعّلة
                        </label>
                    </div>

                    <textarea
                        name="answers[{{ $answer->id }}]"
                        rows="5"
                        class="form-control"
                        dir="rtl"
                    >{{ $answer->answer_text }}</textarea>
                </div>
            </div>
        @endforeach

        {{-- Actions --}}
        <div class="mt-4 d-flex gap-2 justify-content-start">
            <button class="btn btn-success btn-lg">
                💾 حفظ جميع التعديلات
            </button>

            <a href="{{ route('admin.assistant.knowledge') }}"
               class="btn btn-outline-secondary btn-lg">
                ⬅️ رجوع
            </a>
        </div>

        {{-- 🔑 الكلمات المفتاحية --}}
        <div class="card mt-4">
            <div class="card-header">🔑 الكلمات المفتاحية الخاصة بهذه النية</div>
            <div class="card-body">

                <div id="keywordChips" class="chip-container"></div>

                <input type="text"
                       id="keywordInput"
                       class="form-control mt-2"
                       placeholder="اكتب كلمة مفتاحية ثم Enter أو فاصلة">

                {{-- الحقل الحقيقي --}}
                <input type="hidden"
                       name="keywords"
                       id="keywordsHidden"
                       value="{{ implode(', ', $keywords ?? []) }}">

                <small class="text-muted d-block mt-2">
                    • الكلمات الزرقاء موجودة مسبقًا  
                    • الكلمات الخضراء كلمات جديدة  
                    • الحد الأقصى: 3 كلمات لكل كلمة مفتاحية
                </small>
            </div>
        </div>

        {{-- Add New Answer --}}
        <div class="card mt-4">
            <div class="card-header">➕ إضافة إجابة جديدة</div>
            <div class="card-body">

                <textarea
                    name="new_answer"
                    rows="4"
                    class="form-control mb-3"
                    placeholder="اكتب الإجابة الجديدة هنا (20 حرفًا على الأقل)..."
                    dir="rtl"
                ></textarea>

                <div class="d-flex gap-4">
                    <label>
                        <input type="checkbox" name="new_active" checked>
                        مفعّلة مباشرة
                    </label>

                    <label>
                        <input type="checkbox" name="new_primary">
                        تعيينها كإجابة مرجعية
                    </label>
                </div>

                <div class="text-muted mt-2" style="font-size:14px;">
                    في حال تعيينها كمرجعية، سيتم إلغاء المرجعية عن باقي إجابات هذه النية.
                </div>

            </div>
        </div>

    </form>

</div>
@endsection

@section('css')
<style>
/* FORCE RTL — Voyager Override */
.assistant-knowledge-editor,
.assistant-knowledge-editor * {
    direction: rtl !important;
    text-align: right !important;
}

/* Textarea */
.assistant-knowledge-editor textarea.form-control {
    direction: rtl !important;
    text-align: right !important;
    unicode-bidi: plaintext;
    font-size: 16px;
    line-height: 1.9;
    background: #fbfbfb;
}

/* Cards */
.assistant-knowledge-editor .card {
    border-radius: 10px;
}

.assistant-knowledge-editor .card-header {
    background: #f5f7fa;
    font-weight: 600;
}

/* Primary Highlight */
.primary-answer {
    border: 2px solid #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.15);
}

/* Alerts */
.alert {
    font-size: 15px;
    line-height: 1.8;
}

/* Spacing */
.answer-card textarea {
    margin-top: 10px;
}

/*الكلمات المفتاحية*/
.chip-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 38px;
    padding: 6px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fafafa;
}

.chip {
    color: #fff;
    padding: 4px 10px;
    border-radius: 14px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.chip-existing { background: #17a2b8; }
.chip-new { background: #28a745; }

.chip button {
    background: transparent;
    border: none;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
}

</style>
<script>
    const existingKeywords = @json($keywords ?? []);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // 🔑 الكلمات القادمة من السيرفر
    const existingKeywords = @json($keywords ?? []);

    const chipContainer = document.getElementById('keywordChips');
    const input = document.getElementById('keywordInput');
    const hidden = document.getElementById('keywordsHidden');

    if (!chipContainer || !input || !hidden) {
        console.error('❌ عناصر الكلمات المفتاحية غير موجودة في الصفحة');
        return;
    }

    const MAX_WORDS_PER_KEYWORD = 3;

    // 🧠 مرجع ثابت (بدون تكرار)
    let baseKeywords = Array.from(
        new Set(existingKeywords.map(w => w.trim()).filter(Boolean))
    );

    // 🧩 الحالة الحالية
    let keywords = [...baseKeywords];

    function normalize(word) {
        return word.trim().replace(/\s+/g, ' ');
    }

    function render() {
        chipContainer.innerHTML = '';

        // حماية إضافية من التكرار
        keywords = Array.from(
            new Set(keywords.map(w => w.trim()).filter(Boolean))
        );

        const existingLower = baseKeywords.map(w => w.toLowerCase());

        keywords.forEach((word, index) => {
            const isNew = !existingLower.includes(word.toLowerCase());

            const chip = document.createElement('span');
            chip.className = 'chip ' + (isNew ? 'chip-new' : 'chip-existing');
            chip.innerHTML = `
                ${word}
                <button type="button" onclick="removeChip(${index})">×</button>
            `;
            chipContainer.appendChild(chip);
        });

        hidden.value = keywords.join(', ');
    }

    function isValidKeyword(word) {
        return word.split(/\s+/).length <= MAX_WORDS_PER_KEYWORD;
    }

    function addWord(word) {
        word = normalize(word);
        if (!word) return;

        if (!isValidKeyword(word)) {
            alert('❌ الكلمة المفتاحية يجب ألا تتجاوز 3 كلمات.');
            return;
        }

        if (!keywords.map(w => w.toLowerCase()).includes(word.toLowerCase())) {
            keywords.push(word);
            render();
        }
    }

    window.removeChip = function (index) {
        keywords.splice(index, 1);
        render();
    }

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',' || e.key === '،') {
            e.preventDefault();
            addWord(input.value);
            input.value = '';
        }
    });

    // أول عرض
    render();
});
</script>

@endsection
