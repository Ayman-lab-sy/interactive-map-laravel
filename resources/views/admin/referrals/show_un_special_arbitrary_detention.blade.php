@extends('admin.layouts.app')

@section('title', 'Referral – UN Special Procedures (Arbitrary Detention)')

@section('content')

@php
    $readyForGeneration =
        isset($steps)
        && $steps->summary_saved
        && $steps->victim_saved
        && $steps->detention_saved
        && $steps->legal_basis_saved
        && $steps->procedural_saved
        && $steps->remedies_saved
        && $steps->context_done;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بالاجتجاز التعسفي
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
        ولا يتم إرفاقها ضمن إحالات الإجراءات الخاصة للأمم المتحدة.
    </div>

    <div class="info-box muted" style="margin-top:24px;">
        <span class="info-icon">!</span>
         انتهى عرض المعلومات المرجعية للحالة.
        الأقسام التالية مخصصة لإعداد نص الإحالة.
    </div>

</div>


{{-- ========================= --}}
{{-- Block A – Summary --}}
{{-- ========================= --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: ملخص الوقائع المزعومة
        <span class="subtitle">
            Summary of Alleged Facts (EN)
        </span>
    </div>

    <p class="page-subtitle">
        هذا القسم هو المدخل الرئيسي الذي تطّلع عليه
        <strong>Working Group on Arbitrary Detention</strong>.
        يجب وصف الوقائع المتعلقة بحرمان الشخص من حريته كما وردت في الشهادة
        دون توصيف قانوني أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_summary', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="incident_summary_en"
                rows="8"
                required
                data-en-only
                class="form-control"
            >{{ old('incident_summary_en', $data->incident_summary_en ?? '') }}</textarea>
            @error('incident_summary_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>استخدم دائمًا صياغة حيادية <em>(allegedly / reportedly / according to the source)</em></li>
                <li>ركّز على وقائع الحرمان من الحرية كما وردت في الشهادة</li>
                <li>✔ صف كيف ومتى وأين تم احتجاز الشخص بصيغة عامة</li>
                <li>✔ استخدم تسلسلًا زمنيًا بسيطًا إن أمكن</li>
                <li>✔ يمكن الإشارة إلى مدة الاحتجاز أو استمراره إن كان معلومًا</li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(arbitrary detention, unlawful detention, violation)</em></li>
                <li>❌ لا تُسند مسؤولية قانونية أو نية</li>
                <li>❌ لا تحليل قانوني أو استنتاجات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Summary of Alleged Facts</em>
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

@if($steps->summary_saved)
{{-- ========================= --}}
{{-- Block B – Victim --}}
{{-- ========================= --}}
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: معلومات الضحية
        <span class="subtitle">
            Victim Information (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>هدف هذا القسم:</strong><br>
        تقديم معلومات تعريفية أساسية عن الشخص الذي حُرم من حريته، كما وردت في الشهادة،
        لدعم ولاية <strong>Working Group on Arbitrary Detention</strong>.
        يجب أن تكون المعلومات دقيقة ومحدودة، مع مراعاة اعتبارات السلامة والحماية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_victim', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="victim_information_en"
                rows="6"
                required
                data-en-only
                class="form-control"
            >{{ old('victim_information_en', $data->victim_information_en ?? '') }}</textarea>
            @error('victim_information_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ يمكن ذكر الاسم إذا كان معروفًا وآمنًا</li>
                <li>✔ يمكن ذكر العمر أو تاريخ الميلاد التقريبي</li>
                <li>✔ يمكن ذكر الجنس والجنسية</li>
                <li>✔ يمكن ذكر المهنة أو الوضع الاجتماعي إذا كان ذا صلة</li>
                <li>✔ استخدم صياغة وصفية حيادية <em>(is reported to have been detained / is described as…)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(arbitrary detention, unlawful detention)</em></li>
                <li>❌ لا تذكر رتب، أرقام هواتف، عناوين دقيقة، أو معلومات قد تعرّض الشخص أو عائلته للخطر</li>
                <li>❌ لا تحليل قانوني أو استنتاجات</li>
            </ul>
        </div>
        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Information Concerning the Person Deprived of Liberty</em>
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

@if($steps->victim_saved)
{{-- ========================= --}}
{{-- Block C – Details of the Detention --}}
{{-- ========================= --}}
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: تفاصيل الاحتجاز
        <span class="subtitle">
            Details of the Detention (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف ظروف الاحتجاز كما وردت في الشهادة،
        بما في ذلك مكان الاحتجاز المزعوم، مدته، والجهة التي يُعتقد أنها قامت به،
        دون توصيف قانوني أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_detention', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="detention_details_en"
                rows="6"
                required
                data-en-only
                class="form-control"
            >{{ old('detention_details_en', $data->detention_details_en ?? '') }}</textarea>
            @error('detention_details_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف ظروف الاحتجاز كما وردت في الشهادة دون تفسير أو تقييم</li>
                <li>✔ اذكر مكان الاحتجاز بصيغة عامة إن كان معروفًا <em>(a security facility / an unknown location)</em></li>
                <li>✔ اذكر مدة الاحتجاز أو الإطار الزمني إن أمكن <em>(for several days / since early March 2026)</em></li>
                <li>✔ صف طبيعة الاحتجاز <em>(held incommunicado / transferred between locations)</em> إن ورد ذلك</li>
                <li>✔ استخدم صياغة حيادية <em>(was reportedly detained / is alleged to have been held)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(arbitrary, unlawful, illegal)</em></li>
                <li>❌ لا تُسند نية أو مسؤولية قانونية</li>
                <li>❌ لا تحليل قانوني أو استنتاجات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Details of the Detention</em>
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

@if($steps->detention_saved)
{{-- ========================= --}}
{{-- Block D – Legal Basis for the Detention --}}
{{-- ========================= --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: الاساس القانوني للاحتجاز
        <span class="subtitle">
            Legal Basis for the Detention (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح ما إذا كان قد تم إبلاغ الشخص المحتجز
        بأي أساس قانوني لاحتجازه، كما ورد في الشهادة،
        دون أي تحليل قانوني أو توصيف اتهامي.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_legal_basis', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="legal_basis_en"
                rows="5"
                required
                data-en-only
                class="form-control"
            >{{ old('legal_basis_en', $data->legal_basis_en ?? '') }}</textarea>
            @error('legal_basis_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اذكر ما إذا تم إبلاغ الشخص المحتجز بأي سبب لاحتجازه كما ورد في الشهادة</li>
                <li>✔ إن لم يُقدَّم أي سبب، اذكر ذلك بصيغة وصفية محايدة</li>
                <li>✔ يمكن الإشارة إلى ما إذا تم عرض مذكرة أو أمر قضائي، دون تقييم</li>
                <li>✔ استخدم صياغة حيادية <em>(was reportedly informed / no information was reportedly provided)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(illegal / unlawful / arbitrary)</em></li>
                <li>❌ لا تُقيِّم مدى كفاية أو شرعية الأساس القانوني</li>
                <li>❌ لا تحليل قانوني أو استنتاجات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Legal Basis for the Detention</em>
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

@if($steps->legal_basis_saved)
{{-- ========================= --}}
{{-- Block E – Procedural Violations --}}
{{-- ========================= --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: المخالفات الاجرائية
        <span class="subtitle">
            Procedural Violations (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف أية مخالفات إجرائية محتملة
        رافقت الاحتجاز كما وردت في الشهادة،
        مثل عدم العرض على قاضٍ، الحرمان من التواصل مع محامٍ،
        أو عدم إبلاغ العائلة بمكان الاحتجاز،
        دون توصيف قانوني أو استنتاجات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_procedural_violations', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="procedural_violations_en"
                rows="6"
                required
                data-en-only
                class="form-control"
            >{{ old('procedural_violations_en', $data->procedural_violations_en ?? '') }}</textarea>
            @error('procedural_violations_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف ما جرى أو لم يجر من إجراءات أثناء الاحتجاز كما ورد في الشهادة</li>
                <li>✔ يمكن ذكر عدم العرض على قاضٍ أو تأخره بصيغة وصفية</li>
                <li>✔ يمكن ذكر الحرمان من التواصل مع محامٍ أو العائلة إن ورد ذلك</li>
                <li>✔ استخدم صياغة حيادية <em>(was reportedly not brought before a judge / access to legal counsel was reportedly denied)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(violation of due process / breach of law)</em></li>
                <li>❌ لا تربط الوقائع بمعايير قانونية أو التزامات دولية</li>
                <li>❌ لا تحليل قانوني أو استنتاجات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Procedural Aspects of the Detention</em>
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

@if($steps->procedural_saved)
{{-- ========================= --}}
{{-- Block F – Context / Pattern --}}
{{-- ========================= --}}
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: السياق أو النمط (اختياري)
        <span class="subtitle">
            Context / Pattern (Optional) (EN)
        </span>
    </div>

    <div class="info-box info-pin">
        <span class="info-icon">●</span>
        في حال عدم وجود مؤشرات على سياق أو نمط أوسع،
        يمكن تخطي هذا القسم دون أي أثر سلبي على الإحالة.
    </div>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_context', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="context_pattern_en"
                rows="4"
                data-en-only
                class="form-control"
            >{{ old('context_pattern_en', $data->context_pattern_en ?? '') }}</textarea>
            @error('context_pattern_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ استخدم هذا القسم فقط إذا أشارت الشهادة إلى حالات مشابهة أو متكررة</li>
                <li>✔ يمكن الإشارة إلى تكرار الاحتجاز في نفس المنطقة أو الفترة الزمنية</li>
                <li>✔ استخدم صياغة احتمالية <em>(may indicate / suggests / appears to be)</em></li>
                <li>✔ قدّم السياق بوصفه ملاحظة عامة وليس استنتاجًا</li>
                <li>❌ لا تستخدم لغة إثبات أو جزم <em>(proves / confirms / demonstrates)</em></li>
                <li>❌ لا تستنتج وجود سياسة أو ممارسة ممنهجة</li>
                <li>❌ لا تستخدم توصيفات قانونية أو تقييمات</li>
            </ul>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>

    {{-- Skip Context --}}
    <div class="form-actions">
        <form method="POST"
              action="{{ route('admin.referrals.un_sp.arbitrary_detention.skip_context', $referral->id) }}">
            @csrf
            <button class="btn btn-secondary">
                تخطي هذا القسم
            </button>
        </form>
    </div>

</div>
@endif

@if($steps->context_done)
{{-- ========================= --}}
{{-- Block G – Exhaustion of Remedies --}}
{{-- ========================= --}}
<div id="block-g" class="card" data-block="G">

    <div class="section-title">
        القسم السابع: استنفاد سبل الانتصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح ما إذا كانت سبل الانتصاف المحلية
        قد استُنفدت، أو ما إذا كان اللجوء إليها غير ممكن أو ينطوي على مخاطر،
        كما ورد في الشهادة.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.arbitrary_detention.save_remedies', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="remedies_exhausted_en"
                rows="5"
                required
                data-en-only
                class="form-control"
            >{{ old('remedies_exhausted_en', $data->remedies_exhausted_en ?? '') }}</textarea>
            @error('remedies_exhausted_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اذكر ما إذا تم اللجوء إلى سبل انتصاف محلية كما ورد في الشهادة</li>
                <li>✔ إن لم يتم ذلك، اذكر السبب بصيغة تفسيرية محايدة</li>
                <li>✔ يمكن الإشارة إلى الخوف من المخاطر أو عدم توفر السبل</li>
                <li>✔ استخدم صياغة وصفية <em>(were reportedly not pursued / were deemed unavailable)</em></li>
                <li>❌ لا تستخدم لغة اتهامية أو سياسية</li>
                <li>❌ لا تُقيِّم فعالية النظام القضائي أو المؤسسات</li>
                <li>❌ لا تستخدم توصيفات قانونية أو استنتاجات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Exhaustion of Remedies</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ نهائي
            </button>
        </div>
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


{{-- EN only JS --}}
<script>
document.addEventListener('input', function (e) {
    if (e.target.matches('[data-en-only]')) {
        const arabicPattern = /[\u0600-\u06FF]/g;
        if (arabicPattern.test(e.target.value)) {
            e.target.value = e.target.value.replace(arabicPattern, '');
            alert('هذا الحقل يقبل اللغة الإنكليزية فقط.');
        }
    }
});
</script>

@endsection
