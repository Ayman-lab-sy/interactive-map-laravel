@extends('admin.layouts.app')

@section('title', 'Referral – UN Special Procedures (Freedom of Religion or Belief)')

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
        المقرر الخاص المعني بحرية الدين أو المعتقد
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



<div style="margin-top:32px;"></div>

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
        <strong>Special Rapporteur on freedom of religion or belief</strong>.
        يجب تقديم عرض واقعي ومختصر للوقائع المزعومة كما وردت في الشهادة،
        دون توصيف قانوني أو إسناد مسؤولية أو اتهام مباشر.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_summary', $referral->id) }}">
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
                <li>✔ سرد وصفي للوقائع كما وردت في الشهادة</li>
                <li>✔ توضيح تأثير الوقائع على ممارسة الدين أو المعتقد</li>
                <li>✔ استخدام صياغة حيادية مثل <em>(allegedly / reportedly)</em></li>
                <li>✔ ذكر الزمان والمكان بصيغة وصفية إن توفرت</li>
                <li>❌ عدم استخدام توصيفات قانونية أو تقييمات</li>
                <li>❌ عدم تضمين استنتاجات أو مطالبات</li>
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

<div style="margin-top:32px;"></div>

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
        يهدف هذا القسم إلى تقديم معلومات تعريفية أساسية عن
        الأفراد أو الجماعات الذين يُزعم أنهم تأثروا بالوقائع
        المبلغ عنها والمتعلقة بممارسة حرية الدين أو المعتقد،
        وذلك كما ورد في الشهادة، مع مراعاة اعتبارات السلامة
        والحماية ودون توصيف قانوني.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_victim', $referral->id) }}">
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
                <li>✔ ذكر الأفراد أو الجماعات كما وردوا في الشهادة فقط</li>
                <li>✔ يمكن ذكر العمر التقريبي، الجنس أو الصفة العامة إذا كانت ذات صلة</li>
                <li>✔ في حال الجماعات، استخدام توصيف عام <em>(members of a religious community)</em></li>
                <li>✔ استخدام صياغة حيادية <em>(were reportedly affected)</em></li>
                <li>❌ عدم ذكر عناوين دقيقة أو معلومات تعريفية حساسة</li>
                <li>❌ عدم استخدام توصيفات قانونية أو دينية تقييمية</li>
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
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>

    </form>
</div>
@endif

<div style="margin-top:32px;"></div>

{{-- ========================= --}}
{{-- Block C – Religious Identity / Belief --}}
{{-- ========================= --}}
@if($steps->victim_saved)
<div id="block-c" class="card" data-block="C">

    <div class="section-title">
        القسم الثالث: الهوية الدينية أو المعتقد
        <span class="subtitle">
            Religious Identity / Belief (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى توضيح المعتقد أو الانتماء الديني
        ذي الصلة بالوقائع المبلّغ عنها، سواء كان دينًا،
        مذهبًا، معتقدًا، أو عدم اعتناق دين، وذلك كما ورد
        في الشهادة. تُقدَّم هذه المعلومات لأغراض وصفية
        فقط، دون تقييم ديني أو توصيف قانوني.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_identity', $referral->id) }}">
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
                <li>✔ ذكر المعتقد أو الانتماء الديني كما ورد في الشهادة فقط</li>
                <li>✔ يمكن ذكر التعريف الذاتي للفرد أو الجماعة إذا ورد في المصدر</li>
                <li>✔ استخدام صياغة وصفية غير جازمة <em>(is reported to belong to)</em></li>
                <li>✔ يمكن الإشارة إلى الطائفة أو المذهب إذا كان ذلك ذا صلة بالوقائع</li>
                <li>❌ عدم تقييم صحة المعتقد أو مشروعيته</li>
                <li>❌ عدم استخدام توصيفات قانونية مثل
                    <em>(religious discrimination, violation of freedom of belief)</em>
                </li>
                <li>❌ عدم استنتاج وجود استهداف ديني ممنهج</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
             سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>III. Religious Identity / Belief</em>
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


@if($steps->identity_saved)
{{-- ========================= --}}
{{-- Block D – Nature of the Alleged Acts or Measures --}}
{{-- ========================= --}}
<div id="block-d" class="card" data-block="D">

    <div class="section-title">
        القسم الرابع: طبيعة الأفعال أو التدابير المزعومة
        <span class="subtitle">
            Nature of the Alleged Acts or Measures (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى وصف الأفعال، القيود أو التدابير
        التي يُزعم أنها أثّرت على ممارسة حرية الدين أو
        المعتقد، سواء بشكل مباشر أو غير مباشر، كما وردت
        في الشهادة. تُقدَّم هذه المعلومات بصيغة وصفية
        فقط، دون توصيف قانوني أو تقييم لمدى مشروعيتها.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_violations', $referral->id) }}">
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
                <li>✔ وصف الأفعال أو القيود كما وردت في الشهادة
                    (منع عبادة، تقييد شعائر، إغلاق أماكن عبادة، ضغط لتغيير المعتقد…)
                </li>
                <li>✔ توضيح كيف أثّرت هذه التدابير على ممارسة الدين أو المعتقد عمليًا</li>
                <li>✔ استخدام صياغة غير جازمة
                    <em>(were reportedly restricted / were said to face limitations)</em>
                </li>
                <li>✔ الالتزام بالوقائع المبلّغ عنها دون تحليل أو استنتاج</li>
                <li>✔ يمكن ذكر الجهة أو الفاعل بصيغة وصفية غير اتهامية إذا ورد في الشهادة</li>
                <li>❌ عدم استخدام مصطلحات قانونية
                    <em>(violation of freedom of religion, unlawful restriction)</em>
                </li>
                <li>❌ عدم تقييم مدى توافق القيود مع القانون الدولي</li>
                <li>❌ عدم استنتاج وجود سياسة دينية أو تمييز ممنهج</li>
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
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>

    </form>
</div>
@endif


@if($steps->violation_saved)
{{-- ========================= --}}
{{-- Block E – Alleged Perpetrators --}}
{{-- ========================= --}}
<div id="block-e" class="card" data-block="E">

    <div class="section-title">
        القسم الخامس: الجهة أو الأطراف المزعومة
        <span class="subtitle">
            Alleged Perpetrators (EN)
        </span>
    </div>

    <p class="page-subtitle">
        يهدف هذا القسم إلى عرض المعلومات المتوفرة بشأن
        الجهة أو الأطراف التي يُزعم أنها كانت ضالعة في
        الأفعال أو التدابير المبلّغ عنها، كما وردت في
        الشهادة. تُقدَّم هذه المعلومات بصيغة وصفية فقط،
        دون اتهام مباشر أو إسناد مسؤولية قانونية.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_perpetrators', $referral->id) }}">
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
                <li>✔ ذكر الجهة أو الأطراف كما وردت في الشهادة فقط
                    (سلطات محلية، أفراد، جهات رسمية، جماعات غير حكومية…)
                </li>
                <li>✔ استخدام صياغة غير جازمة
                    <em>(allegedly / reportedly / according to the source)</em>
                </li>
                <li>✔ يمكن ذكر الدور المزعوم للجهة بصيغة وصفية
                    (فرض قيود، تنفيذ أوامر، اتخاذ تدابير)
                </li>
                <li>✔ الالتزام بالمعلومات المتوفرة دون إضافة أو استنتاج</li>
                <li>❌ عدم إسناد نية أو سياسة دينية أو تمييزية</li>
                <li>❌ عدم استخدام مصطلحات قانونية
                    <em>(responsible, violation, unlawful)</em>
                </li>
                <li>❌ عدم تحميل الجهة أي مسؤولية قانونية أو دولية</li>
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
            <button class="btn btn-primary">
                حفظ والمتابعة
            </button>
        </div>

    </form>
</div>
@endif


@if($steps->perpetrators_saved)
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

    <p class="page-subtitle">
        يُستخدم هذا القسم حصريًا لتقديم معلومات سياقية عامة،
        إن وُجدت، قد تساعد على فهم الوقائع المبلّغ عنها
        ضمن إطار أوسع. لا يهدف هذا القسم إلى إثبات
        وجود سياسة ممنهجة أو نمط ثابت من الانتهاكات،
        ولا يتضمن أي تقييم قانوني.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_context', $referral->id) }}">
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
                <li>✔ استخدام هذا الحقل فقط إذا توفرت معلومات عامة وموثوقة وردت في الشهادة</li>
                <li>✔ الإشارة إلى وقائع مشابهة بصيغة وصفية وغير جازمة</li>
                <li>✔ استخدام تعابير مثل
                    <em>(according to available information / as reported by the source)</em>
                </li>
                <li>✔ الالتزام بالمستوى الوصفي دون تعميم</li>
                <li>❌ عدم الإشارة إلى وجود سياسة دينية أو تمييز ممنهج</li>
                <li>❌ عدم استخدام مصطلحات مثل
                    <em>(systematic, widespread, policy, pattern of violations)</em>
                </li>
                <li>❌ عدم تضمين توصيف قانوني أو استنتاج تحليلي</li>
            </ul>
        </div>

        <div class="info-box info-pin">
            <span class="info-icon">●</span>
             سيظهر هذا النص في التقرير النهائي تحت قسم:
            <em>VI. Context / Pattern</em>
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

    {{-- Skip Context --}}
    <div class="form-actions">
        <form method="POST"
              action="{{ route('admin.referrals.un_sp.freedom_religion.skip_context', $referral->id) }}">
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
        يهدف هذا القسم إلى توضيح ما إذا كانت قد تمّت
        محاولة اللجوء إلى سبل الانتصاف أو الآليات المتاحة
        على المستوى المحلي فيما يتعلّق بالوقائع المبلّغ عنها،
        أو ما إذا كان اللجوء إليها غير ممكن أو ينطوي على
        مخاطر، وذلك كما ورد في الشهادة.
        يُقدَّم هذا القسم لأغراض معلوماتية فقط، دون تقييم
        قانوني أو استنتاجات.
    </p>

    <form method="POST"
          action="{{ route('admin.referrals.un_sp.freedom_religion.save_remedies', $referral->id) }}">
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
                <li>✔ ذكر ما إذا تم تقديم شكاوى، بلاغات، أو مراجعات رسمية كما ورد في الشهادة</li>
                <li>✔ في حال عدم اللجوء إلى سبل الانتصاف، ذكر السبب بصيغة وصفية حيادية</li>
                <li>✔ يمكن الإشارة إلى مخاطر محتملة، الخوف من الانتقام، أو عدم توفر سبل فعالة</li>
                <li>✔ استخدام صياغة تفسيرية
                    <em>(were reportedly pursued / were not considered available)</em>
                </li>
                <li>❌ عدم تقييم استقلالية أو نزاهة النظام القضائي أو الإداري</li>
                <li>❌ عدم استخدام لغة اتهامية أو توصيفات قانونية</li>
                <li>❌ عدم استنتاج إخلال بالتزامات دولية</li>
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