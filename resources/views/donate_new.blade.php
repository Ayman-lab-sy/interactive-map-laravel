@extends('layouts.main')

@section('title', 'التبرع | منظمة العلويين والأقليات السورية للعدالة والسلام')

@section('meta')

@php
$description = "ادعم منظمة العلويين والأقليات السورية للعدالة والسلام عبر التبرع المباشر للحساب الرسمي في النمسا لدعم حقوق الإنسان والمساعدات الإنسانية.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="قدم دعمك | منظمة العلويين والأقليات السورية">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="قدم دعمك | منظمة العلويين">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "DonateAction",
    "name": "تبرع لمنظمة العلويين والأقليات السورية",
    "target": "{{ request()->fullUrl() }}"
  },
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace",
    "url": "https://www.thealawites.com",
    "areaServed": "Europe",
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "Austria"
    }
  }
]
</script>


@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>قدم دعمك</h1>
  <p>ساهم في دعم حقوق الإنسان والمساعدات الإنسانية عبر التبرع المباشر لحسابنا الرسمي</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
  </div>
</header>

<section class="section news-section">
  <h2>📌 معلومات الحساب البنكي</h2>

  <div class="card" style="max-width:600px; margin:auto; direction:rtl; line-height:2;">
    <p>
      <strong>اسم الحساب:</strong><br>
      Humanitäre, Menschenrechtliche und Politische Verein Alawitis
    </p>

    <p>
      <strong>البنك:</strong><br>
      Erste Bank der oesterreichischen Sparkassen AG
    </p>

    <p>
      <strong>IBAN:</strong><br>
      <b>AT53 2011 1854 3449 2300</b>
    </p>

    <p>
      <strong>BIC / SWIFT:</strong><br>
      <b>GIBAATWWXXX</b>
    </p>

    <p>
      <strong>الدولة:</strong><br>
      النمسا – Austria
    </p>

    <div style="margin-top:20px; font-size:15px; color:#555;">
      يرجى استخدام رقم IBAN واسم الحساب كما هو موضح أعلاه عند إجراء التحويل المصرفي.
      شكرًا لثقتكم ودعمكم ❤️
    </div>
  </div>

  <div style="margin-top:25px; padding:15px; background:#f6fffa; border:1px solid #2ecc71;">
    <strong>🔒 الشفافية المالية</strong>
    <p style="margin-top:8px;">
      يتم استخدام جميع التبرعات لدعم أنشطة المنظمة الحقوقية والإنسانية وفقاً للأهداف المعلنة.
      يمكن طلب معلومات إضافية حول آلية استخدام التبرعات عبر التواصل معنا.
    </p>
  </div>

</section>

@endsection
