@extends('voyager::master')

@section('content')
<div class="page-content container-fluid assistant-unanswered-convert">

    <h1 class="mb-4">🔁 تحويل السؤال إلى معرفة</h1>

    <div class="alert alert-info">
        اختر التصنيف المناسب، أضف الكلمات المفتاحية، ويمكنك إضافة إجابة جديدة.
        عند الحفظ سيتم ربط السؤال بالمعرفة فعليًا.
    </div>

    <div class="panel panel-bordered">
        <div class="panel-body">

            {{-- ✅ عرض أخطاء التحقق --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>فشل الحفظ:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- السؤال --}}
            <p><strong>السؤال:</strong> {{ $entry['question'] }}</p>
            <p><strong>بعد التوسيع:</strong> {{ $entry['expanded'] ?? '-' }}</p>

            <hr>

            {{-- 🔴 الفورم الحقيقي --}}
            <form method="POST"
                  action="{{ route('admin.assistant.convert.store') }}"
                  class="assistant-form"
                  id="assistantConvertForm">

                @csrf
                <input type="hidden" name="draft_id" value="{{ $entry['id'] }}">

                {{-- اختيار التصنيف --}}
                <div class="form-group">
                    <label>📂 اختر التصنيف</label>
                    <select class="form-control"
                            name="category_existing"
                            id="categorySelect">
                        <option value="">— اختر تصنيف —</option>

                        @foreach($categories as $name => $data)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach

                        <option value="__new__">➕ إضافة تصنيف جديد</option>
                    </select>
                    <div class="form-group mt-2" id="newCategoryWrapper" style="display:none">
                        <label>🆕 اسم التصنيف الجديد</label>
                        <input type="text"
                               name="category_new"
                               class="form-control"
                               placeholder="اكتب اسم التصنيف الجديد">
                    </div>
                </div>

                {{-- معاينة + إدخال --}}
                <div id="categoryPreview" style="display:none">

                    {{-- 🎯 اختيار النية (Entry) --}}
                    <div class="form-group">
                        <label>🎯 نبرة / نية الإجابة (Entry)</label>

                        {{-- Dropdown النوايا الموجودة --}}
                        <select id="entrySelect" class="form-control mb-2">
                            <option value="">— اختر نية موجودة —</option>
                        </select>

                        {{-- إدخال نية جديدة --}}
                        <input type="text"
                               name="tone"
                               id="entryInput"
                               class="form-control"
                               placeholder="أو اكتب نية جديدة (مثال: جدي، تشجيعي، مطمئن)"
                               required>

                        <small class="text-muted">
                            اختر نية موجودة أو أضف نية جديدة عند الحاجة.
                        </small>
                    </div>

                    {{-- 🏷️ Tags الخاصة بالـ Entry --}}
                    <div class="form-group">
                        <label>🏷️ Tags (وصف دلالي للنية)</label>
                        <input type="text"
                               name="tags"
                               class="form-control"
                               required
                               placeholder="مثال: حقوق، إنسان، دعم، قانون، توعية">
                        <small class="text-muted">
                            هذه Tags عامة للـ Entry وليست كلمات بحث.
                        </small>
                    </div>

                    {{-- الكلمات المفتاحية --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">🔑 الكلمات المفتاحية</div>
                        <div class="panel-body">

                            <div id="keywordChips" class="chip-container"></div>

                            <input type="text"
                                   id="keywordInput"
                                   class="form-control mt-2"
                                   placeholder="اكتب كلمة ثم Enter أو فاصلة">

                            {{-- الحقل الحقيقي الذي يُرسل --}}
                            <input type="hidden" name="keywords" id="keywordsHidden" required>

                        </div>
                    </div>

                    {{-- الإجابات --}}
                    <div class="panel panel-default mt-3">
                        <div class="panel-heading">💬 إضافة إجابة جديدة (اختياري)</div>
                        <div class="panel-body">
                            <ul id="answersList"></ul>

                            <textarea name="answer"
                                      class="form-control"
                                      rows="3"
                                      placeholder="اكتب إجابة جديدة إن لزم"></textarea>
                        </div>
                    </div>

                    {{-- زر الحفظ --}}
                    <button type="submit" class="btn btn-success mt-3">
                        ✅ حفظ التحويل
                    </button>

                </div>
            </form>

        </div>
    </div>

</div>

{{-- JavaScript --}}
<script>
const categories = @json($categories);

const categorySelect = document.getElementById('categorySelect');
const preview = document.getElementById('categoryPreview');
const chipContainer = document.getElementById('keywordChips');
const input = document.getElementById('keywordInput');
const hidden = document.getElementById('keywordsHidden');
const answersList = document.getElementById('answersList');
const newCategoryWrapper = document.getElementById('newCategoryWrapper');
const MAX_WORDS_PER_KEYWORD = 3;
// ===== Entry (Tone) Logic =====
const entrySelect = document.getElementById('entrySelect');
const entryInput  = document.getElementById('entryInput');
const tagsInput   = document.querySelector('input[name="tags"]');

let currentCategoryKeywords = [];
let keywords = [];

function normalize(word) {
    return word.trim().replace(/\s+/g, ' ');
}

function render() {
    chipContainer.innerHTML = '';

    const existing = (currentCategoryKeywords || []).map(w => w.toLowerCase());

    keywords.forEach((word, index) => {
        const isNew = !existing.includes(word.toLowerCase());

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
    const parts = word.trim().split(/\s+/);
    return parts.length <= MAX_WORDS_PER_KEYWORD;
}

function addWord(word) {
    word = normalize(word);
    if (!word) return;

    // منع الجُمل
    if (!isValidKeyword(word)) {
        alert('❌ الكلمة المفتاحية يجب ألا تتجاوز 3 كلمات.');
        return;
    }

    if (!keywords.map(w => w.toLowerCase()).includes(word.toLowerCase())) {
        keywords.push(word);
        render();
    }
}

function removeChip(index) {
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

categorySelect.addEventListener('change', function () {
    const value = this.value;
    if (value === '__new__') {
        newCategoryWrapper.style.display = 'block';
        preview.style.display = 'block';
        keywords = [];
        render();
        answersList.innerHTML = '';
        return;
    }
    newCategoryWrapper.style.display = 'none';
    if (!categories[value]) {
        preview.style.display = 'none';
        return;
    }
    preview.style.display = 'block';
    // 🔑 تعبئة الكلمات القديمة (للأزرق)
    currentCategoryKeywords = [...(categories[value].keywords || [])];
    // 📝 الحالة الحالية (قد تُضاف عليها كلمات جديدة)
    keywords = [...currentCategoryKeywords];
    // ✅ ثبت الكلمات الأصلية هنا
    window.originalKeywords = [...currentCategoryKeywords]
        .map(w => w.trim().toLowerCase());
    render();
    answersList.innerHTML =
        (categories[value].answers || []).map(a => `<li>${a}</li>`).join('');
});

categorySelect.addEventListener('change', function () {
    const category = this.value;

    // إعادة تهيئة Entry
    if (entrySelect) {
        entrySelect.innerHTML = '<option value="">— اختر نية موجودة —</option>';
    }
    if (entryInput) entryInput.value = '';
    if (tagsInput) tagsInput.value = '';

    if (!categories[category] || !categories[category].entries) return;

    categories[category].entries.forEach(entry => {
        const opt = document.createElement('option');
        opt.value = entry.tone;
        opt.textContent = entry.tone;
        opt.dataset.tags = (entry.tags || []).join(', ');
        entrySelect.appendChild(opt);
    });
});

if (entrySelect) {
    entrySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) return;

        entryInput.value = selected.value;
        tagsInput.value  = selected.dataset.tags || '';
    });
}

// ===== Tone Duplicate Warning =====
document.getElementById('assistantConvertForm')
    ?.addEventListener('submit', function (e) {

    const category = categorySelect.value;
    if (!category || !categories[category] || !categories[category].entries) {
        return;
    }

    const inputToneRaw = entryInput.value;
    if (!inputToneRaw) return;

    const inputTone = normalizeArabic(inputToneRaw);

    const existingTones = categories[category].entries
        .map(e => normalizeArabic(e.tone));

    // 🔎 إذا كتب نية تطابق نية موجودة
    if (existingTones.includes(inputTone)) {

        // 🧠 لكن: هل اختارها من القائمة بدون تعديل؟
        if (
            entrySelect &&
            entrySelect.value &&
            normalizeArabic(entrySelect.value) === inputTone
        ) {
            // هذه حالة اختيار طبيعي → لا تنبيه
            return;
        }

        // 🚨 حالة تكرار خطرة
        const ok = confirm(
            '⚠️ هذه النية موجودة مسبقًا ضمن هذا التصنيف.\n' +
            'هل تريد الاستمرار وإضافة كلمات مفتاحية/إجابة لها؟'
        );

        if (!ok) {
            e.preventDefault();
        }
    }
});

function normalizeArabic(text) {
    return text
        .replace(/[ًٌٍَُِّْ]/g, '')       // إزالة التشكيل
        .replace(/[إأآا]/g, 'ا')          // توحيد الألف
        .replace(/ى/g, 'ي')
        .replace(/ؤ/g, 'و')
        .replace(/ئ/g, 'ي')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase();
}

</script>
<script>
document.getElementById('assistantConvertForm')
    ?.addEventListener('submit', function (e) {

    const original = window.originalKeywords || [];

    // لا يوجد كلمات أصلية → لا تنبيه
    if (original.length === 0) return;

    const hidden = document.getElementById('keywordsHidden');
    if (!hidden) return;

    const current = hidden.value
        .split(',')
        .map(w => w.trim().toLowerCase())
        .filter(Boolean);

    const deleted = original.filter(k => !current.includes(k));

    if (deleted.length > 0) {
        const ok = confirm(
            '⚠️ انتبه، قمت بحذف كلمات مفتاحية موجودة سابقًا.\n\n' +
            'هل أنت متأكد من حفظ التعديلات؟'
        );

        if (!ok) {
            e.preventDefault();
        }
    }
});
</script>

<style>
.chip-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 38px;
    padding: 6px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fafafa;
}

.chip {
    background: #2c7be5;
    color: #fff;
    padding: 4px 8px;
    border-radius: 14px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.chip button {
    background: transparent;
    border: none;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    padding: 0;
}

.chip-existing {
    background: #17a2b8; /* أزرق */
}

.chip-new {
    background: #28a745; /* أخضر */
}

</style>
<style>
/* ===============================
   RTL SCOPE – UNANSWERED CONVERT
   =============================== */
.assistant-unanswered-convert {
    direction: rtl;
    background: #f4f6f9;
}

.assistant-unanswered-convert h1,
.assistant-unanswered-convert h2,
.assistant-unanswered-convert h3,
.assistant-unanswered-convert p,
.assistant-unanswered-convert label,
.assistant-unanswered-convert td,
.assistant-unanswered-convert th {
    text-align: right;
}

/* ===============================
   PANELS & CARDS
   =============================== */
.assistant-unanswered-convert .panel {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: 1px solid #e6e9ef;
}

.assistant-unanswered-convert .panel-heading {
    font-weight: 700;
    color: #343a40;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

/* ===============================
   FORM ELEMENTS
   =============================== */
.assistant-unanswered-convert .form-control {
    border-radius: 10px;
    border: 1px solid #ced4da;
}

.assistant-unanswered-convert .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0,123,255,.15);
}

/* ===============================
   BUTTONS
   =============================== */
.assistant-unanswered-convert .btn-success {
    background: #28a745;
    border-color: #28a745;
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 12px;
}

/* ===============================
   KEYWORD CHIPS (تحسين الوضوح)
   =============================== */
.assistant-unanswered-convert .chip-existing {
    background: #17a2b8;
}

.assistant-unanswered-convert .chip-new {
    background: #28a745;
}

/* ===============================
   ANSWERS LIST
   =============================== */
.assistant-unanswered-convert ul {
    padding-right: 18px;
}

.assistant-unanswered-convert ul li {
    margin-bottom: 6px;
    color: #495057;
}
</style>

@endsection
