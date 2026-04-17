@extends('admin.layouts.app')

@section('title', 'Referral – UN Special Procedures (Violence against Women)')

@section('content')

@php
$readyForGeneration =
    isset($steps)
    && $steps->summary_saved
    && $steps->victim_saved
    && $steps->violence_saved
    && $steps->perpetrators_saved
    && $steps->remedies_saved
    && $steps->context_done;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بالعنف ضد النساء والفتيات
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
        يُعد هذا القسم المدخل الرئيسي الذي تطّلع عليه
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
        يجب تقديم عرض واقعي ومختصر للوقائع المزعومة المتعلقة بأعمال عنف أو إساءة
        يُزعم أنها استهدفت امرأة أو فتاة، سواء في المجال العام أو الخاص،
        كما وردت في الشهادة، دون توصيف قانوني أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_summary', $referral->id) }}">
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
                <li>✔ قدّم سردًا واقعيًا ومختصرًا للوقائع المزعومة كما وردت في الشهادة</li>
                <li>✔ استخدم دائمًا صياغة حيادية مثل <em>(allegedly / reportedly / according to the source)</em></li>
                <li>✔ اذكر الزمان والمكان والسياق العام للواقعة بصيغة وصفية إن توفرت</li>
                <li>✔ صف طبيعة الواقعة دون توصيف قانوني أو تقييم للمشروعية</li>
                <li>✔ يمكن الإشارة إلى أشكال العنف أو الإساءة المزعومة بصيغة غير تقنية</li>
                <li>✔ التزم بالتسلسل الزمني للوقائع إن أمكن</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(gender-based violence, domestic violence, sexual violence)</em></li>
                <li>❌ لا تُسند مسؤولية قانونية أو نية متعمدة لأي جهة</li>
                <li>❌ لا تتضمن استنتاجات، مطالبات، أو توصيات</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
             سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>I. Summary of Alleged Facts</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>
    </form>
</div>

@if($steps->summary_saved)
{{-- ========================= --}}
{{-- Block B – Victim --}}
{{-- ========================= --}}
<div id="block-b" class="card" data-block="B">
    <div class="section-title">
        القسم الثاني: معلومات عن المرأة/الفتاة المعنية
        <span class="subtitle">
            Information on the Woman / Girl Concerned (EN)
        </span>
    </div>
    <p class="page-subtitle">
        يهدف هذا القسم إلى تقديم معلومات تعريفية أساسية عن المرأة أو الفتاة
        التي يُزعم أنها تعرّضت لأعمال عنف أو إساءة تدخل ضمن نطاق ولاية
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
        تُقدَّم المعلومات كما وردت في الشهادة، مع مراعاة اعتبارات السلامة
        والحماية، ودون توصيف قانوني أو تحميل مسؤولية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_victim', $referral->id) }}">
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
                <li>✔ اذكر الاسم فقط إذا كان معروفًا وآمنًا، أو استخدم توصيفًا عامًا عند الضرورة</li>
                <li>✔ يمكن ذكر العمر التقريبي، الحالة الاجتماعية، أو أي معلومات ديموغرافية عامة إن توفرت</li>
                <li>✔ صف المرأة أو الفتاة بصيغة حيادية <em>(is reported to be / was described as)</em></li>
                <li>✔ يمكن الإشارة إلى الدور الاجتماعي أو المهني إذا كان ذا صلة بالوقائع</li>
                <li>✔ التزم حصريًا بالمعلومات الواردة في الشهادة دون توسّع أو تفسير</li>
                <li>❌ لا تذكر عناوين دقيقة أو معلومات تعريفية حساسة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(victim of gender-based violence)</em></li>
                <li>❌ لا تُقيِّم طبيعة العنف أو خطورته في هذا القسم</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>II. Information on the Woman / Girl Concerned</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>
    </form>
</div>
@endif

@if($steps->victim_saved)
{{-- ========================= --}}
{{-- Block C – Violence --}}
{{-- ========================= --}}
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
      القسم الثالث: طبيعة وأشكال العنف المزعوم
        <span class="subtitle">
            Nature and Forms of Violence (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف طبيعة وأشكال العنف أو الإساءة
        التي يُزعم أن المرأة أو الفتاة المعنية قد تعرّضت لها،
        سواء كانت جسدية، نفسية، جنسية، اقتصادية، أو غير ذلك،
        بما في ذلك العنف المنزلي، التهديدات، أو العنف المرتبط
        بالنزاع، وذلك ضمن نطاق ولاية
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
        تُقدَّم المعلومات لأغراض وصفية فقط، دون توصيف قانوني
        أو استنتاجات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_violence', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="violence_description_en" 
                rows="6" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('violence_description_en', $data->violence_description_en ?? '') }}</textarea>
            @error('violence_description_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف طبيعة العنف أو الإساءة كما وردت في الشهادة دون إضافة أو تحليل</li>
                <li>✔ يمكن ذكر تكرار الأفعال، مدتها، أو شدتها بصيغة وصفية عامة</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(was reportedly subjected to / was described as experiencing)</em></li>
                <li>✔ يمكن الإشارة إلى السياق (منزلي، عام، مرتبط بالنزاع) إذا ورد في الشهادة</li>
                <li>✔ التزم حصريًا بالمعلومات المبلّغ عنها</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(gender-based violence, domestic violence)</em></li>
                <li>❌ لا تُقيِّم خطورة الأفعال أو آثارها القانونية</li>
                <li>❌ لا تستنتج وجود نية، سياسة، أو ممارسة ممنهجة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>III. Nature and Forms of Violence</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>
    </form>
</div>
@endif

@if($steps->violence_saved)
{{-- ========================= --}}
{{-- Block D – Perpetrators --}}
{{-- ========================= --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
      القسم الرابع: الجهة أو الأشخاص المزعوم تورطهم
        <span class="subtitle">
            Alleged Perpetrators (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى عرض المعلومات الواردة في الشهادة بشأن
        الجهة أو الأشخاص الذين يُزعم أنهم كانوا ضالعين في أعمال
        العنف أو الإساءة المبلغ عنها ضد المرأة أو الفتاة المعنية،
        وذلك ضمن نطاق ولاية
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
        تُقدَّم هذه المعلومات بصيغة غير اتهامية ولأغراض وصفية فقط،
        دون تحميل مسؤولية قانونية أو استخلاص استنتاجات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_perpetrators', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="alleged_perpetrators_en" 
                rows="5" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('alleged_perpetrators_en', $data->alleged_perpetrators_en ?? '') }}</textarea>
            @error('alleged_perpetrators_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اذكر الجهة أو الأشخاص كما وردوا في الشهادة فقط</li>
                <li>✔ استخدم توصيفات عامة وغير جازمة <em>(partner, family member, security personnel, armed individuals, unidentified persons)</em></li>
                <li>✔ يمكن ذكر العلاقة بالضحية أو الدور العام بصيغة وصفية إذا ورد في الشهادة</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(were reportedly involved / were described as responsible)</em></li>
                <li>✔ التزم حصريًا بالمعلومات المبلّغ عنها دون توسّع أو تفسير</li>
                <li>❌ لا تستخدم لغة اتهامية أو جازمة</li>
                <li>❌ لا تُسند مسؤولية قانونية أو أوامر مباشرة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(perpetrators of gender-based violence)</em></li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
             سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>IV. Alleged Perpetrators</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>
    </form>
</div>
@endif

@if($steps->perpetrators_saved)
{{-- ========================= --}}
{{-- Block E – Context --}}
{{-- ========================= --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: السياق أو النمط (اختياري)
        <span class="subtitle">
            Context / Pattern (Optional) (EN)
        </span>
    </div>

    <div class="info-box info-pin">
        <span class="info-icon">●</span>
        في حال عدم وجود مؤشرات على سياق أو نمط أوسع،
        يمكن تخطي هذا القسم دون أي أثر سلبي على الإحالة.
    </div>

    <p class="page-subtitle">
        يُستخدم هذا القسم فقط إذا أشارت المعلومات الواردة في الشهادة
        إلى وجود سياق أوسع أو نمط متكرر من أعمال العنف أو الإساءة
        ضد النساء أو الفتيات، سواء على مستوى الأسرة، المجتمع،
        أو في سياق أوسع ذي صلة، وذلك ضمن نطاق ولاية
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
        تُقدَّم هذه المعلومات لأغراض سياقية عامة فقط، دون الجزم
        بوجود سياسة رسمية أو ممارسة ممنهجة.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_context', $referral->id) }}">
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
                <li>✔ استخدم صياغة احتمالية وغير جازمة <em>(may indicate / appears to suggest / could reflect)</em></li>
                <li>✔ يمكن الإشارة إلى تكرار وقائع مشابهة ضد نساء أو فتيات أخريات إن ورد في الشهادة</li>
                <li>✔ يمكن الإشارة إلى عوامل اجتماعية أو سياقية عامة إذا ذُكرت كملاحظة عامة</li>
                <li>✔ قدّم المعلومات بوصفها سياقًا عامًا دون تعميم أو استنتاج</li>
                <li>❌ لا تستخدم لغة جازمة <em>(demonstrates / proves / confirms)</em></li>
                <li>❌ لا تستنتج وجود سياسة رسمية أو ممارسة ممنهجة</li>
                <li>❌ لا تستخدم توصيفات قانونية أو تقييمات قانونية</li>
            </ul>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>
    </form>
    
    <div class="form-actions">
        <form method="POST"
              action="{{ route('admin.referrals.un_sp.violence_against_women.skip_context', $referral->id) }}">
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
{{-- Block F – Remedies --}}
{{-- ========================= --}}
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: استنفاذ سبل الانصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح ما إذا كانت قد تمّت محاولة اللجوء
        إلى سبل الانتصاف أو الآليات المتاحة على المستوى المحلي
        استجابةً لأعمال العنف أو الإساءة المبلّغ عنها ضد امرأة
        أو فتاة، أو ما إذا كان اللجوء إلى هذه السبل غير ممكن
        أو ينطوي على مخاطر، وذلك كما ورد في الشهادة، ووفقًا
        لأساليب العمل المتّبعة ضمن ولاية
        <strong>Special Rapporteur on violence against women and girls, its causes and consequences</strong>.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.violence_against_women.save_remedies', $referral->id) }}">
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
                <li>✔ اذكر ما إذا تم تقديم شكاوى، بلاغات، أو طلب حماية كما ورد في الشهادة</li>
                <li>✔ يمكن الإشارة إلى اللجوء إلى الشرطة، القضاء، أو آليات حماية اجتماعية إن ذُكرت</li>
                <li>✔ في حال عدم اللجوء إلى سبل الانتصاف، اذكر الأسباب بصيغة وصفية حيادية</li>
                <li>✔ يمكن الإشارة إلى الخوف من الانتقام، الوصم الاجتماعي، أو مخاطر السلامة الشخصية</li>
                <li>✔ استخدم صياغة تفسيرية <em>(were reportedly pursued / were deemed unavailable / were not considered safe)</em></li>
                <li>❌ لا تُقيِّم استقلالية أو فعالية النظام القضائي أو الإداري</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفات قانونية</li>
                <li>❌ لا تستنتج إخلالًا بالتزامات قانونية أو دولية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>VI. Steps Taken / Remedies</em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             هذا الحقل مخصص للإدخال باللغة الإنكليزية فقط.
        </div>
        <div class="form-actions">
            <button class="btn btn-primary">
                حفظ والمتابعة
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