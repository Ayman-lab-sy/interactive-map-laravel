@extends('admin.layouts.app')

@section('title', 'Referral – Humanitarian Protection (ICRC)')

@section('content')

@php
$readyForGeneration =
    isset($steps)
    && $steps->source_saved
    && $steps->location_saved
    && $steps->timeframe_saved
    && $steps->needs_saved
    && $steps->risks_saved
    && $steps->mandate_saved
    && $steps->assistance_saved;
@endphp

<div class="page-title">
    إحالة – الحماية الإنسانية
    <div class="page-subtitle">
        اللجنة الدولية للصليب الأحمر (ICRC)
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
        ولا يتم إرفاقها ضمن إحالات اللجنة الدولية للصليب الأحمر (ICRC)
    </div>

    <div class="info-box muted" style="margin-top:24px;">
        <span class="info-icon">!</span>
         انتهى عرض المعلومات المرجعية للحالة.
        الأقسام التالية مخصصة لإعداد نص الإحالة.
    </div>

</div>

{{-- ========================= --}}
{{-- Block A – Source --}}
{{-- ========================= --}}
<div id="block-a" class="card" data-block="A">

    <div class="section-title">
        القسم الأول: وصف الوضع الإنساني
        <span class="subtitle">
            Description of the Humanitarian Situation
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى نقل <strong>الوصف الإنساني للحالة كما ورد إلى المنظمة من المصدر</strong>.
        يجب أن يعبّر النص عن طبيعة الوضع الإنساني والمعاناة التي أبلغ عنها المصدر، 
        دون ذكر أسماء، ودون تحليل قانوني أو اتهام مباشر.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_source', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="source_account_en" 
                rows="4" 
                required 
                data-en-only
                class="form-control"
            >{{ old('source_account_en', $data->source_account_en ?? '') }}</textarea>
            @error('source_account_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف الوضع الإنساني كما رواه المصدر (ظروف المعيشة، المعاناة، الاحتياجات، المخاطر)</li>
                <li>✔ استخدم لغة وصفية حيادية تعكس الواقع الإنساني فقط</li>
                <li>✔ اكتب كما لو أنك تنقل ما قاله المصدر بصيغة غير مباشرة</li>
                <li>✔ يمكن ذكر التأثير على المدنيين (صحة، غذاء، مأوى، أمان)</li>
                <li>❌ لا تذكر أسماء أشخاص أو جهات</li>
                <li>❌ لا تستخدم توصيفًا قانونيًا أو مصطلحات حقوقية</li>
                <li>❌ لا تذكر "انتهاك" أو "مسؤولية" أو "قانون دولي"</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>3. Source Account (As Received)</em>
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
{{-- Block B – Location & Time --}}
{{-- ========================= --}}
@if($steps->source_saved)
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: الموقع والإطار الزمني
        <span class="subtitle">
            Location and Timeframe (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى تحديد <strong>الإطار المكاني والزمني العام</strong> للحالة
        كما وردت في المعلومات المستلمة، وذلك لأغراض الفهم الإنساني والسياق العام فقط.
    </p>
    
    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_location_time', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="general_location_en" 
                rows="3" 
                required data-en-only
                placeholder="General location"
                class="form-control form-control--sm"
            >{{ old('general_location_en', $data->general_location_en ?? '') }}</textarea>
            @error('general_location_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

            <textarea 
                name="incident_timeframe_en" 
                rows="3" 
                required data-en-only
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
                <li>✔ اذكر الموقع بصيغة عامة (مدينة / منطقة / محافظة)</li>
                <li>✔ تجنّب ذكر عناوين دقيقة أو تفاصيل قد تكشف هوية أشخاص أو أماكن حساسة</li>
                <li>✔ الإطار الزمني يجب أن يكون وصفيًا (تاريخ تقريبي، فترة زمنية، مستمر حتى الآن)</li>
                <li>✔ يمكن استخدام تعبيرات مثل <em>(early 2025 / over recent months / ongoing)</em></li>
                <li>❌ لا تستخدم إحداثيات دقيقة أو تفاصيل ميدانية حساسة</li>
                <li>❌ لا تُدرج تحليلات أو استنتاجات ضمن هذا القسم</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            ستظهر هذه المعلومات في التقرير النهائي تحت القسم:
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
{{-- Block C – Humanitarian Needs --}}
{{-- ========================= --}}
@if($steps->location_saved && $steps->timeframe_saved)
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: الاحتياجات الإنسانية المحددة
        <span class="subtitle">
            Identified Humanitarian Needs (EN)
        </span>
    </div>

    <p class="page-subtitle">       
        يهدف هذا القسم إلى توضيح <strong>الاحتياجات الإنسانية الأساسية</strong>
        التي برزت من المعلومات الواردة، كما فُهمت على المستوى الإنساني
        دون توصيف قانوني أو تحليلي.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_needs', $referral->id) }}">
        @csrf
        <div class="form-group">
        <textarea name="humanitarian_needs_en" rows="5" required data-en-only
            style="width:100%;padding:12px;border:1px solid #cbd5f5;border-radius:6px;">{{ old('humanitarian_needs_en', $data->humanitarian_needs_en ?? '') }}</textarea>
            @error('source_account_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>
                    ✔ يُعبّأ هذا الحقل على شكل أسطر، حيث يُعرض كل سطر كبند مستقل ضمن التقرير النهائي.
                    <br>
                    <em>مثال:</em>
                    <br>
                    <em>Limited access to basic food supplies</em><br>
                    <em>Shortages of essential medicines</em>
                </li>
                <li>✔ اذكر الاحتياجات الإنسانية بشكل وصفي ومباشر (مثل: الغذاء، الدواء، الرعاية الصحية، المأوى، المياه، الحماية).</li>
                <li>✔ اعتمد فقط على ما ورد في المعلومات المستلمة من المصدر دون افتراض أو استنتاج إضافي.</li>
                <li>✔ يمكن إدخال أكثر من احتياج، كل احتياج في سطر مستقل.</li>
                <li>✔ استخدم لغة إنسانية محايدة وغير اتهامية.</li>
                <li>❌ لا تذكر أسباب أو تحليلات سياسية أو عسكرية.</li>
                <li>❌ لا تُقيّم مسؤولية أي جهة أو طرف.</li>
                <li>❌ لا تستخدم مصطلحات قانونية أو حقوقية.</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>6. Identified Humanitarian Needs</em>
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
{{-- Block D – Risks --}}
{{-- ========================= --}}
@if($steps->needs_saved)
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: المخاطر الفورية على المدنيين
        <span class="subtitle">
            Immediate Risks
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح <strong>المخاطر الإنسانية الفورية</strong>
        التي قد يتعرض لها المدنيون في حال استمرار الوضع الحالي دون تدخل
        أو دعم إنساني مناسب.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_risks', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="immediate_risks_en" 
                rows="4" 
                required 
                data-en-only
                class="form-control"
             >{{ old('immediate_risks_en', $data->immediate_risks_en ?? '') }}</textarea>
        
            @error('immediate_risks_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف المخاطر المحتملة بشكل إنساني ووقائي (تدهور صحي، نزوح، تعرّض للخطر)</li>
                <li>✔ اربط المخاطر بالوضع الإنساني القائم وليس بأسباب سياسية أو عسكرية</li>
                <li>✔ استخدم لغة وصفية غير اتهامية (may face / could be exposed to)</li>
                <li>✔ ركّز على الأثر المحتمل على المدنيين، خصوصًا الفئات الأكثر ضعفًا</li>
                <li>❌ لا تُسمِّ جهات مسؤولة</li>
                <li>❌ لا تستخدم توصيفات قانونية أو حقوقية</li>
                <li>❌ لا تُطلق أحكامًا أو استنتاجات نهائية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>7. Immediate Risks to Civilians</em>
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
{{-- Block E – ICRC Mandate --}}
{{-- ========================= --}}
@if($steps->risks_saved)
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: الصلة بالولاية الإنسانية للصليب الأحمر
        <span class="subtitle">
            Relevance to ICRC Mandate
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح <strong>لماذا تُعد هذه الحالة ذات صلة مباشرة
        بالولاية الإنسانية للجنة الدولية للصليب الأحمر (ICRC)</strong>،
        من زاوية إنسانية بحتة، دون أي توصيف قانوني أو سياسي.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_mandate', $referral->id) }}">
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
                <li>✔ اربط الحالة بتأثيرها على سلامة المدنيين واحتياجاتهم الإنسانية</li>
                <li>✔ اذكر عناصر مثل: الحماية، الوصول للخدمات الأساسية، المساعدة الإنسانية</li>
                <li>✔ استخدم لغة عامة ومحايدة (falls within the humanitarian mandate)</li>
                <li>✔ يمكن الإشارة إلى سياق عنف أو نزاع دون تحديد أطراف أو مسؤوليات</li>
                <li>❌ لا تستخدم مصطلحات قانون دولي أو التزامات قانونية</li>
                <li>❌ لا تذكر انتهاكات أو خروقات أو اتهامات</li>
                <li>❌ لا تطلب إجراءات محددة أو إلزامية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>8. Relevance to the ICRC Humanitarian Mandate</em>
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
{{-- Block F – Case Snapshot (Optional) --}}
{{-- ========================= --}}
@if($steps->mandate_saved)
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: لمحة عن الحالة/ ملخص تشغيلي للحالة (اختياري)
        <span class="subtitle">
            Protection / Case Snapshot (Optional)
        </span>
    </div>

    <p class="page-subtitle">
        يُستخدم هذا القسم <strong>اختياريًا</strong> لتقديم لمحة سريعة
        ومركّزة عن وضع الحماية أو طبيعة الحالة من زاوية تشغيلية،
        في حال كانت هناك <strong>مخاوف حماية واضحة أو طابع استعجالي</strong>.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_snapshot', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="case_snapshot_en" 
                rows="4" 
                data-en-only
                class="form-control"
            >{{ old('case_snapshot_en', $data->case_snapshot_en ?? '') }}</textarea>
        
            @error('case_snapshot_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ استخدم هذا الحقل فقط إذا كانت هناك مخاوف حماية محددة أو طارئة</li>
                <li>✔ يمكن ذكر الوضع العام (مدنيون مهددون / نازحون / موقوفون / فئة ضعيفة)</li>
                <li>✔ يمكن ذكر مستوى الاستعجال (High / Medium)</li>
                <li>✔ استخدم لغة مختصرة، وصفية، وغير اتهامية</li>
                <li>❌ لا تُعد شرح الحالة من جديد</li>
                <li>❌ لا تستخدم توصيفات قانونية أو مصطلحات حقوقية</li>
                <li>❌ لا تذكر أسماء أشخاص أو جهات أو تفاصيل حساسة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>9. Protection / Case Snapshot (Optional)</em>
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
{{-- Block G – Assistance --}}
{{-- ========================= --}}
@if($steps->mandate_saved)
<div id="block-g" class="card" data-block="G">

    <div class="section-title">
        القسم السابع:  طلب المساعدة الإنسانية
        <span class="subtitle">
            Requested Humanitarian Assistance
        </span>
    </div>

    <p class="page-subtitle">
        يُستخدم هذا القسم لتوضيح <strong>نوع المساعدة الإنسانية المطلوبة</strong>
        بشكل عام، استنادًا إلى الوضع الموصوف سابقًا،
        دون تحديد آليات تنفيذ أو توجيه أو تحميل مسؤوليات.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.icrc.save_assistance', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="assistance_requested_en" 
                rows="4" 
                required 
                data-en-only
                class="form-control"
            >{{ old('assistance_requested_en', $data->assistance_requested_en ?? '') }}</textarea>
        
            @error('assistance_requested_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ استخدم صيغة طلب إنساني عامة (assessment / assistance / support)</li>
                <li>✔ يمكن الإشارة إلى مجالات عامة مثل (health / food / shelter / protection)</li>
                <li>✔ استخدم لغة حيادية وغير ملزمة</li>
                <li>✔ يمكن أن يكون النص سطرًا واحدًا أو فقرة قصيرة</li>
                <li>❌ لا تُعيد شرح الحالة</li>
                <li>❌ لا تستخدم لغة اتهامية أو سياسية</li>
                <li>❌ لا تطلب إجراءات محددة أو إلزامية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت القسم:
            <em>10. Requested Humanitarian Assistance</em>
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
