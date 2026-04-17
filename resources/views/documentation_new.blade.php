@extends('layouts.main')

@section('title', 'توثيق انتهاك أو حالة إنسانية بسرية تامة | منظمة العلويين')

@section('meta')

@php
$description = "منصة آمنة وسرية لتوثيق الانتهاكات والحالات الإنسانية في سوريا. يمكنك الإبلاغ باسمك الحقيقي أو مستعار مع حماية كاملة للبيانات.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="توثيق حالة إنسانية | منظمة العلويين">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "توثيق حالة إنسانية",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}",
  "publisher": {
    "@type": "Organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace"
  }
}
</script>

@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>توثيق انتهاك أو حالة إنسانية بشكل آمن وسري</h1>
  <p>شاركنا تفاصيل الحالة التي تعرضت لها ليتم توثيقها ضمن ملفات المنظمة ومناصرتها</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
  </div>
</header>

<div class="form-container" style="background:#eef2ff; border:2px solid #6366f1; text-align:center;">

  <h3 style="margin-bottom:10px;">⚠️ قبل أن تبدأ</h3>

  <p style="margin-bottom:15px;">
    إذا كنت تشعر بالخوف أو التردد، فهذا طبيعي.  
    يمكنك إرسال حالتك بدون ذكر اسمك الحقيقي، وجميع المعلومات تُعامل بسرية تامة.
  </p>

  <div style="margin-bottom:15px; color:#059669;">
    ✔ يمكنك استخدام اسم مستعار  
    ✔ يمكنك ترك بعض الحقول فارغة  
    ✔ يمكنك إرسال الحد الأدنى من المعلومات فقط  
  </div>

  <p style="font-size:14px; color:#444;">
    لن يتم مشاركة أي معلومات بدون موافقتك
  </p>

</div>

<main class="main-content container">
  <div class="form-container"
       style="
          margin-bottom:25px;
          border:2px dashed #f1c40f;
          background:#fffbe6;
          display:flex;
          flex-direction:column;
          align-items:center;
          text-align:center;
       ">

      <p style="font-weight:bold; margin-bottom:8px;">
          🔔 هل لديك حالة مسجّلة مسبقًا؟
      </p>

      <p style="margin-bottom:16px;">
          يمكنك إضافة معلومات أو أدلة جديدة باستخدام
          <strong>رقم الحالة</strong> و<strong>رمز المتابعة</strong>.
      </p>

      <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
         class="btn btn-outline">
          ➕ إضافة معلومات لحالة موجودة
      </a>

  </div>

  <div class="form-container" style="border:2px solid #2ecc71; background:#f6fffa;">
    <h3 style="margin-bottom:10px;">🔒 الخصوصية والأمان</h3>

    <p>
      هذا النموذج <strong>آمن وسري بالكامل</strong>.
      جميع البيانات التي يتم إدخالها تُحفظ ضمن نظام محمي ولا يتم مشاركتها مع أي جهة خارجية
      <strong>إلا بموافقتك الصريحة والمسبقة</strong>.
    </p>

    <p>
      يمكنك الإبلاغ:
      <strong>باسمك الحقيقي</strong> أو
      <strong>باستخدام اسم مستعار</strong> أو
      <strong>دون إدخال أي بيانات تعريفية حساسة</strong>.
    </p>

    <p>
      مشاركة المعلومات مع جهات دولية أو حقوقية تتم فقط إذا اخترت ذلك بنفسك،
      ودون ذكر اسمك الحقيقي في جميع الأحوال.
    </p>
  </div>

  <div class="form-container">
    <h2>📄 نموذج توثيق حالة</h2>

    <p class="form-note">
      <span style="color:red;">*</span>
      الحقول المعلّمة بنجمة حمراء إلزامية لضمان توثيق الحالة بشكل صحيح.
    </p>

    <form action="{{ route('case.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      @if ($errors->any())
        <div class="form-note" style="border-color:#e74c3c; background:#fdecea;">
          <strong>⚠️ لم يتم إرسال النموذج</strong>
          <p>يرجى تصحيح الأخطاء الموضّحة أدناه ثم إعادة المحاولة.</p>
        </div>
        
      @endif

      <div class="form-group">
        <label>نوع الاسم المستخدم:
          <span class="required">*</span>
        </label>
        <select name="name_type" required>
          <option value="real" {{ old('name_type') == 'real' ? 'selected' : '' }}>الاسم الحقيقي</option>
          <option value="alias" {{ old('name_type','alias') == 'alias' ? 'selected' : '' }}>اسم مستعار</option>
        </select>
        @error('full_name') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>الاسم الكامل أو المستعار:
          <span class="required">*</span>
        </label>
        <small class="hint">
          يمكنك كتابة اسمك الحقيقي، أو اسم مستعار، أو أي اسم تشعر بالأمان باستخدامه.
        </small>
        <input type="text" name="full_name" value="{{ old('full_name') }}">

        @error('full_name')
          <small style="color:#c0392b;">{{ $message }}</small>
        @enderror

      </div>

      <div class="form-group">
        <label>تاريخ الميلاد:</label>
        <input type="date" name="birth_date" value="{{ old('birth_date') }}">
      </div>

      <div class="form-group">
          <label>الطائفة / المكوّن:</label>
          <select name="component" class="form-control" required>
              <option value="">-- اختر المكوّن / الطائفة --</option>
              <option value="ALAWITE" {{ old('component') == 'ALAWITE' ? 'selected' : '' }}>علوي</option>
              <option value="SUNNI" {{ old('component') == 'SUNNI' ? 'selected' : '' }}>سني</option>
              <option value="SHIA" {{ old('component') == 'SHIA' ? 'selected' : '' }}>شيعي</option>
              <option value="ISMAILI" {{ old('component') == 'ISMAILI' ? 'selected' : '' }}>إسماعيلي</option>
              <option value="DRUZE" {{ old('component') == 'DRUZE' ? 'selected' : '' }}>درزي</option>
              <option value="Murshidi" {{ old('component') == 'Murshidi' ? 'selected' : '' }}>مرشدي</option>
              <option value="CHRISTIAN" {{ old('component') == 'CHRISTIAN' ? 'selected' : '' }}>مسيحي</option>
              <option value="KURD" {{ old('component') == 'KURD' ? 'selected' : '' }}>كردي</option>
              <option value="TURKMEN" {{ old('component') == 'TURKMEN' ? 'selected' : '' }}>تركماني</option>
              <option value="CIRCASSIAN" {{ old('component') == 'CIRCASSIAN' ? 'selected' : '' }}>شركسي</option>
              <option value="ARMENIAN" {{ old('component') == 'ARMENIAN' ? 'selected' : '' }}>أرمني</option>
              <option value="ASSYRIAN_CHALDEAN" {{ old('component') == 'ASSYRIAN_CHALDEAN' ? 'selected' : '' }}>آشوري / كلداني / سرياني</option>
              <option value="OTHER" {{ old('component') == 'OTHER' ? 'selected' : '' }}>أخرى / غير محدد</option>
          </select>
      </div>

      <div class="form-group">
        <label>مكان الإقامة (الدولة / المدينة):
          <span class="required">*</span>
        </label>
        <small class="hint">
          اكتب الدولة والمدينة الحالية، أو آخر مكان إقامة إذا كنت نازحًا.
        </small>
        <input type="text" name="location" value="{{ old('location') }}">
        @error('location') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>رقم الهاتف / واتساب:</label>
        <input type="tel" name="phone" placeholder="مثال: +963xxxxxxxxx" value="{{ old('phone') }}">
      </div>

      <div class="form-group">
        <label>البريد الإلكتروني:
          <span class="required">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email') }}">
      </div>

      <div class="form-group">
        <label>الزوج / الزوجة:</label>
        <input type="text" name="spouse_name" value="{{ old('spouse_name') }}">
      </div>

      <div class="form-group">
        <label>عدد الأولاد وأسماؤهم وأعمارهم:</label>

        @php
          $oldNames = old('children_names', ['']);
          $oldAges  = old('children_ages', ['']);
        @endphp

        <div id="children-container">
          @foreach($oldNames as $index => $name)
            <div class="child-entry">
              <input type="text"
                     name="children_names[]"
                     placeholder="اسم الطفل"
                     value="{{ $name }}">

              <input type="number"
                     name="children_ages[]"
                     placeholder="العمر"
                     value="{{ $oldAges[$index] ?? '' }}">
           </div>
          @endforeach
        </div>

        <button type="button"
                class="add-child-btn"
                onclick="addChildEntry()">
          ➕ إضافة طفل آخر
        </button>
      </div>

      <div class="form-group">
        <label>هل يوجد تهديد مباشر؟</label>
        <select name="direct_threat">
          <option value="1" {{ old('direct_threat') == '1' ? 'selected' : '' }}>نعم</option>
          <option value="0" {{ old('direct_threat') == '0' ? 'selected' : '' }}>لا</option>
        </select>
      </div>
       
      <div class="form-group">
        <label>نوع الانتهاك:
          <span class="required">*</span>
        </label>
        <select name="violation_type" required>
          <option value="">-- اختر --</option>

          <option value="arbitrary_detention" {{ old('violation_type') == 'arbitrary_detention' ? 'selected' : '' }}>
            اعتقال تعسفي
          </option>

          <option value="enforced_disappearance" {{ old('violation_type') == 'enforced_disappearance' ? 'selected' : '' }}>
            اختفاء قسري
          </option>

          <option value="torture" {{ old('violation_type') == 'torture' ? 'selected' : '' }}>
            تعذيب أو معاملة قاسية
          </option>

          <option value="threat" {{ old('violation_type') == 'threat' ? 'selected' : '' }}>
            تهديد أو ترهيب
          </option>

          <option value="discrimination" {{ old('violation_type') == 'discrimination' ? 'selected' : '' }}>
            تمييز ديني أو عرقي
          </option>

          <option value="sexual_violence" {{ old('violation_type') == 'sexual_violence' ? 'selected' : '' }}>
            عنف جنسي / قائم على النوع
          </option>

          <option value="property_violation" {{ old('violation_type') == 'property_violation' ? 'selected' : '' }}>
            مصادرة أو تدمير ممتلكات
          </option>

          <option value="forced_displacement" {{ old('violation_type') == 'forced_displacement' ? 'selected' : '' }}>
            تهجير قسري
          </option>

          <option value="other" {{ old('violation_type') == 'other' ? 'selected' : '' }}>
            أخرى
          </option>
        </select>
        @error('violation_type') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>وصف التهديد أو الانتهاك:
          <span class="required">*</span>
        </label>
        <small class="hint">
          صف ما حدث بالتفصيل: ماذا حصل؟ متى؟ من المتضرر؟ وكيف أثّر ذلك عليك أو على عائلتك.
          لا تقلق من الأسلوب، اكتب بطريقتك.
        </small>
        <textarea name="threat_description" rows="4">{{ old('threat_description') }}</textarea>
        @error('threat_description') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>الجهة المسؤولة عن التهديد (إن عُرفت):</label>
        <input type="text" name="threat_source" value="{{ old('threat_source') }}">
      </div>

      <div class="form-group">
        <label>تاريخ أو فترة التهديد:</label>
        <input type="date" name="threat_date" value="{{ old('threat_date') }}">
      </div>

      <div class="form-group">
        <label>أماكن حصلت بها الانتهاكات (إن وجدت):</label>
        <input type="text" name="threat_locations" value="{{ old('threat_locations') }}">
      </div>

      <div class="form-group">
        <label>هل يوجد أفراد يعانون من آثار نفسية أو صحية؟</label>
        <select name="psychological_impact">
          <option value="1">نعم</option>
          <option value="0">لا</option>
        </select>
      </div>

      <div class="form-group">
        <label>تفاصيل إضافية عن الحالة النفسية أو الاجتماعية:</label>
        <small class="hint">
          يمكنك هنا توضيح أي آثار نفسية أو اجتماعية ناتجة عن ما حدث،
          مثل: الخوف المستمر، القلق، الاكتئاب، فقدان العمل،
          العزلة الاجتماعية، صعوبات مع الأطفال أو العائلة.
          هذا الحقل اختياري، ويُستخدم فقط لفهم وضعك بشكل أفضل.
        </small>
        <textarea name="impact_details" rows="3">{{ old('impact_details') }}</textarea>
      </div>
      
      <div class="form-group">
        <label>هل تعتقد أن هذه الحالة جزء من نمط متكرر؟</label>
        <select name="is_pattern_case" required>
          <option value="0" {{ old('is_pattern_case') == '0' ? 'selected' : '' }}>لا</option>
          <option value="1" {{ old('is_pattern_case') == '1' ? 'selected' : '' }}>نعم</option>
        </select>
      </div>

      <div class="form-group">
        <label>درجة حساسية الحالة:
          <span class="required">*</span>
        </label>
        <select name="case_sensitivity" required>
          <option value="low" {{ old('case_sensitivity') == 'low' ? 'selected' : '' }}>منخفضة</option>
          <option value="medium" {{ old('case_sensitivity') == 'medium' ? 'selected' : '' }}>متوسطة</option>
          <option value="high" {{ old('case_sensitivity') == 'high' ? 'selected' : '' }}>عالية</option>
        </select>
        @error('case_sensitivity') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>المستندات والأدلة (صور، تقارير...):</label>
        <small class="hint">
          يمكنك رفع صور، تقارير، رسائل، أو أي ملف يدعم روايتك.
          رفع الملفات اختياري، ويمكنك إضافتها لاحقًا.
        </small>
        <input type="file" name="documents[]" multiple>
      </div>

      <div class="form-group" style="background:#f9f9f9; padding:10px;">
        <small>
          ⚠️ <strong>تنبيه مهم:</strong><br>
          الموافقات التالية اختيارية (باستثناء توثيق الحالة)،
          ويمكنك سحبها لاحقًا في أي وقت عبر فريق المنظمة.
        </small>
      </div>

      <div class="form-check">
        <label>
          <input type="checkbox" name="agreed_to_document" {{ old('agreed_to_document') ? 'checked' : '' }} required>
          أوافق على توثيق هذه الحالة ضمن ملفات المنظمة. <span class="required">*</span>
        </label>
        @error('agreed_to_document') <small style="color:#c0392b;">{{ $message }}</small> @enderror
      </div>
      <div class="form-check">
        <label><input type="checkbox" name="agreed_to_share"> أوافق على مشاركة قضيتي مع منظمات حقوقية خارجية (دون ذكر اسمي الحقيقي).</label>
      </div>
      <div class="form-check">
        <label><input type="checkbox" name="agreed_to_campaign"> أوافق على استخدام قصتي في تقارير أو حملات مناصرة (بشكل غير معلن).</label>
      </div>
      
      <div class="form-note">
        ⚠️ بعد إرسال الحالة سيتم إعطاؤك <strong>رقم حالة</strong> و<strong>رمز متابعة</strong>.<br>
        يرجى الاحتفاظ بهما لإضافة أي معلومات لاحقًا.
      </div>

      <div style="text-align: center;">
        <button type="submit" class="btn btn-outline">📨 إرسال الحالة بشكل آمن</button>
      </div>
    </form>
  </div>
</main>

<script>
  function addChildEntry() {
    const container = document.getElementById('children-container');

    const wrapper = document.createElement('div');
    wrapper.className = 'child-entry';

    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.name = 'children_names[]';
    nameInput.placeholder = 'Child name';

    const ageInput = document.createElement('input');
    ageInput.type = 'number';
    ageInput.name = 'children_ages[]';
    ageInput.placeholder = 'Age';

    wrapper.appendChild(nameInput);
    wrapper.appendChild(ageInput);

    container.appendChild(wrapper);
  }
</script>

<style>
  .required {
    color: red;
    font-weight: bold;
    margin-right: 4px;
  }

  .hint {
    display: block;
    font-size: 0.85em;
    color: #666;
    margin-bottom: 6px;
  }

  .form-note {
    background: #fff8e1;
    border: 1px solid #f1c40f;
    padding: 10px;
    margin-bottom: 20px;
    font-size: 0.9em;
  }
</style>

@endsection
