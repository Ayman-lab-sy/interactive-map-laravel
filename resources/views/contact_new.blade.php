@extends('layouts.main')

@section('title', 'تواصل معنا - منظمة العلويين والأقليات السورية')

@section('meta')

@php
$description = "تواصل مع منظمة العلويين والأقليات السورية للعدالة والسلام للاستفسارات، التعاون، أو الدعم القانوني والحقوقي.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="تواصل معنا | منظمة العلويين والأقليات السورية">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="تواصل معنا | منظمة العلويين والأقليات السورية">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "تواصل معنا",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Organization of Alawites and Syrian Minorities for Justice and Peace",
  "url": "https://www.thealawites.com",
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer support",
    "email": "info@thealawites.com",
    "availableLanguage": ["Arabic", "English"]
  }
}
</script>

@endsection


@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>تواصل معنا</h1>
  <p>نسعد بتواصلك معنا لأي استفسار أو تعاون</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      العودة للصفحة الرئيسية
    </a>
  </div>
</header>

<section class="section about">
  <h2>أرسل لنا رسالة</h2>

  <form action="{{ route('contact.send', ['locale' => app()->getLocale()])  }}" method="POST"
        style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px;">
    @csrf

    <!-- Honeypot field (لا يراه المستخدم) -->
    <input type="text" name="website"
           style="position:absolute; left:-9999px;"
           tabindex="-1"
           autocomplete="off">
           
    <input type="text" name="name" placeholder="الاسم الكامل" required
           style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <input type="email" name="email" placeholder="البريد الإلكتروني" required
           style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <textarea name="message" placeholder="اكتب رسالتك هنا" rows="6" required
              style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

    <input type="hidden" name="locale" value="ar">
    
    <button type="submit" class="btn">إرسال</button>
  </form>

  <div style="
      margin-top:45px;
      padding:25px;
      background:#f8f9fc;
      border:1px solid #e3e6f0;
      border-radius:10px;
      text-align:center;
      max-width:600px;
      margin-left:auto;
      margin-right:auto;
  ">
      <h3 style="margin-bottom:10px;">📧 تواصل مباشر</h3>

      <p style="margin-bottom:15px; color:#555;">
          إذا كنت تفضل التواصل عبر البريد الإلكتروني مباشرة،
          يمكنك مراسلتنا على العنوان الرسمي التالي:
      </p>

      <a href="mailto:info@thealawites.com"
         class="btn">
          info@thealawites.com
      </a>
  </div>

  <div style="margin-top:30px; text-align:center; max-width:600px; margin-left:auto; margin-right:auto;">
    <h3>سياسة الاستجابة</h3>
    <p>
      نحرص على الرد على جميع الرسائل خلال فترة زمنية معقولة.
      في حال عدم تلقي رد خلال أيام قليلة، يرجى التحقق من البريد غير الهام.
    </p>
  </div>

</section>

@endsection
