@extends('admin.layouts.app')

@section('title', 'Referral – UN Accountability (OHCHR)')

@section('content')

@php
$readyForGeneration =
    isset($steps)
    && $steps->context_saved
    && $steps->methodology_saved
    && $steps->location_saved
    && $steps->timeframe_saved
    && $steps->documented_saved
    && $steps->concerns_saved
    && $steps->mandate_saved;
@endphp

<div class="page-title">
    إحالة – المساءلة الأممية
    <div class="page-subtitle">
        مكتب المفوض السامي لحقوق الإنسان (OHCHR)
    </div>
</div>

{{-- ========================= --}}
{{-- بطاقة معلومات الحالة الأصلية (للاطلاع التحريري فقط) --}}
{{-- ========================= --}}
<div class="card">

    <div class="section-title">
        معلومات الحالة الأصلية
        <span class="page-subtitle">
            (للاطلاع التحريري فقط)
        </span>
    </div>

    <div class="case-info-grid">

        {{-- رقم الحالة --}}
        <div class="info-card">
            <span class="info-label">رقم الحالة</span>
            <span class="info-value">
                {{ $case->case_number ?? '—' }}
            </span>
        </div>

        {{-- الاسم الكامل --}}
        <div class="info-card">
            <span class="info-label">الاسم الكامل</span>
            <span class="info-value">
                {{ $case->full_name ?? '—' }}
            </span>
        </div>

        {{-- نوع الاسم --}}
        <div class="info-card">
            <span class="info-label">نوع الاسم</span>
            <span class="info-value">
                {{ $case->name_type === 'alias' ? 'اسم مستعار' : 'اسم حقيقي' }}
            </span>
        </div>

        {{-- تاريخ الميلاد --}}
        <div class="info-card">
            <span class="info-label">تاريخ الميلاد</span>
            <span class="info-value">
                {{ $case->birth_date ?? '—' }}
            </span>
        </div>

        {{-- مكان الاقامة --}}
        <div class="info-card">
            <span class="info-label">مكان الإقامة</span>
            <span class="info-value">
                {{ $case->location ?? '—' }}
            </span>
        </div>
    </div>
    <div style="margin-top:18px;"></div>
    <div class="case-info-grid">

        {{-- الطائفة / المكوّن --}}
        <div class="info-card">
            <span class="info-label">الطائفة / المكوّن</span>
            <span class="info-value">
                {{ config('referral.components.' . $case->component) ?? '—' }}
            </span>
        </div>

        {{-- نوع الانتهاك --}}
        <div class="info-card">
            <span class="info-label">نوع الانتهاك</span>
            <span class="info-value">
                {{ config('referral.violation_types.' . $case->violation_type) ?? '—' }}
            </span>
        </div>

        {{-- مكان الواقعة --}}
        <div class="info-card">
            <span class="info-label">مكان الواقعة</span>
            <span class="info-value">
                {{ $case->threat_locations ?? '—' }}
            </span>
        </div>

        {{-- تاريخ الواقعة --}}
        <div class="info-card">
            <span class="info-label">تاريخ الواقعة</span>
            <span class="info-value">
                {{ $case->threat_date ?? '—' }}
            </span>
        </div>

        {{-- حساسية الحالة --}}
        <div class="info-card">
            <span class="info-label">حساسية الحالة</span>
            <span class="info-value emphasis">
                {{ strtoupper($case->case_sensitivity ?? '—') }}
            </span>
        </div>
    </div>
    <div style="margin-top:18px;"></div>
    {{-- بيانات العائلة --}}
    @if(
        $allowFamilyData &&
        (
            !empty($case->spouse_name)
            || !empty($case->children)
        )
    )
    <div class="card">

        <div class="section-title">
            بيانات العائلة
            <span class="subtitle">
                Family Information (Reference Only)
            </span>
        </div>

        {{-- الزوج / الزوجة --}}
        <div class="info-card">
            <span class="info-label">الزوج / الزوجة</span>
            <span class="info-value">
                {{ $case->spouse_name ?? '—' }}
            </span>
        </div>

        {{-- الأولاد --}}
        <div class="info-card">
            <span class="info-label">الأولاد</span>
            <span class="info-value">
                @if(!empty($case->children))
                    @php
                        $children = json_decode($case->children, true);
                            @endphp

                    {{ collect($children)
                        ->map(fn($c) =>
                            ($c['name'] ?? '—') . ' (' . ($c['age'] ?? '?') . ')'
                        )
                        ->implode('، ')
                    }}
                @else
                    —
                @endif
            </span>
        </div>

    </div>
    @endif


    {{-- وصف الوقائع --}}
    <div style="margin-top:24px;">
        <strong>وصف الوقائع كما وردت في الشهادة</strong>
        <p class="case-description">
            {{ $case->threat_description ?? '—' }}
        </p>
    </div>

    {{-- الأثر --}}
    <div class="case-impact">
        <strong>الأثر النفسي / الاجتماعي</strong>
        <p>{{ $case->impact_details ?? '—' }}</p>
    </div>

    {{-- تنبيهات --}}
    <div class="info-box info-info" style="margin-top:16px;">
        <span class="info-icon">!</span>
         هذه المعلومات معروضة <strong>للمرجع التحريري فقط</strong>.
        لا يجب نسخها حرفيًا داخل التقرير، بل إعادة صياغتها بشكل مهني وحيادي.
    </div>

    <div class="info-box info-danger" style="margin-top:10px; color:#b91c1c;">
        <span class="info-icon">×</span>
         الأدلة الداعمة محفوظة ضمن ملف الحالة الداخلي
        ولا يتم إرفاقها ضمن إحالات المفوض السامي لحقوق الإنسان (OHCHR).
    </div>

    <div class="info-box muted" style="margin-top:24px;">
        <span class="info-icon">!</span>
         انتهى عرض المعلومات المرجعية للحالة.
        الأقسام التالية مخصصة لإعداد نص الإحالة.
    </div>

</div>

{{-- ========================= --}}
{{-- Block A – Context --}}
{{-- ========================= --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: ملخص السياقي للوضع
        <span class="subtitle">
            Contextual Description of the Situation (OHCHR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى تقديم <strong>وصف سياقي عام للوضع الحقوقي كما ورد إلى المنظمة من المصادر</strong>،
        وذلك لأغراض <strong>التوثيق المؤسسي والمساءلة</strong> ضمن ولاية
        مكتب المفوض السامي لحقوق الإنسان (OHCHR).
        <br><br>
        يجب أن يركّز النص على <strong>السياق العام، البيئة المحيطة، والظروف السائدة</strong>
        دون الدخول في تفاصيل وقائعية دقيقة أو توصيفات قانونية،
        وبأسلوب معلوماتي حيادي.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_context', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="source_context_en" 
                rows="5" 
                required 
                data-en-only
                class="form-control"
            >{{ old('source_context_en', $data->source_context_en ?? '') }}</textarea>
            @error('source_context_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ قدّم خلفية عامة عن الوضع كما نُقلت إلى المنظمة (توتر أمني، قيود، مناخ عام)</li>
                <li>✔ اكتب بصيغة وصفية محايدة ومعلوماتية</li>
                <li>✔ ركّز على السياق والظروف وليس على الوقائع التفصيلية</li>
                <li>✔ استخدم لغة مناسبة لتقارير التوثيق المؤسسي لـ OHCHR</li>
                <li>❌ لا تذكر أسماء أشخاص، جماعات، أو مواقع دقيقة</li>
                <li>❌ لا تستخدم توصيفات قانونية أو مصطلحات جنائية</li>
                <li>❌ لا تُسند مسؤولية أو تُشير إلى اتهامات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>3. Contextual Overview of the Situation</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>

{{-- ========================= --}}
{{-- Block B – Methodology --}}
{{-- ========================= --}}
@if($steps->context_saved)
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: المنهجية المعتمدة
        <span class="subtitle">
            Methodology Note (OHCHR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توضيح <strong>منهجية جمع، مراجعة، وتقييم المعلومات</strong>
        المعتمدة من قبل المنظمة، وذلك <strong>لأغراض الشفافية المؤسسية فقط</strong>
        ضمن سياق التوثيق والمساءلة.
        <br><br>
        يجب أن يشرح النص <strong>كيف</strong> تم استلام المعلومات،
        وكيف جرى التعامل معها داخليًا، دون الخوض في محتوى الحالة أو
        تقديم أي استنتاجات تحليلية.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_methodology', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="methodology_note_en" 
                rows="4" 
                required 
                data-en-only
                class="form-control"
            >{{ old('methodology_note_en', $data->methodology_note_en ?? '') }}</textarea>
            @error('methodology_note_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
            <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اشرح آلية استلام المعلومات (تواصل مباشر، مصادر محلية، تقارير)</li>
                <li>✔ وضّح وجود مراجعة داخلية للتناسق والمصداقية</li>
                <li>✔ استخدم لغة مؤسسية محايدة مناسبة لتقارير OHCHR</li>
                <li>✔ ركّز على المنهج، وليس على تفاصيل الحالة</li>
                <li>❌ لا تُقيّم صحة الادعاءات قانونيًا</li>
                <li>❌ لا تستخدم توصيفات جنائية أو مصطلحات اتهامية</li>
                <li>❌ لا تذكر أسماء مصادر أو تفاصيل تعريفية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>2. Methodology</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block C – Location & Time --}}
{{-- ========================= --}}
@if($steps->methodology_saved)
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: الموقع والإطار الزمني
        <span class="subtitle">
            Location and Timeframe
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توثيق <strong>الموقع العام</strong> و<strong>الإطار الزمني التقريبي</strong>
        للحالة كما وردا إلى المنظمة من المصادر،
        وذلك <strong>لأغراض السياق والتوثيق فقط</strong>.
        <br><br>
        يجب استخدام توصيفات عامة وغير دقيقة، دون تحديد مواقع حساسة أو تواريخ تفصيلية.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_location_time', $referral->id) }}">
        @csrf
        <div class="form-group">

            <textarea 
                name="general_location_en" 
                rows="3" 
                required 
                data-en-only
                placeholder="General location"
                class="form-control form-control--sm"
            >{{ old('general_location_en', $data->general_location_en ?? '') }}</textarea>
            @error('general_location_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <textarea 
                name="incident_timeframe_en" 
                rows="3" 
                required 
                data-en-only
                placeholder="Timeframe"
                class="form-control form-control--sm"
            >{{ old('incident_timeframe_en', $data->incident_timeframe_en ?? '') }}</textarea>
            @error('incident_timeframe_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ استخدم توصيفًا جغرافيًا عامًا (منطقة، إقليم، محافظة)</li>
                <li>✔ استخدم إطارًا زمنيًا تقريبيًا (بداية الشهر، منتصف السنة)</li>
                <li>✔ اكتب كما ورد عن المصدر دون تعديل أو تفسير</li>
                <li>❌ لا تذكر أسماء قرى، أحياء دقيقة، أو إحداثيات</li>
                <li>❌ لا تستخدم تواريخ يومية أو ساعات</li>
                <li>❌ لا تربط الموقع أو الزمن بأي جهة فاعلة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا المحتوى في التقرير النهائي تحت القسم:
            <em>4. Location and Timeframe</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block D – Documented Information --}}
{{-- ========================= --}}
@if($steps->location_saved && $steps->timeframe_saved)
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: المعلومات الموثقة
        <span class="subtitle">
            Documented Information (As Received)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توثيق <strong>الوقائع والمعلومات كما وردت من المصادر</strong>
        وبالشكل الذي جُمعت به من قبل المنظمة،
        <strong>دون أي تحليل، استنتاج، أو توصيف قانوني</strong>.
        <br><br>
        هذا القسم يمثّل <strong>النقل الوقائعي المباشر</strong> لما أفادت به المصادر،
        ويُستخدم كأساس للتوثيق المؤسسي فقط.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_documented_info', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="documented_information_en" 
                rows="5" 
                required 
                data-en-only
                class="form-control"
            >{{ old('documented_information_en', $data->documented_information_en ?? '') }}</textarea>
            @error('documented_information_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
            <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف الوقائع كما نُقلت من المصدر (ما الذي حدث، كيف، وما الأثر المباشر)</li>
                <li>✔ استخدم لغة وصفية محايدة وغير تحليلية</li>
                <li>✔ اكتب بصيغة نقل غير مباشر لما أفاد به المصدر</li>
                <li>✔ حافظ على التسلسل الزمني للأحداث إن وُجد</li>
                <li>❌ لا تستخدم توصيفات قانونية (انتهاك، جريمة، مسؤولية…)</li>
                <li>❌ لا تُسمّي جهات فاعلة أو أطراف مشتبه بها</li>
                <li>❌ لا تضف استنتاجات أو تقييمات شخصية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا المحتوى في التقرير النهائي تحت القسم:
            <em>5. Factual Information (As Documented)</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block E – Identified Concerns --}}
{{-- ========================= --}}
@if($steps->documented_saved)
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: المخاوف الحقوقية المحددة
        <span class="subtitle">
            Identified Human Rights Concerns
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى <strong>تحديد المخاوف الحقوقية العامة</strong> التي تبرز
        <strong>استنادًا إلى المعلومات الموثّقة أعلاه</strong>،
        دون توصيف قانوني أو تحميل مسؤولية.
        <br><br>
        هذا القسم يمثّل <strong>تحديدًا مؤسسيًا للمجالات المثيرة للقلق الحقوقي</strong>
        من منظور توثيقي، وليس تقييمًا قانونيًا أو حكمًا قضائيًا.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_concerns', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="identified_concerns_en" 
                rows="4" 
                required 
                data-en-only
                class="form-control"
            >{{ old('identified_concerns_en', $data->identified_concerns_en ?? '') }}</textarea>
            @error('identified_concerns_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ حدّد مجالات القلق الحقوقي بشكل عام (السلامة، الكرامة، الحماية، الوصول للخدمات)</li>
                <li>✔ اربط المخاوف بما ورد في المعلومات الموثّقة دون إعادة سرد الوقائع</li>
                <li>✔ استخدم لغة تحليلية مؤسسية غير قانونية</li>
                <li>✔ يمكن الكتابة بنقاط أو فقرات قصيرة واضحة</li>
                <li>❌ لا تستخدم مصطلحات قانونية (انتهاك، جريمة، مسؤولية جنائية)</li>
                <li>❌ لا تُسمّي جهات أو أطراف فاعلة</li>
                <li>❌ لا تصدر استنتاجات أو توصيات قضائية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا المحتوى في التقرير النهائي تحت القسم:
            <em>6. Identified Human Rights Concerns</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block F – Pattern Observation (Optional) --}}
{{-- ========================= --}}
@if($steps->concerns_saved)
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: ملاحظات حول الأنماط المحتملة (اختياري)
        <span class="subtitle">
            Pattern Observation (Optional)
        </span>
    </div>

    <div class="info-box info-pin">
        <span class="info-icon">●</span>
        هذا القسم <strong>اختياري بالكامل</strong> ويجب تركه فارغاً إذا لم تتوفر مؤشرات واضحة أو إذا كان إدراجه قد يؤدي إلى استنتاجات غير مؤكدة
    </div>

    <p class="page-subtitle">
        يتيح هذا الحقل للمنظمة <strong>الإشارة – عند الاقتضاء فقط – إلى وجود عناصر
        متكررة أو مؤشرات عامة</strong> قد تتجاوز هذه الحالة الفردية،
        وذلك <strong>استنادًا إلى المعرفة التراكمية المؤسسية</strong>.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_pattern', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="pattern_observation_en" 
                rows="4" 
                data-en-only
                class="form-control"
            >{{ old('pattern_observation_en', $data->pattern_observation_en ?? '') }}</textarea>
            @error('pattern_observation_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ استخدم عبارات حذِرة (may indicate, appears to reflect, suggests)</li>
                <li>✔ يمكن الإشارة لتكرار زمني أو تشابه سياقي دون تحديد أطراف</li>
                <li>✔ اكتب من منظور مؤسسي عام وليس استنتاجيًا</li>
                <li>❌ لا تجزم بوجود سياسة أو ممارسة ممنهجة</li>
                <li>❌ لا تستخدم لغة اتهامية أو قانونية</li>
                <li>❌ لا تربط النمط بجهة أو فاعل محدد</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت العنوان:
            <em>7. Observed Patterns</em><br>
            <span style="font-style:italic;">
                (أو سيظهر نص توضيحي ثابت في حال ترك الحقل فارغًا)
            </span>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ (اختياري)</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block G – Mandate --}}
{{-- ========================= --}}
@if($steps->concerns_saved)
<div id="block-g" class="card" data-block="G">

    <div class="section-title">
        القسم السابع:  الصلة بولاية مفوضية الأمم المتحدة السامية لحقوق الإنسان
        <span class="subtitle">
            Relevance to the OHCHR Mandate
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توضيح <strong>الأساس المؤسسي</strong> الذي يجعل الحالة
        الموثقة ذات صلة بولاية مفوضية الأمم المتحدة السامية لحقوق الإنسان،
        بما يشمل <strong>التوثيق، الرصد، المتابعة، أو الدعم التحليلي</strong>.
        <br><br>
        يجب أن يركّز النص على <strong>الاختصاص العام للمفوضية</strong> دون
        توصيف قانوني أو استنتاج مسؤوليات.
    </p>

    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_mandate', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="mandate_relevance_en" 
                rows="3" 
                required 
                data-en-only
                class="form-control"
            >{{ old('mandate_relevance_en', $data->mandate_relevance_en ?? '') }}</textarea>
            @error('mandate_relevance_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اربط الحالة بوظائف OHCHR (monitoring, documentation, reporting)</li>
                <li>✔ استخدم لغة مؤسسية عامة (falls within the mandate, relevant to)</li>
                <li>✔ ركّز على حماية حقوق الإنسان والرصد المنهجي</li>
                <li>❌ لا تطلب إجراءات قضائية أو محاسبة مباشرة</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفًا جنائيًا</li>
                <li>❌ لا تُحدّد أطرافًا أو مسؤوليات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>8. Relevance to the OHCHR Mandate</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ نهائي</button>
        </div>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Block H – Internal Notes --}}
{{-- ========================= --}}
@if($steps->mandate_saved)
<div id="block-h" class="card" data-block="H">

    <div class="section-title">
         ملاحظات داخلية (غير مشمولة في التقرير)
        <span class="subtitle">
            Internal Notes (Not Included in Report)
        </span>
    </div>

    <div class="info-box info-info">
        <span class="info-icon">ℹ</span>

        <div>
            <strong>ملاحظات داخلية للفريق</strong>
            <p style="margin:6px 0 8px;">
                يُستخدم هذا الحقل لتسجيل ملاحظات تنظيمية داخلية
                لا تُدرج ضمن التقرير النهائي مثل:
            </p>

            <ul class="info-list">
                <li>ملاحظات المتابعة</li>
                <li>اعتبارات تحريرية داخلية</li>
                <li>تقييمات أولية أو تعليمات مستقبلية</li>
            </ul>
        </div>
    </div>


    <form method="POST" action="{{ route('admin.referrals.un_accountability.ohchr.save_internal_notes', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="additional_notes_internal" 
                rows="4"
                class="form-control"
            >{{ old('additional_notes_internal', $data->additional_notes_internal ?? '') }}</textarea>
            @error('additional_notes_internal')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>هذا الحقل:</strong>
            <ul>
                <li>✔ اختياري بالكامل</li>
                <li>✔ مخصص للاستخدام الداخلي فقط</li>
                <li>✔ لا يؤثر على جاهزية التقرير أو توليده</li>
            </ul>
        </div>

        <div class="info-box info-info" style=color:#b45309>
            <span class="info-icon">!</span>
             تنبيه: لا تُدرج هنا أي معلومات يُفترض إرسالها إلى OHCHR.
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
            <strong>هذا المحتوى لا يظهر</strong> في التقرير النهائي،
        ولا يتم تضمينه في أي مخرجات خارجية (PDF / Preview / Export).
        </div>

        <button class="btn btn-secondary">حفظ داخلي</button>
    </form>
</div>
@endif

{{-- ========================= --}}
{{-- Final Actions – Execution Card --}}
{{-- ========================= --}}
<div class="card">

    <div class="section-title">
        إجراءات الإحالة النهائية
        <span class="subtitle">
            Final Referral Actions
        </span>
    </div>

    <p class="page-subtitle">
        بعد استكمال جميع أقسام الإحالة أعلاه، يمكن الآن
        الانتقال إلى مرحلة تثبيت الإحالة وتوليد التقرير الرسمي.
        يرجى التأكد من مراجعة جميع المدخلات قبل المتابعة.
    </p>

    {{-- الحالة: قيد التحضير --}}
    @if($referral->referral_status === 'prepared')

        @if($readyForGeneration)
            <form method="POST"
                  action="{{ route('admin.referrals.mark-ready', $referral->id) }}">
                @csrf

                <div class="form-actions">
                    <button class="btn btn-primary">
                        تجهيز الإحالة للتوليد
                    </button>
                </div>

                <div class="info-box info-danger" style="color:#b91c1c;">
                    <span class="info-icon">×</span>
                     بعد هذه الخطوة تصبح الإحالة مقفلة ولا يمكن تعديل أي من الأقسام.
                </div>
            </form>
        @else
            <div class="info-box info-pin" style="color:#b91c1c;">
                <span class="info-icon">●</span>
                 لا يمكن تجهيز الإحالة قبل استكمال جميع الحقول المطلوبة.
            </div>
        @endif

    @endif

    {{-- الحالة: جاهزة للتوليد --}}
    @if($referral->referral_status === 'ready_for_generation')

        <div class="form-actions">
            <a href="{{ route('admin.referrals.generate-report', $referral->id) }}"
               class="btn btn-primary"
               target="_blank"
               rel="noopener noreferrer"
               onclick="setTimeout(() => { window.location.reload(); }, 700);">
                توليد التقرير
            </a>
        </div>

    @endif

    {{-- الحالة: تم التوليد --}}
    @if(in_array($referral->referral_status, ['generated','exported']))

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             الإحالة مقفلة. التقرير تم توليده ولا يمكن إعادة التوليد.
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.referrals.download-pdf', $referral->id) }}"
               class="btn btn-primary">
                تحميل PDF
            </a>

            <a href="{{ route('admin.referrals.un.cover-letter', $referral->id) }}"
               class="btn btn-secondary"
               target="_blank"
               rel="noopener noreferrer">
                تحميل Cover Letter (UN)
            </a>
        </div>

    @endif

</div>


{{-- ========================= --}}
{{-- Audit Log – Final Section --}}
{{-- ========================= --}}
@if(isset($auditLogs) && $auditLogs->count())
<div class="card">

    <div class="section-title">
        سجل الأحداث
        <span class="subtitle">
            Audit Log
        </span>
    </div>

    <p class="page-subtitle">
        يوضّح هذا السجل جميع الإجراءات التي تم تنفيذها
        على هذه الإحالة، بما في ذلك عمليات الحفظ،
        التعديل، التثبيت، والتوليد، لأغراض التتبع
        والمراجعة الداخلية.
    </p>

    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>الإجراء</th>
                    <th>المستخدم</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditLogs as $log)
                    <tr>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->user_name ?? '—' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($log->created_at)->toDateTimeString() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endif

{{-- EN only --}}
<script>
document.addEventListener('input', function (e) {
    if (e.target.matches('[data-en-only]')) {
        if (/[\u0600-\u06FF]/.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[\u0600-\u06FF]/g, '');
            alert('هذا الحقل يقبل اللغة الإنكليزية فقط.');
        }
    }
});
</script>

@endsection
