@extends('admin.layouts.app')

@section('title', 'Referral – UN Special Procedures (Minority Issues)')

@section('content')

@php
$readyForGeneration =
    isset($steps)
    && $steps->summary_saved
    && $steps->victim_saved
    && $steps->identity_saved
    && $steps->violation_saved
    && $steps->perpetrators_saved
    && $steps->remedies_saved
    && $steps->context_done;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بقضايا الأقليات
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
        <strong>Special Rapporteur on Minority Issues</strong>.
        يجب تقديم عرض واقعي ومختصر للوقائع المزعومة التي تمس حقوق
        أشخاص ينتمون إلى أقليات قومية، إثنية، لغوية أو دينية،
        كما وردت في الشهادة، مع إبراز طبيعة الأفعال أو التدابير
        التي يُزعم أنها أثّرت على تمتعهم بحقوقهم،
        دون توصيف قانوني أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_summary', $referral->id) }}">
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
                <li>✔ قدّم ملخصًا واقعيًا ومختصرًا للوقائع كما وردت في الشهادة</li>
                <li>✔ استخدم دائمًا صياغة حيادية مثل <em>(allegedly / reportedly / according to the source)</em></li>
                <li>✔ أوضح أن الوقائع تتعلق بأشخاص ينتمون إلى أقلية محددة (إن كانت معروفة)</li>
                <li>✔ اذكر طبيعة الأفعال أو التدابير المبلغ عنها (تمييز، استبعاد، تضييق، عنف، خطاب كراهية…)</li>
                <li>✔ اذكر السياق الزمني والمكاني بصيغة وصفية إن كان متوفرًا</li>
                <li>✔ يمكن الإشارة إلى الجهة أو الفاعل بصيغة غير اتهامية إذا ورد ذلك في الشهادة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(discrimination, violation, persecution)</em></li>
                <li>❌ لا تُسند نية أو سياسة ممنهجة أو مسؤولية قانونية لأي جهة</li>
                <li>❌ لا تتضمن تحليلاً قانونيًا أو مطالبات أو استنتاجات</li>
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
            <button class="btn btn-primary">حفظ والمتابعة</button>
        </div>
    </form>
</div>

{{-- ========================= --}}
{{-- Block B – Victim --}}
{{-- ========================= --}}
@if($steps->summary_saved)
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: معلومات عن الأفراد
        <span class="subtitle">
            Information on the Individual(s) (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى تقديم معلومات تعريفية أساسية عن الأفراد
        المتأثرين بالوقائع المبلغ عنها، والذين يُزعم أنهم ينتمون إلى
        أقلية قومية، إثنية، لغوية أو دينية، وذلك كما ورد في الشهادة.
        تُقدَّم المعلومات لأغراض وصفية فقط، مع مراعاة اعتبارات
        السلامة والحماية، ودون توصيف قانوني أو تحميل مسؤولية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_victim', $referral->id) }}">
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
                <li>✔ يمكن ذكر العمر التقريبي، الجنس، الجنسية، أو الانتماء المجتمعي إن ورد في الشهادة</li>
                <li>✔ أوضح انتماء الأفراد إلى أقلية محددة بصيغة وصفية وغير جازمة إن كان ذلك مذكورًا</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(the individual is reported to belong to / was described as)</em></li>
                <li>✔ التزم بالمعلومات الواردة في الشهادة دون توسّع أو استنتاج</li>
                <li>❌ لا تذكر عناوين دقيقة أو معلومات تعريفية حساسة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(victim of discrimination / minority rights violation)</em></li>
                <li>❌ لا تُقيِّم وضع الأقلية أو مستوى الحماية القانونية لها</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>II. Information on the Individual(s)</em>
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
{{-- Block C – Minority Identity --}}
{{-- ========================= --}}
@if($steps->victim_saved)
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: هوية الأقلية
        <span class="subtitle">
            Minority Identity (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح هوية الأقلية ذات الصلة بالوقائع
        المبلغ عنها، سواء كانت قومية، إثنية، لغوية أو دينية، وذلك
        كما ورد في الشهادة. تُقدَّم هذه المعلومات لأغراض وصفية
        فقط، دون تقييم قانوني أو استنتاج بشأن وجود تمييز أو انتهاك
        لحقوق الأقليات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_identity', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea
                name="minority_or_religious_identity_en"
                rows="4"
                required
                data-en-only
                class="form-control"
            >{{ old('minority_or_religious_identity_en', $data->minority_or_religious_identity_en ?? '') }}</textarea>
            @error('minority_or_religious_identity_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اذكر الانتماء القومي، الإثني، اللغوي أو الديني كما ورد في الشهادة فقط</li>
                <li>✔ استخدم صياغة وصفية وغير جازمة <em>(is reported to belong to / was described as)</em></li>
                <li>✔ يمكن ذكر التسمية الشائعة أو التعريف الذاتي للأقلية إن ورد في المصدر</li>
                <li>✔ التزم بالمعلومات المتاحة دون توسّع أو تفسير</li>
                <li>❌ لا تُقيِّم وضع الأقلية أو مستوى الحماية القانونية لها</li>
                <li>❌ لا تستخدم مصطلحات قانونية مثل <em>(minority rights violation, discrimination)</em></li>
                <li>❌ لا تستنتج وجود استهداف ممنهج أو سياسة عامة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>III. Minority Identity</em>
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

@if($steps->identity_saved)
{{-- ========================= --}}
{{-- Block D – Violation --}}
{{-- ========================= --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: طبيعة الأفعال أو التدابير المزعومة
        <span class="subtitle">
            Nature of the Alleged Acts or Measures (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف الأفعال، الممارسات أو التدابير
        التي يُزعم أنها أثّرت على حقوق أشخاص ينتمون إلى أقلية
        قومية، إثنية، لغوية أو دينية، كما وردت في الشهادة.
        تُقدَّم المعلومات بصيغة وصفية فقط، دون توصيف قانوني
        أو تقييم لمدى توافقها مع التزامات دولية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_violations', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="violation_description_en" 
                rows="5" 
                required 
                data-en-only 
                class="form-control"
                >{{ old('violation_description_en', $data->violation_description_en ?? '') }}</textarea>
            @error('violation_description_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
            
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف الأفعال أو التدابير كما وردت في الشهادة (منع، تقييد، مضايقة، استبعاد، خطاب تحريضي…)</li>
                <li>✔ اذكر كيف أثّرت هذه الأفعال على ممارسة الحقوق أو الحياة اليومية للأفراد المعنيين</li>
                <li>✔ استخدم صياغة غير جازمة <em>(were reportedly subjected to / were said to experience)</em></li>
                <li>✔ التزم بالوقائع المبلّغ عنها دون توسّع أو تحليل</li>
                <li>✔ يمكن ذكر الجهة أو الفاعل بصيغة وصفية غير اتهامية إذا ورد في الشهادة</li>
                <li>❌ لا تستخدم مصطلحات قانونية مثل <em>(violation of minority rights, discrimination, persecution)</em></li>
                <li>❌ لا تُقيِّم مشروعية الأفعال أو مدى مخالفتها للقانون الدولي</li>
                <li>❌ لا تستنتج وجود سياسة ممنهجة أو نمط تمييزي عام</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>IV. Nature of the Alleged Acts or Measures</em>
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

@if($steps->violation_saved)
{{-- ========================= --}}
{{-- Block E – Perpetrators --}}
{{-- ========================= --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: الجهة أو الأطراف المزعومة تورطها
        <span class="subtitle">
            Alleged Perpetrators (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى عرض المعلومات الواردة في الشهادة بشأن
        الجهة أو الأطراف التي يُزعم أنها كانت ضالعة في الأفعال أو
        التدابير المبلغ عنها، والتي أثّرت على أشخاص ينتمون إلى أقلية
        قومية، إثنية، لغوية أو دينية. تُقدَّم هذه المعلومات بصيغة
        غير اتهامية ولأغراض وصفية فقط.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_perpetrators', $referral->id) }}">
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
                <li>✔ اذكر الجهة أو الأطراف كما وردت في الشهادة فقط</li>
                <li>✔ استخدم توصيفات عامة وغير جازمة <em>(state authorities, local officials, unidentified individuals)</em></li>
                <li>✔ يمكن ذكر الصفة أو الدور الوظيفي بصيغة وصفية إذا ورد في المصدر</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(were reportedly involved / were said to be responsible)</em></li>
                <li>✔ التزم بالمعلومات المبلّغ عنها دون توسّع أو تفسير</li>
                <li>❌ لا تستخدم لغة اتهامية أو جازمة</li>
                <li>❌ لا تُسند مسؤولية قانونية أو أوامر مباشرة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(perpetrators of discrimination, violators of minority rights)</em></li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>V. Alleged Perpetrators</em>
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
{{-- ========================= --}}
{{-- Block F – Context --}}
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

    <p class="page-subtitle">
        يُستخدم هذا القسم فقط إذا أشارت المعلومات الواردة في الشهادة
        إلى وجود سياق عام، ظروف أوسع، أو تكرار وقائع مشابهة
        أثّرت على أشخاص ينتمون إلى نفس الأقلية. تُقدَّم هذه
        المعلومات لأغراض سياقية عامة فقط، دون الجزم بوجود
        سياسة ممنهجة أو ممارسة تمييزية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_context', $referral->id) }}">
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
                <li>✔ يمكن الإشارة إلى وقائع مشابهة وردت في الشهادة أو إلى ظروف عامة محيطة</li>
                <li>✔ قدّم المعلومات بوصفها ملاحظات سياقية فقط</li>
                <li>✔ التزم بما ورد في الشهادة دون توسّع أو استنتاج</li>
                <li>❌ لا تستخدم لغة جازمة مثل <em>(demonstrates / confirms / proves)</em></li>
                <li>❌ لا تستنتج وجود سياسة رسمية أو نمط تمييزي ممنهج</li>
                <li>❌ لا تستخدم توصيفات قانونية أو تقييمات حقوقية</li>
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
              action="{{ route('admin.referrals.un_sp.minority_issues.skip_context', $referral->id) }}">
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
{{-- Block G – Remedies --}}
{{-- ========================= --}}
<div id="block-g" class="card" data-block="G">

    <div class="section-title">
        القسم السابع: استنفاد سبل الانتصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح ما إذا كانت قد تمّت محاولة
        اللجوء إلى سبل الانتصاف أو الآليات المتاحة على المستوى
        المحلي ردًا على الوقائع المبلغ عنها، أو ما إذا كان
        اللجوء إليها غير ممكن، غير فعّال، أو ينطوي على مخاطر،
        وذلك كما ورد في الشهادة ووفقًا لأساليب العمل المتّبعة
        ضمن ولاية
        <strong>Special Rapporteur on Minority Issues</strong>.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.minority_issues.save_remedies', $referral->id) }}">
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
                <li>✔ اذكر ما إذا تم تقديم شكاوى، بلاغات، أو مراجعات رسمية كما ورد في الشهادة</li>
                <li>✔ في حال عدم اللجوء إلى سبل الانتصاف، اذكر السبب بصيغة وصفية حيادية</li>
                <li>✔ يمكن الإشارة إلى مخاطر محتملة، الخوف من الانتقام، أو عدم فعالية السبل المتاحة</li>
                <li>✔ استخدم صياغة تفسيرية <em>(were reportedly pursued / were deemed unavailable / were not considered safe)</em></li>
                <li>❌ لا تُقيِّم استقلالية أو نزاهة النظام القضائي أو الإداري</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفات قانونية</li>
                <li>❌ لا تستنتج إخلالًا بالتزامات دولية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>VII. Steps Taken / Remedies</em>
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