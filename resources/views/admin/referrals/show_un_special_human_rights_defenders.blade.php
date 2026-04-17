@extends('admin.layouts.app')

@section('title', 'Referral – UN Special Procedures (Human Rights Defenders)')

@section('content')

@php
$readyForGeneration =
    isset($steps)
    && $steps->summary_saved
    && $steps->victim_saved
    && $steps->role_saved
    && $steps->activities_saved
    && $steps->targeting_saved
    && $steps->violations_saved
    && $steps->remedies_saved
    && $steps->context_done;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بالدفاع عن حقوق الإنسان
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
        <strong>Special Rapporteur on the situation of human rights defenders</strong>.
        يجب تقديم عرض واقعي ومختصر للوقائع المزعومة المتعلقة بالأفعال أو التدابير
        التي يُزعم أنها استهدفت شخصًا بسبب دوره أو أنشطته كمدافع عن حقوق الإنسان،
        كما وردت في الشهادة، دون توصيف قانوني أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_summary', $referral->id) }}">
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
                <li>✔ استخدم دائمًا صياغة حيادية مثل <em>(allegedly / reportedly / according to the source)</em></li>
                <li>✔ قدّم ملخصًا للوقائع كما وردت في الشهادة دون تحليل أو توصيف قانوني</li>
                <li>✔ اذكر بإيجاز طبيعة الوقائع أو التدابير المبلغ عنها (تهديد، اعتقال، مراقبة، تشويه سمعة…)</li>
                <li>✔ أوضح أن الوقائع تتعلق بشخص يُعرَّف أو يُوصَف كمدافع عن حقوق الإنسان</li>
                <li>✔ اذكر السياق الزمني والمكاني بصيغة وصفية إن كان متوفرًا</li>
                <li>✔ يمكن الإشارة إلى الجهة أو الفاعل بصيغة غير اتهامية إن ورد في الشهادة</li>
                <li>❌ لا تستخدم توصيفات قانونية مثل <em>(violation, unlawful, persecution)</em></li>
                <li>❌ لا تُسند نية أو سياسة ممنهجة أو مسؤولية قانونية لأي جهة</li>
                <li>❌ لا تتضمن استنتاجات، تقييمات قانونية، أو مطالبات</li>
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

@if($steps->summary_saved)
{{-- ========================= --}}
{{-- Block B – Victim --}}
{{-- ========================= --}}
<div id="block-b" class="card" data-block="B">

    <div class="section-title">
        القسم الثاني: معلومات عن المدافع/ة عن حقوق الإنسان
        <span class="subtitle">
            Information on the Human Rights Defender (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى تقديم معلومات تعريفية أساسية عن الشخص
        الذي يُزعم أنه تعرّض لأفعال أو تدابير بسبب دوره كمدافع عن حقوق الإنسان،
        كما وردت في الشهادة، دعمًا لولاية
        <strong>Special Rapporteur on the situation of human rights defenders</strong>.
        يجب مراعاة اعتبارات السلامة والحماية عند إدخال المعلومات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_victim', $referral->id) }}">
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
                <li>✔ يمكن ذكر الاسم فقط إذا كان معروفًا وآمنًا، أو استخدام توصيف عام عند الضرورة</li>
                <li>✔ يمكن ذكر العمر التقريبي، الجنس، الجنسية، أو الصفة العامة إن وُجدت</li>
                <li>✔ اذكر الدور أو الصفة الحقوقية بشكل وصفي <em>(human rights activist, lawyer, monitor, journalist, civil society actor)</em></li>
                <li>✔ استخدم صياغة حيادية <em>(is reported to be / is described as)</em></li>
                <li>✔ يمكن ذكر الانتماء المهني أو المؤسسي إذا كان ذا صلة بالدور الحقوقي</li>
                <li>❌ لا تذكر عناوين دقيقة، أرقام هواتف، أو معلومات تعريفية حساسة</li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(victim of violation, persecuted defender)</em></li>
                <li>❌ لا تُقيِّم المخاطر أو مستوى الحماية قانونيًا</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>II. Information on the Human Rights Defender</em>
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
{{-- Block C – Role --}}
{{-- ========================= --}}
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: دور الشخص في الدفاع عن حقوق الإنسان
        <span class="subtitle">
            Role as a Human Rights Defender (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح طبيعة الدور الذي يقوم به الشخص
        في مجال الدفاع عن حقوق الإنسان، كما ورد في الشهادة،
        بما في ذلك الصفة أو الوظيفة أو النشاط العام الذي يُعرّفه
        كمدافع عن حقوق الإنسان، دعمًا لولاية
        <strong>Special Rapporteur on the situation of human rights defenders</strong>.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_role', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="defender_role_en" 
                rows="5" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('defender_role_en', $data->defender_role_en ?? '') }}</textarea>
            @error('defender_role_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف الدور الحقوقي بصيغة وصفية <em>(human rights lawyer, civil society activist, documentation monitor)</em></li>
                <li>✔ اذكر مجال العمل الحقوقي إن كان معروفًا <em>(detention monitoring, documentation of violations, legal aid)</em></li>
                <li>✔ يمكن ذكر الإطار العام للنشاط (محلي، مجتمعي، مهني) دون تفاصيل تنظيمية حساسة</li>
                <li>✔ استخدم صياغة حيادية <em>(is reported to work as / is described as engaging in)</em></li>
                <li>✔ ركّز على طبيعة الدور وليس نتائجه أو مخاطره</li>
                <li>❌ لا تذكر تفاصيل تشغيلية، شبكات، أو أسماء جهات قد تعرّض الشخص للخطر</li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(protected defender, legitimate human rights defender)</em></li>
                <li>❌ لا تربط هنا بين الدور والانتهاكات (هذا في الأقسام اللاحقة)</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>III. Role as a Human Rights Defender</em>
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

@if($steps->role_saved)
{{-- ========================= --}}
{{-- Block D – Activities --}}
{{-- ========================= --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: وصف الأنشطة الحقوقية
        <span class="subtitle">
            Description of Activities (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف طبيعة الأنشطة الحقوقية التي يقوم بها الشخص
        في إطار دوره كمدافع عن حقوق الإنسان، كما وردت في الشهادة،
        بما في ذلك الأعمال أو المبادرات أو الممارسات السلمية ذات الصلة
        بتعزيز أو حماية حقوق الإنسان.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_activities', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="activities_description_en" 
                rows="6" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('activities_description_en', $data->activities_description_en ?? '') }}</textarea>
            @error('activities_description_en')
                <div class="form-error">{{ $message }}</div>
            @enderror

        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف الأنشطة بصيغة واقعية <em>(monitoring, documentation, legal assistance, advocacy, reporting)</em></li>
                <li>✔ يمكن ذكر نوع النشاط أو مجاله دون تفاصيل تشغيلية أو تنظيمية حساسة</li>
                <li>✔ اذكر ما إذا كانت الأنشطة علنية أو مجتمعية أو مهنية بصيغة عامة</li>
                <li>✔ استخدم صياغة حيادية <em>(is reported to engage in / carries out activities related to)</em></li>
                <li>✔ التزم بما ورد في الشهادة دون توسّع أو استنتاج</li>
                <li>❌ لا تذكر هنا أي تدابير استهداف أو ردود فعل (هذا في الأقسام اللاحقة)</li>
                <li>❌ لا تستخدم توصيفات قانونية أو سياسية</li>
                <li>❌ لا تتضمن تقييمًا لأثر الأنشطة أو مشروعيتها</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
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

@if($steps->activities_saved)
{{-- ========================= --}}
{{-- Block E – Targeting --}}
{{-- ========================= --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: الرابط بين الأنشطة الحقوقية والاستهداف المزعوم
        <span class="subtitle">
            Link Between Activities and Targeting (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح الكيفية التي يُزعم بها أن الأفعال
        أو التدابير المبلغ عنها قد تكون مرتبطة بدور الشخص
        وأنشطته كمدافع عن حقوق الإنسان، كما وردت في الشهادة،
        دون الجزم بوجود علاقة سببية أو نية محددة.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_targeting', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="targeting_link_en" 
                rows="5" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('targeting_link_en', $data->targeting_link_en ?? '') }}</textarea>

            @error('targeting_link_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اشرح الرابط المحتمل بصيغة تفسيرية حيادية <em>(appears to be linked to / reportedly following / may be connected to)</em></li>
                <li>✔ يمكن الإشارة إلى التوقيت أو التسلسل الزمني بين الأنشطة والاستهداف</li>
                <li>✔ اذكر أي مؤشرات وردت في الشهادة تُفهم على أنها سبب محتمل للاستهداف</li>
                <li>✔ استخدم لغة احتمالية وغير جازمة</li>
                <li>✔ التزم بما ورد في الشهادة دون إضافة تحليل أو استنتاج</li>
                <li>❌ لا تُثبت علاقة سببية أو نية متعمدة</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفات قانونية <em>(retaliation, reprisal, punishment)</em></li>
                <li>❌ لا تُسند المسؤولية لجهة بعينها</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>V. Link Between Activities and Targeting</em>
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

@if($steps->targeting_saved)
{{-- ========================= --}}
{{-- Block F – Violations --}}
{{-- ========================= --}}
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: الأفعال أو التدابير المزعومة
        <span class="subtitle">
            Alleged Violations or Reprisals (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف الأفعال أو التدابير التي يُزعم أنها
        اتُّخذت بحق الشخص المعني، كما وردت في الشهادة، بما في ذلك
        أي تهديدات، مضايقات، احتجاز، مراقبة، أو أشكال أخرى من الضغط
        أو التضييق المرتبطة بالاستهداف المبلغ عنه.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_violations', $referral->id) }}">
        @csrf

        <div class="form-group">
            <textarea 
                name="violations_details_en" 
                rows="6" 
                required 
                data-en-only 
                class="form-control"
            >{{ old('violations_details_en', $data->violations_details_en ?? '') }}</textarea>

            @error('violations_details_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ صف ما حدث بصيغة واقعية <em>(was reportedly arrested, was summoned, received threats, was subjected to surveillance)</em></li>
                <li>✔ اذكر تسلسل الأحداث أو توقيتها إن كان متوفرًا</li>
                <li>✔ يمكن ذكر الجهة أو الفاعل بصيغة عامة وغير اتهامية <em>(authorities, security personnel, unidentified individuals)</em></li>
                <li>✔ استخدم صياغة حيادية <em>(was reportedly subjected to / was said to have experienced)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية <em>(violation of international law, unlawful detention, reprisal)</em></li>
                <li>❌ لا تُقيِّم مشروعية الأفعال أو قانونيتها</li>
                <li>❌ لا تُسند نية أو سياسة ممنهجة</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>VI. Alleged Violations or Reprisals</em>
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

@if($steps->violations_saved)
{{-- ========================= --}}
{{-- Block G – Context --}}
{{-- ========================= --}}
<div id="block-g" class="card" data-block="G">

    <div class="section-title">
        القسم السابع: السياق أو النمط (اختياري)
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
        إلى وجود سياق أوسع أو نمط متكرر من الأفعال أو التدابير
        التي يُزعم أنها استهدفت مدافعين عن حقوق الإنسان.
        تُقدَّم هذه المعلومات لأغراض سياقية فقط، دون الجزم
        بوجود سياسة أو ممارسة ممنهجة.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_context', $referral->id) }}">
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
                <li>✔ استخدم صياغة احتمالية <em>(may indicate / appears to suggest / could reflect)</em></li>
                <li>✔ يمكن الإشارة إلى تكرار وقائع مشابهة أو سياق عام إن ورد في الشهادة</li>
                <li>✔ قدّم المعلومات بوصفها ملاحظات سياقية عامة فقط</li>
                <li>✔ التزم بما ورد في الشهادة دون توسّع أو استنتاج</li>
                <li>❌ لا تستخدم لغة جازمة <em>(demonstrates / proves / confirms)</em></li>
                <li>❌ لا تستنتج وجود سياسة رسمية أو ممارسة ممنهجة</li>
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
              action="{{ route('admin.referrals.un_sp.human_rights_defenders.skip_context', $referral->id) }}">
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
{{-- Block H – Remedies --}}
{{-- ========================= --}}
<div id="block-h" class="card" data-block="H">

    <div class="section-title">
        القسم الثامن: اسنتفاذ سبل الانتصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح ما إذا تم اللجوء إلى سبل الانتصاف
        أو الآليات المتاحة على المستوى المحلي ردًا على الأفعال
        أو التدابير المبلغ عنها، أو ما إذا كان اللجوء إليها غير ممكن
        أو ينطوي على مخاطر، كما ورد في الشهادة.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.human_rights_defenders.save_remedies', $referral->id) }}">
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
                <li>✔ إن لم يتم اللجوء إلى سبل الانتصاف، اذكر السبب بصيغة تفسيرية حيادية</li>
                <li>✔ يمكن الإشارة إلى مخاطر محتملة، الخوف من الانتقام، أو عدم فعالية السبل المتاحة</li>
                <li>✔ استخدم صياغة وصفية <em>(were reportedly pursued / were deemed unavailable / were not considered safe)</em></li>
                <li>❌ لا تُقيِّم استقلالية أو فعالية النظام القضائي أو الإداري</li>
                <li>❌ لا تستخدم لغة اتهامية أو توصيفات قانونية</li>
                <li>❌ لا تستنتج إخلالًا بالتزامات قانونية</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>IX. Steps Taken / Remedies</em>
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
