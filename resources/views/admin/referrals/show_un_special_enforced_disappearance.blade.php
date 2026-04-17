@extends('admin.layouts.app')

@section('title', 'إحالة – UN Special Procedures (الاختفاء القسري)')

@section('content')

@php
    $readyForGeneration =
        isset($steps)
        && $steps->summary_saved
        && $steps->victim_saved
        && $steps->circumstances_saved
        && $steps->perpetrators_saved
        && $steps->remedies_saved;
@endphp

<div class="page-title">
    إحالة – الإجراءات الخاصة للأمم المتحدة
    <div class="page-subtitle">
        المقرر الخاص المعني بالاختفاء القسري
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
        هذا الملخص هو <u>المدخل الرئيسي</u> الذي تطّلع عليه
       <strong>Working Group on Enforced or Involuntary Disappearances</strong>.
       يجب أن يصف وقائع الحرمان من الحرية وما تلاه من انقطاع في الاتصال واستمرار غموض مصير أو مكان الشخص كما وردت بالشهادة دون توصيف قانوني او اتهام مباشر
    </p>

    <form method="POST"
            action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_summary', $referral->id) }}">
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
                <li>ركّز على: الحرمان من الحرية، انقطاع الاتصال، إخفاء المصير أو المكان</li>
                <li>❌ لا تستخدم توصيفات قانونية أو استنتاجات <em>(enforced disappearance, crime, violation)</em></li>
                <li>❌ لا اتهام مباشر أو تحليل قانوني</li>
                <li>✔ اذكر الزمان والمكان بصيغة عامة <em>(In mid-January 2026, in the vicinity of…)</em></li>
                <li>✔ وضّح ما إذا كان مصير الشخص لا يزال مجهولًا حتى تاريخ التقديم</li>
                <li>✔ اذكر أي محاولات قامت بها العائلة أو أطراف أخرى لمعرفة المصير</li>
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
        القسم الثاني: معلومات عن الضحية
        <span class="subtitle">
            Victim Information (EN)
        </span>
    </div>

    <p class="page-subtitle">
            <strong>هدف هذا القسم:</strong><br>
            تقديم معلومات تعريفية أساسية عن الشخص المختفي، كما وردت في الشهادة،
             لدعم ولاية مجموعة العمل المعنية بحالات الإخفاء القسري.
            من المهم عدم تضمين أي معلومات قد تؤدي إلى الكشف عن مكان الشخص المختفي،
           أو قد تعرّضه أو أفراد عائلته لخطر مباشر.
    </p>
    <form method="POST"
          action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_victim', $referral->id) }}">
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
                <li>✔ يمكن ذكر اسم الشخص المختفي إذا كان معروفًا</li>
                <li>✔ يمكن ذكر العمر أو تاريخ الميلاد إن توفر</li>
                <li>✔ يمكن ذكر الجنس والجنسية</li>
                <li>✔ يمكن ذكر آخر مكان معروف شوهد فيه قبل الاختفاء</li>
                <li>✔ استخدم صياغة وصفية محايدة <em>(is reported to have been deprived of liberty…)</em></li>
                <li>❌ لا تستخدم توصيفات قانونية أو استنتاجات <em>(enforced disappearance / crime)</em></li>
                <li>❌ لا تذكر عناوين دقيقة أو تفاصيل قد تعرّض العائلة للخطر</li>
            </ul>            
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>II. Information on the Victim </em>
        </div>

        <div class="info-box info-danger" style="color:#b91c1c;">
            <span class="info-icon">×</span>
             يجب عدم إدخال أي معلومات قد تعرّض الشخص المختفي أو ذويه لخطر مباشر.
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
        القسم الثالث: ظروف الحرمان من الحرية والإخفاء
        <span class="subtitle">
            Circumstances of Disappearance (EN)
        </span>
    </div>

    <p class="page-subtitle">
    <strong>هدف هذا القسم:</strong><br>
    وصف كيفية حرمان الشخص من حريته وما تلاه من انقطاع في الاتصال واستمرار غموض مصيره أو مكانه كما ورد في الشهادة
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_circumstances', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="circumstances_en"
                rows="6" 
                required 
                data-en-only
                class="form-control"
            >{{ old('circumstances_en', $data->circumstances_en ?? '') }}</textarea>
            @error('circumstances_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>كيف تم حرمان الشخص من حريته (توقيف، احتجاز، اقتياد بالقوة)؟</li>
                <li>من هم الأشخاص أو الجهات المزعومة التي نفذت ذلك (بصيغة عامة)؟</li>
                <li>هل تم إبلاغ العائلة بمكان الاحتجاز أم تم إنكاره؟</li>
                <li>هل انقطع الاتصال بالشخص بشكل كامل، ومتى؟</li>
                <li>هل ما زال مصير أو مكان الشخص مجهولًا حتى تاريخ التقديم؟</li>
                <li>❌ لا توصيفات قانونية أو اتهامات أو تحليل قانوني</li>
                <li>❌ لا ذكر أسماء جهات، وحدات، أو مسؤولين محددين</li>
            </ul>              
        </div>
        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>III. Circumstances of the Disappearance</em>
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

@if($steps->circumstances_saved)
{{-- Block D --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: الجهات أو الأطراف المزعومة
        <span class="subtitle">
            Alleged Perpetrators (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>تنبيه تحريري:</strong><br>
        هذا القسم لا يهدف إلى الاتهام أو تحميل المسؤولية بل إلى وصف الجهات المزعومة كما وردت في الشهادة دون اي استنتاج او توصيف قانوني
    </P>
    <form method="POST"
          action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_perpetrators', $referral->id) }}">
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
                <li>✔ استخدم توصيفات عامة <em>(State actors, security forces, unknown individuals, individuals acting in an official capacity)</em></li>
                <li>✔ اربط الدور المزعوم بالحرمان من الحرية فقط، دون توصيف لاحق</li>
                <li>✔ استخدم صياغة حيادية مثل <em>(allegedly / reportedly / according to the source)</em></li>
                <li>✔ استخدم عبارات مثل <em>(The source alleges that… / The individuals are described as… / The deprivation of liberty is reported to have been carried out by…)</em></li>
                <li>❌ لا تذكر أسماء، رتب، وحدات، مؤسسات، أو مسؤولين محددين</li>
                <li>❌ لا تُسند نية أو سياسة أو أوامر</li>
            </ul>               
        </div>
        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>IV. Alleged Responsible Actors</em>
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
{{-- Block E --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: السياق أو النمط (اختياري)
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
        يستخدم هذا القسم فقد إذا كانت الواقعة جزءا من نمط أوسع او ممارسة متكررة لحالات حرمان من الحرية يتبعها انقطاع في المصير أو المكان وليس اذا كانت حادثة فردية معزولة
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_context', $referral->id) }}">
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
                <li>✔ استخدم صياغة احتمالية <em>(may indicate / suggests / appears to be / has been reported)</em></li>
                <li>✔ ركّز على تكرار حالات مشابهة من الحرمان من الحرية يعقبها انقطاع المصير أو المكان</li>
                <li>✔ يمكن الإشارة إلى نفس المنطقة، نفس الفترة الزمنية، أو نفس الأسلوب العام</li>
                <li>❌ تجنب استخدام لغة إثبات أو جزم <em>(proves / confirms / demonstrates)</em></li>
                <li>❌ لا تستنتج وجود سياسة أو خطة ممنهجة</li>
            </ul>               
        </div>
        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>V. Context / Pattern</em>
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
              action="{{ route('admin.referrals.un_sp.enforced_disappearance.skip_context', $referral->id) }}">
            @csrf
            <button class="btn btn-secondary">
                تخطي هذا القسم
            </button>
        </form>
    </div>

</div>
@endif

@if($steps->context_done)
{{-- Block F --}}
<div id="block-f" class="card" data-block="F">

    <div class="section-title">
        القسم السادس: استنفاذ سبل الانتصاف
        <span class="subtitle">
            Exhaustion of Remedies (EN)
        </span>
    </div>

    <p class="page-subtitle">
        <strong>أهمية قانونية:</strong><br>
         تعتمد مجموعة العمل المعنية بحالات الإخفاء القسري
        على هذا القسم لتقييم ما إذا كانت سبل الانتصاف المحلية قد استنفدت او ما إذا كان اللجوء إاليها غير ممكن أو ينطوي على مخاطر
    </p>
    <form method="POST"
          action="{{ route('admin.referrals.un_sp.enforced_disappearance.save_remedies', $referral->id) }}">
        @csrf
        <div class="form-group">
            <textarea 
                name="steps_taken_en" 
                rows="5" 
                required 
                data-en-only
                class="form-control"
            >{{ old('steps_taken_en', $data->steps_taken_en ?? '') }}</textarea>
            @error('steps_taken_en')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="info-box info-info">
            <span class="info-icon">!</span>
             <strong>إرشادات تحريرية:</strong>
            <ul>
                <li>✔ اشرح ما إذا تم اللجوء إلى سبل انتصاف محلية (شرطة، قضاء، شكاوى رسمية)</li>
                <li>✔ إذا لم يتم ذلك، اشرح السبب (الخوف من الانتقام، عدم فعالية السبل، غياب الوصول)</li>
                <li>✔ استخدم لغة تفسيرية محايدة مثل:
                    <em>(was not considered effective / was deemed unavailable / would have posed a risk)</em>
                </li>
                <li>❌ لا تستخدم لغة سياسية أو اتهامية</li>
                <li>❌ لا تستخدم عبارات مثل:
                    <em>(the judiciary is corrupt / the state refuses justice)</em>
                </li>
            </ul>                
        </div>
        <div class="info-box info-pin">
            <span class="info-icon">●</span>
            سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>VI. Steps Taken at the Domestic Level</em>
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
