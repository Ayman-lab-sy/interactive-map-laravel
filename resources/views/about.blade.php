@extends('layouts.main')

@section('title', 'من نحن | منظمة العلويين والأقليات السورية للعدالة والسلام')

@section('meta')

@php
$description = "منظمة مستقلة غير ربحية مسجلة في النمسا تعمل على حماية وتمكين الأقليات السورية، عبر التوثيق الحقوقي، الدعم القانوني، والمناصرة الدولية.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="من نحن | منظمة العلويين والأقليات السورية">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="من نحن | منظمة العلويين والأقليات السورية">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "من نحن",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}",
  "publisher": {
    "@type": "Organization",
    "name": "منظمة العلويين والأقليات السورية للعدالة والسلام"
  }
}
</script>

@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>منظمة العلويين والأقليات السورية للعدالة والسلام</h1>
  <h2>من نحن؟</h2>

  <p>تعرف على الرؤية، الرسالة، والأنشطة التي تقوم بها منظمة العلويين والأقليات السورية.</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      العودة للصفحة الرئيسية
    </a>
  </div>
</header>

<section class="section about">
  <h2>تعريف بالمنظمة</h2>
  <p>
    نحن منظمة مستقلة غير ربحية مسجّلة في النمسا، أُسّست بهدف حماية وتمكين الأقليات السورية، وعلى رأسها الأقلية العلوية. نعمل في مجال حقوق الإنسان  وتعزيز العدالة والسلام عبر التمثيل السياسي، والدعم القانوني، والتوثيق الحقوقي، والمساعدات الإنسانية.
  </p>

  <br>

  <p>
    تشمل أنشطتنا: الدفاع عن حقوق الإنسان، تقديم الدعم القانوني للضحايا، التنسيق مع منظمات دولية، وتوثيق الانتهاكات الواقعة على الأقليات. كما نعمل على نشر الوعي وتدريب الكوادر الشبابية لتمكينها من تمثيل مجتمعاتها بشكل فاعل.
  </p>

  <br>

  <p>
    رؤيتنا هي بناء مجتمع سوري عادل ومتعدد، يحترم التنوّع ويضمن المشاركة الكاملة لجميع مكوّناته في مستقبل البلاد.
  </p>
</section>

@endsection
