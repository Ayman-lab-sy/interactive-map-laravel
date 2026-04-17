@extends('layouts.main')

@section('title', 'سياسة الخصوصية | منظمة العلويين والأقليات السورية للعدالة والسلام')

@section('meta')

@php
$description = "توضح هذه الصفحة كيفية جمع واستخدام وحماية البيانات الشخصية في موقع منظمة العلويين والأقليات السورية للعدالة والسلام، وفق معايير الخصوصية وحماية البيانات الأوروبية.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="سياسة الخصوصية | منظمة العلويين والأقليات السورية">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="سياسة الخصوصية | منظمة العلويين والأقليات السورية">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "سياسة الخصوصية",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}"
}
</script>

@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>سياسة الخصوصية</h1>
  <p>خصوصيتكم تهمنا – نوضح هنا كيف نتعامل مع بياناتكم.</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      العودة للصفحة الرئيسية
    </a>
  </div>
</header>

<section class="section about">
  <h2>مقدمة</h2>
  <p>
    نلتزم في منظمة العلويين والأقليات السورية بحماية خصوصية زوار موقعنا،
    ونوضح في هذه الصفحة كيف نجمع البيانات، ولماذا، وكيف نحميها.
  </p>

  <h2>البيانات التي نجمعها</h2>
  <p>
    قد نقوم بجمع بعض المعلومات التي يقدمها المستخدم طوعًا مثل الاسم،
    البريد الإلكتروني، أو الرسائل المرسلة من خلال نموذج التواصل.
  </p>

  <h2>استخدام البيانات</h2>
  <p>
    تُستخدم البيانات فقط للتواصل مع المرسل أو تحسين خدماتنا.
    لا يتم بيع أو مشاركة البيانات مع أي طرف ثالث.
  </p>

  <h2>حماية البيانات</h2>
  <p>
    نعتمد إجراءات أمان تقنية وإدارية لحماية بياناتكم من الوصول غير المصرّح به
    أو الاستخدام الخاطئ.
  </p>

  <h2>حقوق المستخدم</h2>
  <p>
    يحق لأي مستخدم طلب حذف بياناته من أنظمتنا في أي وقت من خلال التواصل معنا عبر البريد:
    <a href="mailto:info@thealawites.com">info@thealawites.com</a>
  </p>

  <h2>الامتثال للائحة حماية البيانات الأوروبية (GDPR)</h2>
  <p>
  تلتزم المنظمة بأحكام اللائحة العامة لحماية البيانات الأوروبية (GDPR)،
  ويحق للمستخدم طلب الوصول إلى بياناته، تصحيحها، أو حذفها في أي وقت.
  </p>

  <h2>خصوصية الحالات الموثّقة</h2>
  <p>
  جميع المعلومات المتعلقة بالحالات الإنسانية تُعامل بسرية تامة،
  ولا يتم مشاركتها مع أي جهة خارجية إلا بموافقة صريحة من صاحب الحالة.
  </p>

  <h2>ملفات تعريف الارتباط (Cookies)</h2>
  <p>
  قد يستخدم الموقع ملفات تعريف الارتباط لأغراض تحليلية وتحسين تجربة المستخدم.
  يمكن للمستخدم تعطيل الكوكيز من إعدادات المتصفح في أي وقت.
  </p>

</section>

@endsection
