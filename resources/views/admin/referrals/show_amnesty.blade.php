@extends('admin.layouts.app')

@section('title', 'Amnesty International – Referral')

@section('content')

@php
$amnestyData = \Illuminate\Support\Facades\DB::connection('cases')
    ->table('case_referral_ngo_amnesty')
    ->where('referral_id', $referral->id)
    ->first();

$readyForGeneration =
    !empty($amnestyData?->source_account_en) &&
    !empty($amnestyData?->general_location_en) &&
    !empty($amnestyData?->incident_timeframe_en) &&
    !empty($amnestyData?->violation_summary_en) &&
    !empty($amnestyData?->psychosocial_impact_en);
@endphp

<div class="page-title">
    إحالة – المسار القانوني
    <div class="page-subtitle">
        منظمة العفو الدولية (Amnesty International)
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
        ولا يتم إرفاقها ضمن إحالات منظمة العفو الدولية (Amnesty International)
    </div>

    <div class="info-box muted" style="margin-top:24px;">
        <span class="info-icon">!</span>
         انتهى عرض المعلومات المرجعية للحالة.
        الأقسام التالية مخصصة لإعداد نص الإحالة.
    </div>

</div>


{{-- ===============================
   Section 1: Source Account
================================ --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: شهادة المصدر (كما وردت)
        <span class="subtitle">
            Source Account
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى <strong>نقل شهادة المصدر كما وردت حرفيًا</strong>
        إلى منظمة Amnesty International، دون أي تعديل أو إعادة صياغة أو
        تحليل من قبل المحرر.
        <br><br>
        يجب أن يعكس النص أدناه رواية المصدر للأحداث كما قُدِّمت،
        مع الحفاظ على الصياغة الوصفية وعدم إدخال أي توصيف قانوني
        أو استنتاجات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.amnesty.save-source', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="source_account_en"
                rows="10"
                required
                data-en-only
                style="width:100%;padding:10px;border:1px solid #cbd5f5;border-radius:6px;"
                placeholder="Reproduce the source’s account as reported, without interpretation, legal qualification, or attribution."
            >{{ old('source_account_en', $data->source_account_en ?? '') }}</textarea>
            @error('source_account_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ انسخ شهادة المصدر كما وردت دون إعادة صياغة</li>
                <li>✔ استخدم عبارات المصدر الأصلية قدر الإمكان</li>
                <li>✔ يمكن استخدام صيغة <em>(according to the source)</em> عند الحاجة</li>
                <li>❌ لا تُحلّل الشهادة أو تُقيّم مصداقيتها</li>
                <li>❌ لا تستخدم أي مصطلحات قانونية أو توصيفات اتهامية</li>
                <li>❌ لا تضف معلومات لم ترد في شهادة المصدر</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>Source Account (As Reported)</em>
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

{{-- ===============================
   Section 2: Case Summary (Prepared by Organization)
================================ --}}
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: ملخص الحالة
        <span class="subtitle">
            Case Summary
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى تقديم <strong>ملخص تحريري محايد</strong>
        للحالة كما فُهمت من شهادة المصدر، وذلك لأغراض
        التوثيق الداخلي لدى Amnesty International.
        <br><br>
        يجب أن يعكس الملخص أدناه الفهم العام للوقائع
        <strong>دون إعادة نسخ شهادة المصدر حرفيًا</strong>،
        ودون إدخال أي توصيف قانوني أو استنتاجات.
    </p>
    <form method="POST"
          action="{{ route('admin.referrals.amnesty.save-summary', $referral->id) }}">
        @csrf

        {{-- General Location --}}
        <div class="form-group">
            <label class="section-title">الموقع العام <span class="subtitle">General Location</span></label>
            <textarea 
                name="general_location_en"
                rows="2"
                required
                data-en-only
                placeholder="e.g. Somewhere in Damascus, Syria"
                class="form-control form-control--sm"
            >{{ old('general_location_en', $data->general_location_en ?? '') }}</textarea>
            @error('general_location_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
                
            <div class="info-box info-info">
            <span class="info-icon">!</span>
                 اذكر الموقع بصيغة عامة دون عناوين دقيقة أو تفاصيل حساسة.
            </div>
        </div>

        {{-- Incident Timeframe --}}
        <div class="form-group">
            <label class="section-title">الإطار الزمني<span class="subtitle">Incident Timeframe</span></label>
            <textarea 
                name="incident_timeframe_en"
                rows="2"
                required
                data-en-only
                placeholder="e.g. Between March and April 2023"
                class="form-control form-control--sm"
            >{{ old('incident_timeframe_en', $data->incident_timeframe_en ?? '') }}</textarea>
            @error('incident_timeframe_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <div class="info-box info-info">
            <span class="info-icon">!</span>
                 يمكن استخدام إطار زمني تقريبي كما ورد في الشهادة.
            </div>
        </div>

        {{-- Nature of Incident --}}
        <div class="form-group">
            <label class="section-title">طبيعة الحادث<span class="subtitle">Nature of the Reported Incident</span></label>
            <textarea 
                name="violation_summary_en"                   
                rows="4"                     
                required                    
                data-en-only                     
                class="form-control"
            >{{ trim(old('violation_summary_en', $data->violation_summary_en ?? '')) }}</textarea>
            @error('violation_summary_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <div class="info-box info-info">
            <span class="info-icon">!</span>
                 إرشادات تحريرية:
                <ul>
                    <li>✔ لخص الوقائع بأسلوب وصفي ومحايد</li>
                    <li>✔ استخدم صياغة مثل <em>(reportedly / according to the source)</em></li>
                    <li>✔ ركّز على ما حدث دون تحليل أو توصيف قانوني</li>
                    <li>❌ لا تستخدم مصطلحات قانونية أو اتهامية</li>
                    <li>❌ لا تُقيِّم مشروعية الأفعال أو مسؤولية أي جهة</li>
                </ul>
            </div>
        </div>

        {{-- Psychosocial Impact --}}
        <div class="form-group">
            <label class="section-title">الأثر النفسي<span class="subtitle">Reported Psychosocial Impact</span></label>
            <textarea 
                name="psychosocial_impact_en"
                rows="4"
                required
                data-en-only
                class="form-control"
            >{{ old('psychosocial_impact_en', $data->psychosocial_impact_en ?? '') }}</textarea>
            @error('psychosocial_impact_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <div class="info-box info-info">
            <span class="info-icon">!</span>
                 اذكر الأثر النفسي أو الاجتماعي كما ورد في الشهادة،
                دون تقييم طبي أو استنتاج.
            </div>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت عنوان:
            <em>Case Summary (Prepared by the Organization)</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             جميع الحقول مخصصة للإدخال باللغة الانكليزية
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">حفظ نهائي</button>
        </div>
    </form>
</div>

{{-- ===============================
   Section 3: Optional / Internal
================================ --}}
{{-- <div class="card" style="margin-bottom:20px;">
    <div style="font-weight:700;margin-bottom:10px;">
        ملاحظات داخلية (اختياري)
    </div>
    <div style="font-size:14px;color:#334155;margin-bottom:12px;line-height:1.7;">
        هذا القسم مخصص <strong>للاستخدام الداخلي فقط</strong> من قبل فريق المنظمة،
        ولا يظهر ضمن التقرير النهائي، ولا يتم إرساله إلى
        Amnesty International.
    </div>
    <form method="POST"
          action="{{ route('admin.referrals.amnesty.save-optional', $referral->id) }}">
        @csrf

        <div style="margin-bottom:14px;">
            <label style="font-weight:600;">اعتبارات حقوق الانسان (اختياري)</label>
            <textarea name="human_rights_considerations_en"
                      rows="4"
                      style="width:100%;padding:10px;border:1px solid #cbd5f5;border-radius:6px;"
                      placeholder="ملاحظات داخلية فقط. ليست جزء من التقرير">
            {{ old('human_rights_considerations_en', $data->human_rights_considerations_en ?? '') }}</textarea>
        </div>

        <div style="margin-bottom:14px;">
            <label>مستوى التحقق</label>
            <select name="verification_level">
                <option value="UNVERIFIED">غير مؤكدة</option>
                <option value="BASIC_REVIEW">مراجعة أساسية</option>
                <option value="INTERNAL_REVIEW">مراجعة داخلية</option>
                <option value="ENHANCED_REVIEW">مراجعة محسنة</option>
            </select>
        </div>

        <div style="margin-bottom:14px;">
            <label style="font-weight:600;">ملخص المراجعة الداخلية</label>
            <textarea name="review_summary_en"
                      rows="3"
                      style="width:100%;padding:10px;border:1px solid #cbd5f5;border-radius:6px;"
                      placeholder="ملاحظة صغيرة حول المراجعة">
                    {{ old('review_summary_en', $data->review_summary_en ?? '') }}</textarea>
        </div>

        <div style="margin-bottom:14px;">
            <label style="font-weight:600;">تاريخ المراجعة</label>
            <input type="date" name="review_date">
        </div>

        <div style="margin-bottom:14px;">
            <label>
                <input type="checkbox" name="has_supporting_materials" value="1">
                هل تم ارفاق المواد الداعمة من قبل المصدر
            </label>
        </div>

        <button class="btn-primary">
            حفظ الملاحظات الداخلية
        </button>
    </form>
</div> --}}

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

{{-- EN only guard --}}
<script>
document.addEventListener('input', function (e) {
    if (e.target.matches('[data-en-only]')) {
        const arabic = /[\u0600-\u06FF]/g;
        if (arabic.test(e.target.value)) {
            e.target.value = e.target.value.replace(arabic, '');
            alert('English only.');
        }
    }
});
</script>

@endsection
