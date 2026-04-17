@extends('admin.layouts.app')

@section('title', 'Referral Details')

@section('content')




<div class="page-title">
    إحالة – التوثيق والمناصرة الحقوقية
    <div class="page-subtitle">
        منظمة هيومن رايتس ووتش (Human Rights Watch)
    </div>
</div>

<div class="page-subtitle" style="margin-top:4px;">
    حالة الإحالة:
    <strong>{{ strtoupper($referral->referral_status) }}</strong>
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
        ولا يتم إرفاقها ضمن إحالات منظمة Human Rights Watch
    </div>

    <div class="info-box muted" style="margin-top:24px;">
        <span class="info-icon">!</span>
         انتهى عرض المعلومات المرجعية للحالة.
        الأقسام التالية مخصصة لإعداد نص الإحالة.
    </div>

</div>

{{-- ========================= --}}
{{-- Block A – Legal Narrative --}}
{{-- ========================= --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: الصياغة القانونية المعتمدة
        <span class="subtitle">
            Legal Narrative (Human Rights Watch)
        </span>
    </div>

    @if($steps->narrative_saved)
        <div class="info-box info-success">
            <span class="info-icon">✓</span>
            تم حفظ الصياغة القانونية بنجاح.
        </div>
    @endif

    <p class="page-subtitle">
        يُعد هذا القسم <strong>النص الأساسي الذي تعتمد عليه منظمة Human Rights Watch</strong>
        في تقييم الحالة، والتحقق منها، واستخدامها ضمن
        <strong>التقارير الحقوقية، التحقيقات، أو أنشطة المناصرة الدولية</strong>.
        <br><br>
        يجب أن تُكتب الصياغة بأسلوب <strong>قانوني، مهني، وحيادي</strong>،
        يعكس الوقائع كما وردت إلى المنظمة، دون استخدام لغة اتهامية
        أو توصيفات قضائية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.ngo.hrw.save_narrative', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea
                name="narrative_en"
                rows="8"
                required
                data-en-only
                class="form-control"
            >{{ old('narrative_en', $narrative->content ?? '') }}</textarea>

            @error('narrative_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
            <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ قدّم سردًا قانونيًا واضحًا للوقائع كما وردت إلى المنظمة</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(allegedly / reportedly / according to available information)</em></li>
                <li>✔ اذكر الزمان والمكان بصيغة وصفية غير دقيقة عند الحاجة</li>
                <li>✔ حافظ على أسلوب مهني مناسب لتقارير Human Rights Watch</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفات جنائية</li>
                <li>❌ لا تُسند مسؤولية قانونية مباشرة لأي جهة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
           <em>Source Account (As Reported)</em>
        </div>

        <div class="info-box info-danger">
                <span class="info-icon">×</span>
            هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ الصياغة والمتابعة
            </button>
        </div>

    </form>

</div>


{{-- Block 2: violation --}}
@if($steps->narrative_saved)
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: مواءمة ملخص القضية
        <span class="subtitle">
            Summary Alignment & Violation Classification
        </span>
    </div>

    @if($steps->summary_saved)
        <div class="info-box info-success">
            <span class="info-icon">✓</span>
             تم حفظ مواءمة ملخص القضية بنجاح.
        </div>
    @endif

    <p class="page-subtitle">
        يهدف هذا القسم إلى <strong>مواءمة نوع الانتهاك المختار مع الوقائع الأساسية كما وردت في الشهادة</strong>،
        وذلك لضمان الاتساق بين التصنيف الموضوعي وملخص القضية
        المستخدم في التقرير المرسل إلى <strong>Human Rights Watch</strong>.
    </p>

    <form method="POST" action="{{ route('admin.referrals.ngo.hrw.save_summary_alignment', $referral->id) }}">
        @csrf
         {{-- Violation Classification --}}
        <div class="form-group">
            <label class="section-title">
                تصنيف الانتهاك (لأغراض الملخص)
                <span class="required"></span>
            </label>

            <select name="violation_classification" required class="form-control">
                <option value="">— اختر التصنيف القانوني للانتهاك —</option>

                <optgroup label="الحرية والأمان الشخصي">
                    <option value="ARBITRARY_ARREST_OR_DETENTION"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'ARBITRARY_ARREST_OR_DETENTION' ? 'selected' : '' }}>
                        اعتقال أو احتجاز تعسفي
                    </option>
                    <option value="ENFORCED_DISAPPEARANCE"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'ENFORCED_DISAPPEARANCE' ? 'selected' : '' }}>
                        اختفاء قسري
                    </option>
                </optgroup>

                <optgroup label="السلامة الجسدية والنفسية">
                    <option value="TORTURE_OR_INHUMAN_TREATMENT"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'TORTURE_OR_INHUMAN_TREATMENT' ? 'selected' : '' }}>
                        تعذيب أو معاملة قاسية أو لا إنسانية
                    </option>
                    <option value="THREATS_OR_INTIMIDATION"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'THREATS_OR_INTIMIDATION' ? 'selected' : '' }}>
                        تهديد أو ترهيب
                    </option>
                </optgroup>

                <optgroup label="التمييز والانتهاكات القائمة على الهوية">
                    <option value="DISCRIMINATION_BASED_VIOLATION"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'DISCRIMINATION_BASED_VIOLATION' ? 'selected' : '' }}>
                        تمييز ديني أو عرقي
                    </option>
                    <option value="SEXUAL_OR_GENDER_BASED_VIOLENCE"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'SEXUAL_OR_GENDER_BASED_VIOLENCE' ? 'selected' : '' }}>
                        عنف جنسي أو قائم على النوع
                    </option>
                </optgroup>

                <optgroup label="الملكية وسبل العيش">
                    <option value="PROPERTY_CONFISCATION_OR_DESTRUCTION"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'PROPERTY_CONFISCATION_OR_DESTRUCTION' ? 'selected' : '' }}>
                        مصادرة أو تدمير ممتلكات
                    </option>
                    <option value="LIVELIHOOD_RESTRICTION"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'LIVELIHOOD_RESTRICTION' ? 'selected' : '' }}>
                        تقييد سبل العيش
                    </option>
                </optgroup>

                <optgroup label="النزوح والاستقرار">
                    <option value="FORCED_DISPLACEMENT"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'FORCED_DISPLACEMENT' ? 'selected' : '' }}>
                        تهجير قسري
                    </option>
                </optgroup>

                <optgroup label="حالات مركبة">
                    <option value="MULTIPLE_VIOLATIONS"
                        {{ old('violation_classification', $referral->violation_classification ?? '') === 'MULTIPLE_VIOLATIONS' ? 'selected' : '' }}>
                        انتهاكات متعددة أو مركبة
                    </option>
                </optgroup>
            </select>

            <div class="info-box info-info">
            <span class="info-icon">!</span>
             اختر نوع الانتهاك الأساسي الذي تعكسه الشهادة.  
             يُستخدم هذا الاختيار لتوليد الصياغة الحقوقية العامة في التقرير الدولي.
            </div>

        </div>

        {{-- Summary Alignment Note --}}
        <div class="form-group">
            <label class="section-title">
                سطر مواءمة الملخص مع الشهادة
                <span class="required"></span>
            </label>

            <textarea
                name="summary_alignment_note"
                rows="4"
                required
                data-en-only
                class="form-control"
                placeholder="اكتب جملة أو جملتين تلخصان الوقائع الجوهرية المرتبطة بنوع الانتهاك المختار، بصياغة محايدة وغير اتهامية."
            >{{ old('summary_alignment_note', $referral->summary_alignment_note ?? '') }}</textarea>

            @error('summary_alignment_note')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Guidance --}}
        <div class="info-box info-info">
            <span class="info-icon">!</span>
            <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ لا تنسخ الشهادة حرفيًا</li>
                <li>✔ استخدم لغة محايدة وغير اتهامية</li>
                <li>✔ لا تذكر أسماء أشخاص أو جهات</li>
                <li>✔ الهدف هو ربط التصنيف بالوقائع الأساسية فقط</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت جزء:
           <em>Case Summary (Prepared by the Organization)</em>
        </div>

        <div class="info-box info-danger">
            <span class="info-icon">×</span>
            هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ مواءمة الملخص
            </button>
        </div>
     </form>
</div>

@endif

{{-- ========================= --}}
{{-- Block C – Analytical Content --}}
{{-- ========================= --}}
@if($steps->summary_saved)

<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: المحتوى التحليلي
        <span class="subtitle">
            Analytical Content (EN)
        </span>
    </div>

    @if($steps->analysis_saved)
        <div class="info-box info-success">
            <span class="info-icon">✓</span>
            تم حفظ جميع الحقول التحليلية بنجاح
        </div>
    @endif

    <p class="page-subtitle">
        يُستخدم هذا القسم لتقديم <strong>معلومات تحليلية عامة</strong>
        تُدرج في التقرير الدولي لأغراض التوثيق الحقوقي،
        دون الخوض في تفاصيل حساسة أو توصيفات قانونية.
        <br><br>
        يجب أن تعكس المدخلات <strong>خلاصة تحليلية مبنية على الشهادة</strong>
        وبأسلوب حيادي ومهني.
    </p>

    <form method="POST" action="{{ route('admin.referrals.ngo.hrw.save_analytical', $referral->id) }}">
        @csrf

        {{-- General Location --}}
        <div class="form-group">
            <label class="section-title">
                الموقع العام للحادثة
                <span class="subtitle">(General Location)</span>
            </label>

            <textarea 
                name="general_location_en"
                rows="2"
                required
                data-en-only
                placeholder="e.g. Somewhere in Damascus, Syria"
                class="form-control form-control--sm"
            >{{ old('general_location_en', $narrative->general_location_en ?? '') }}</textarea>
            @error('general_location_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <div class="info-box info-info">
                <span class="info-icon">!</span>
                اذكر الموقع بصيغة عامة فقط (مدينة أو محافظة).
                يُمنع ذكر عناوين دقيقة أو مواقع تفصيلية.
            </div>
        </div>

        {{-- Incident Timeframe --}}
        <div class="form-group">
            <label class="section-title">
                الإطار الزمني للحادثة
                <span class="subtitle">(Incident Timeframe)</span>
            </label>

            <textarea 
                name="incident_timeframe_en"
                rows="2"
                data-en-only
                class="form-control form-control--sm"
                placeholder="e.g. Early 2026"
            >{{ old('incident_timeframe_en', $narrative->incident_timeframe_en ?? '') }}</textarea>
            @error('incident_timeframe_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <div class="info-box info-info">
                <span class="info-icon">!</span>
                استخدم توصيفًا زمنيًا عامًا دون تاريخ دقيق
                (مثل: Early 2026، Mid-2025).
            </div>
        </div>

        {{-- Psychosocial Impact --}}
        <div class="form-group">
            <label class="section-title">
                ملخص الأثر النفسي / الاجتماعي
                <span class="subtitle">(Psychosocial Impact Summary)</span>
            </label>

            <textarea
                name="psychosocial_impact_en"
                rows="4"
                data-en-only
                class="form-control"
                placeholder="Brief neutral summary of psychosocial or social impact"
            >{{ old('psychosocial_impact_en', $narrative->psychosocial_impact_en ?? '') }}</textarea>

            <div class="info-box info-info">
                <span class="info-icon">!</span>
                لخّص الأثر النفسي أو الاجتماعي كما ورد في الشهادة،
                دون تشخيص طبي أو تقييم مهني.
            </div>

            <div class="info-box info-danger">
                <span class="info-icon">×</span>
               جميع الحقول مخصصة للإدخال باللغة الانكليزية فقط
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ المحتوى التحليلي
            </button>
        </div>

    </form>
</div>

@endif



{{-- ========================= --}}
{{-- Final Actions – Execution Card --}}
{{-- ========================= --}}
<div class="card" id="block-d">

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
        const value = e.target.value;
        const arabicPattern = /[\u0600-\u06FF]/;

        if (arabicPattern.test(value)) {
            e.target.value = value.replace(/[\u0600-\u06FF]/g, '');
            alert('هذا الحقل يقبل اللغة الإنكليزية فقط.');
        }
    }
});
</script>

@endsection
