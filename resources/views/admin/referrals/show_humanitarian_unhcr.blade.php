@extends('admin.layouts.app')

@section('title', 'Referral – Humanitarian Protection (UNHCR)')

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
        المفوضية السامية للأمم المتحدة لشؤون اللأجئين (UNHCR)
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
        ولا يتم إرفاقها ضمن إحالات المفوضية السامية للأمم المتحدة لشؤون اللأجئين (UNHCR)
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
        القسم الأول: وصف الوضع الإنساني والحماية
        <span class="subtitle">
            Description of the Humanitarian Situation (UNHCR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توثيق <strong>الوصف الإنساني ووضع الحماية كما ورد إلى المنظمة من المصدر</strong>،
        مع التركيز على أوضاع النزوح، مخاطر الحماية، واحتياجات الفئات المتأثرة،
        وذلك <strong>لدعم التقييم الإنساني والحمايتي</strong> ضمن ولاية المفوضية السامية لشؤون اللاجئين.
        <br><br>
        يجب أن يكون النص وصفيًا، حياديًا، وغير قانوني، ويعكس ما نقله المصدر دون تحليل أو استنتاج.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_source', $referral->id) }}">
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
                <li>✔ صف الوضع الإنساني والحمايتي كما نقله المصدر (نزوح، ضعف، مخاطر، وصول للخدمات)</li>
                <li>✔ استخدم لغة إنسانية حيادية مناسبة لتقارير UNHCR</li>
                <li>✔ ركّز على أوضاع المدنيين، النازحين، أو المعرّضين لخطر النزوح</li>
                <li>✔ اكتب بصيغة نقل غير مباشر لما أفاد به المصدر</li>
                <li>❌ لا تذكر أسماء أشخاص أو عائلات أو مواقع دقيقة</li>
                <li>❌ لا تستخدم مصطلحات قانونية أو توصيفات جنائية</li>
                <li>❌ لا تشير إلى مسؤوليات أو اتهامات</li>
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
            Location and Timeframe (UNHCR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى تحديد <strong>النطاق الجغرافي العام</strong> و<strong>الإطار الزمني التقريبي</strong>
        للوضع الإنساني أو الحمايتي المبلّغ عنه، بما يدعم الفهم العام للسياق
        دون تعريض المصدر أو المتأثرين لأي مخاطر أمنية.
        <br><br>
        يجب تجنّب التفاصيل الدقيقة، والتركيز على توصيف عام مناسب لأغراض
        التقييم الإنساني والحمايتي.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_location_time', $referral->id) }}">
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
                <li>✔ استخدم توصيفًا جغرافيًا عامًا (منطقة، محافظة، إقليم)</li>
                <li>✔ تجنّب ذكر قرى محددة أو أحياء أو إحداثيات دقيقة</li>
                <li>✔ استخدم إطارًا زمنيًا تقريبيًا (شهر، فترة، منذ تاريخ)</li>
                <li>✔ اربط الموقع والزمن بالسياق الإنساني أو الحمايتي فقط</li>
                <li>❌ لا تذكر عناوين دقيقة أو مواقع حساسة</li>
                <li>❌ لا تستخدم تواريخ تفصيلية إذا لم تكن ضرورية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت:
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
{{-- Block C – Needs --}}
{{-- ========================= --}}
@if($steps->location_saved && $steps->timeframe_saved)
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: الاحتياجات الإنسانية المحددة
        <span class="subtitle">
            Identified Humanitarian Needs (UNHCR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توثيق <strong>الاحتياجات الإنسانية والحمايتية الأساسية</strong>
        التي أبلغ عنها المصدر أو التي يمكن استخلاصها مباشرة من وصف الوضع الإنساني،
        وذلك لدعم تقييم الاحتياجات والتخطيط للاستجابة الإنسانية.
        <br><br>
        يجب أن تركز الصياغة على <strong>الاحتياجات العملية والإنسانية</strong> دون
        تحليل سياسي أو توصيف قانوني.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_needs', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="humanitarian_needs_en" 
                rows="5" 
                required 
                data-en-only
                class="form-control"
            >{{ old('humanitarian_needs_en', $data->humanitarian_needs_en ?? '') }}</textarea>
            @error('humanitarian_needs_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اذكر الاحتياجات الإنسانية الأساسية (مأوى، غذاء، صحة، مياه، حماية)</li>
                <li>
                    ✔ يُعبّأ هذا الحقل على شكل أسطر، حيث يُعرض كل سطر كبند مستقل ضمن التقرير النهائي.
                    <br>
                    <em>مثال:</em>
                    <br>
                    <em>Limited access to basic food supplies</em><br>
                    <em>Shortages of essential medicines</em>
                </li>
                <li>✔ اربط الاحتياجات بالوضع الميداني وتأثيره على المدنيين</li>
                <li>✔ استخدم لغة عملية ومباشرة</li>
                <li>❌ لا تستخدم توصيفًا قانونيًا أو مصطلحات حقوقية</li>
                <li>❌ لا تذكر جهات مسؤولة أو اتهامات</li>
                <li>❌ لا تدرج مطالب سياسية أو إعلامية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت:
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
            Immediate Risks (UNHCR)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توضيح <strong>المخاطر الإنسانية والحمايتية الفورية</strong>
        التي قد يواجهها المدنيون في حال استمرار الوضع الحالي دون تدخل أو دعم مناسب.
        <br><br>
        يجب أن تركز الصياغة على المخاطر العملية المتوقعة قصيرة المدى،
        خاصة تلك المتعلقة بالسلامة، النزوح، الصحة، أو التدهور الإنساني.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_risks', $referral->id) }}">
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
                <li>✔ ركّز على المخاطر القريبة والملموسة (نزوح إضافي، تدهور صحي، انعدام الأمن)</li>
                <li>✔ اربط المخاطر مباشرة باستمرار الوضع الحالي</li>
                <li>✔ استخدم لغة توقعية إنسانية (may face, risk of, increased vulnerability)</li>
                <li>✔ يمكن ذكر الفئات الأكثر عرضة للخطر (أطفال، نساء، مسنون)</li>
                <li>❌ لا تستخدم لغة اتهامية أو سياسية</li>
                <li>❌ لا تذكر جهات فاعلة أو مسؤوليات</li>
                <li>❌ لا تستخدم مصطلحات قانونية أو حقوقية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت:
            <em>7. Immediate Risks</em>
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
{{-- Block E – Mandate --}}
{{-- ========================= --}}
@if($steps->risks_saved)
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: صلة الحالة بتفويض المفوضية السامية للأمم المتحدة لشؤون اللاجئين
        <span class="subtitle">
            Relevance to UNHCR Mandate
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا الحقل إلى توضيح <strong>سبب ارتباط هذه الحالة بتفويض UNHCR</strong>،
        من منظور الحماية الإنسانية، النزوح، أو التعرض لمخاطر تؤثر على المدنيين
        داخل أو خارج مناطق إقامتهم الأصلية.
        <br><br>
        يجب أن توضّح الصياغة كيف تندرج الحالة ضمن مجالات عمل UNHCR
        دون توصيف قانوني أو تحميل مسؤوليات.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_mandate', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="mandate_relevance_en" 
                rows="3" 
                required 
                data-en-only
                class="form-control"
            >{{ old('mandate_relevance_en', $data->mandate_relevance_en ?? '') }}</textarea>
            @error('identified_concerns_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اربط الحالة بمفاهيم الحماية، النزوح، أو انعدام الأمان المدني</li>
                <li>✔ استخدم لغة تفويضية عامة (falls within UNHCR’s mandate, raises protection concerns)</li>
                <li>✔ يمكن الإشارة إلى خطر النزوح أو انعدام سبل الحماية</li>
                <li>✔ حافظ على صيغة تحليل إنساني غير قانوني</li>
                <li>❌ لا تستخدم مصطلحات قانونية (violation, breach, responsibility)</li>
                <li>❌ لا تذكر دول أو أطراف أو جهات فاعلة</li>
                <li>❌ لا تحوّل الفقرة إلى طلب مساعدة (ذلك في البلوك التالي)</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت:
            <em>8. Relevance to UNHCR Mandate</em>
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
{{-- Block F – Snapshot --}}
{{-- ========================= --}}
@if($steps->mandate_saved)
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: لمحة عن الحالة / ملخص الحالة (اختياري)
        <span class="subtitle">
            Protection / Case Snapshot (Optional)
        </span>
    </div>

    <div class="info-box info-pin">
        <span class="info-icon">●</span>
    هذا القسم<strong>اختياري</strong>ويستخدم فقط عند وجود معلومات ذات صلة مباشرة بالحماية
    </div>

    <p class="page-subtitle">
        يتيح هذا الحقل إضافة <strong>لمحة موجزة</strong> عن الحالة من زاوية الحماية الإنسانية،
        أو الإشارة إلى عناصر خاصة تستحق الانتباه ولم تُذكر تفصيليًا في الأقسام السابقة.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_snapshot', $referral->id) }}">
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
                <li>✔ استخدم فقرة قصيرة ومباشرة</li>
                <li>✔ ركّز على زوايا حماية لم تُغطَّ سابقًا</li>
                <li>✔ يمكن إبراز فئة ضعيفة أو ظرف خاص (نزوح متكرر، فقدان مأوى)</li>
                <li>✔ حافظ على لغة إنسانية حيادية</li>
                <li>❌ لا تكرّر نصوص البلوكات السابقة</li>
                <li>❌ لا تضف تحليلاً قانونيًا أو مطالب</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا القسم في التقرير النهائي تحت:
            <em>9. Protection / Case Snapshot (Optional)</em>
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
        يهدف هذا الحقل إلى تحديد <strong>نوع المساعدة الإنسانية المطلوبة</strong>
        بشكل عام، استنادًا إلى الوضع الإنساني الموصوف في الأقسام السابقة.
        <br><br>
        يجب أن يكون الطلب بصيغة <strong>غير إلزامية</strong>، إنسانية، ومتصلة مباشرة
        بالاحتياجات والمخاطر المذكورة أعلاه.
    </p>

    <form method="POST" action="{{ route('admin.referrals.humanitarian.unhcr.save_assistance', $referral->id) }}">
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
                <li>✔ استخدم صيغة طلب عامة (assessment / support / assistance)</li>
                <li>✔ اربط الطلب بالاحتياجات الإنسانية دون إعادة شرح الحالة</li>
                <li>✔ يمكن ذكر مجالات مثل: health, shelter, food, protection</li>
                <li>✔ اجعل النص موجزًا وواضحًا</li>
                <li>❌ لا تطلب إجراءات محددة أو إلزامية</li>
                <li>❌ لا تُسمِّ جهات مسؤولة أو أطراف</li>
                <li>❌ لا تستخدم لغة سياسية أو قانونية</li>
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
