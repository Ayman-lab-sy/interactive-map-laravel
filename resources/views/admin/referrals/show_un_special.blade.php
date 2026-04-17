@extends('admin.layouts.app')

@section('title', 'إحالة – UN Special Procedures (التعذيب)')

@section('content')

@php
    $readyForGeneration =
        isset($steps)
        && $steps->summary_saved
        && $steps->victim_saved
        && $steps->perpetrators_saved
        && $steps->remedies_saved;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بالتعذيب
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


{{-- Block A --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: ملخص الوقائع المزعومة
        <span class="subtitle">
            Summary of Alleged Facts (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>أهمية هذا القسم:</strong><br>
        هذا الملخص هو <u>المدخل الرئيسي</u> الذي تقرأه الإجراءات الخاصة للأمم المتحدة.
        يجب أن يصف الوقائع المزعومة كما وردت في الشهادة، دون استنتاجات قانونية أو اتهامات.
    </p>
    
    <form method="POST" action="{{ route('admin.referrals.unsp.torture.save-summary', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="incident_summary_en"
                rows="8"
                required
                data-en-only
                class="form-control"
                placeholder="اكتب ملخصًا تحريريًا للوقائع المزعومة كما وردت في الشهادة، بصياغة حيادية وباللغة الإنكليزية فقط."
            >{{ old('incident_summary_en', $data->incident_summary_en ?? '') }}</textarea>
            @error('incident_summary_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>استخدم صياغة حيادية (allegedly / as reported / according to the source)</li>
                <li>لا تستخدم اي توصيف قانوني مثل (torture, ill-treatment, violation, crime)</li>
                <li>لا تذكر أسماء أشخاص أو مؤسسات</li>
                <li>يمنع الاتهام المباشر او لغة جازمة او تحليل او استنتاج</li>
                <li>الهدف ليس اثبات التعذيب بل عرض وقائع قد ترقى الى تعذيب ضمن ولاية المقرر</li>
                <li>حاول دائما ذكر المكان والزمان بشكل عام مثال (In early January 2026, in the vicinity of …)</li>
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
{{-- Block B --}}
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: وصف الضحية
        <span class="subtitle">
            Victim Profile (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>هدف هذا القسم:</strong><br>
        إعطاء صورة عامة عن الضحية دون أي معلومات قد تؤدي إلى التعريف بها أو تعريضها للخطر.
    </p>

    <form method="POST" action="{{ route('admin.referrals.unsp.torture.save-victim', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="victim_profile_en"
                rows="6"
                required
                data-en-only
                class="form-control"
                placeholder="صف الضحية بشكل عام (العمر التقريبي، النوع الاجتماعي، الوضع العام) دون أي معلومات تعريفية."
            >{{ old('victim_profile_en', $data->victim_profile_en ?? '') }}</textarea>
            @error('victim_profile_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>❌ لا أسماء، لا معلومات تعريفية، لا توصيف قانوني.</li>
                <li>✔ مسموح: العمر التقريبي، النوع الاجتماعي، الوضع العام (مدني / عامل / طالب / بلا عمل / محتجز سابقًا / وضع اقتصادي ضعيف)</li>
                <li>❌ ممنوع: الاسم، المهنة الدقيقة، العنوان، أي تفاصيفل تعريفية او تقييم طبي او نفسي</li>
                <li>✔ استخدم مثلا (The individual is described as… / According to the source… / The person reportedly…)</li>
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
</div>
@endif

@if($steps->victim_saved)
{{-- Block C --}}
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: الجهات أو الأطراف المزعومة
        <span class="subtitle">
            Alleged Perpetrators (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>تنبيه تحريري:</strong><br>
         هذا القسم لا يهدف إلى الاتهام، بل إلى وصف الجهات المزعومة كما وردت في الشهادة.
    </p>

    <form method="POST" action="{{ route('admin.referrals.unsp.torture.save-perpetrators', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="alleged_perpetrators_en"
                rows="5"
                required
                data-en-only
                class="form-control"
                placeholder="اذكر الجهات أو الأطراف بصيغة عامة (state actors, security forces, unknown individuals…)."
            >{{ old('alleged_perpetrators_en', $data->alleged_perpetrators_en ?? '') }}</textarea>
            @error('alleged_perpetrators_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li> استخدم توصيفات عامة فيما يخص (State actors, security forces, unknown individuals, Law enforcement officials, Individuals acting in an official capacity)</li>
                <li>من المفضل استخدام مصطلحات مثل (allegedly / reportedly / according to the source)</li>
                <li> استخدم دائما: (The source alleges that… / The individuals are described as… / The acts are reportedly attributed to…)</li>
                <li>❌ لا تذكر أسماء، رتب، وحدات محددة، أو مسؤولين معروفين</li>
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
</div>
@endif

@if($steps->perpetrators_saved)
{{-- Block D --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: السياق أو النمط (اختياري)
        <span class="subtitle">
            Context / Pattern (EN)
        </span>
    </div>

    <div class="info-box info-pin">
        <span class="info-icon">●</span>
        في حال عدم وجود مؤشرات على سياق أو نمط أوسع،
        يمكن تخطي هذا القسم دون أي أثر سلبي على الإحالة.
    </div>

    <p class="page-subtitle">
        <strong>متى يُستخدم هذا القسم؟</strong><br>
        فقط إذا كانت الواقعة جزءًا من نمط أوسع أو ممارسة متكررة، وليس حادثة فردية.
    </p>

    <form method="POST" action="{{ route('admin.referrals.unsp.torture.save-context', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="context_pattern_en"
                rows="4"
                data-en-only
                class="form-control"
                placeholder="هل توجد مؤشرات على نمط أو سياق أوسع؟ (اختياري)"
            >{{ old('context_pattern_en', $data->context_pattern_en ?? '') }}</textarea>
            @error('context_pattern_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>حاول استخدام ما يلي : (may indicate / suggests / appears to be / has been reported)</li>
                <li>تجنب تماما استخدام (proves / confirms / demonstrates)</li>
                <li> مثال: تكرار حالات مشابهة في نفس المنطقة أو خلال نفس الفترة الزمنية.</li>
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
            action="{{ route('admin.referrals.unsp.torture.skip-context', $referral->id) }}">
            @csrf
            <button class="btn btn-secondary">
                 تخطي هذا القسم
            </button>
        </form>
    </div>

</div>
@endif
    
@if($steps->context_done)
{{-- Block E --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: اسنتفاذ سبل الانتصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>أهمية قانونية:</strong><br>
        تعتمد الإجراءات الخاصة على هذا القسم لتحديد مدى إمكانية تدخلها.
    </p>

    <form method="POST" action="{{ route('admin.referrals.unsp.torture.save-remedies', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="remedies_exhausted_en"
                rows="5"
                required
                data-en-only
                class="form-control"
                placeholder="اشرح ما إذا تم اللجوء إلى سبل انتصاف محلية أو لماذا لم يكن ذلك ممكنًا."
            >{{ old('remedies_exhausted_en', $data->remedies_exhausted_en ?? '') }}</textarea>
            @error('remedies_exhausted_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اشرح بإيجاز: هل تم اللجوء للقضاء؟ لماذا لم يكن ذلك ممكنًا؟</li>
                <li>❌ لا تستخدم لغة عاطفية أو سياسية</li>
                <li>✔ استخدام لغة تفسيرية لا اتهامية مثل (was not considered effective / was deemed unavailable / would have posed a risk / no accessible remedy existed)</li>
                <li>❌ لا تستخدم نهائيا (the judiciary is corrupt / the state refuses justice / the system is criminal)</li>
            </ul>                
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
