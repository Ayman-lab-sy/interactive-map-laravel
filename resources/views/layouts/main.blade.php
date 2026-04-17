@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#7a001f">
  <meta property="og:site_name" content="Organization of Alawites and Syrian Minorities for Justice and Peace">
  <meta name="application-name" content="Organization of Alawites and Syrian Minorities for Justice and Peace">

  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <script>
    window.ASSISTANT_API_ENDPOINT = "/api/assistant/search";
  </script>

  <title>@yield('title')</title>

  @yield('meta')

  <link rel="canonical" href="{{ request()->url() }}" />

  <link rel="alternate" hreflang="ar"
        href="{{ url('/ar' . Str::after(request()->path(), app()->getLocale())) }}" />

  <link rel="alternate" hreflang="en"
        href="{{ url('/en' . Str::after(request()->path(), app()->getLocale())) }}" />

  <link rel="alternate" hreflang="x-default"
        href="{{ url('/ar') }}" />


  <link rel="stylesheet"
      href="{{ url('assets/thealawites_home.css') }}?v={{ filemtime(public_path('assets/thealawites_home.css')) }}">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">

  {{-- Organization Schema --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "https://www.thealawites.com/#organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace",
    "alternateName": "Alawite Association for Peace",
    "url": "https://www.thealawites.com",
    "logo": {
      "@type": "ImageObject",
      "url": "https://www.thealawites.com/favicon-32x32.png",
      "width": 500,
      "height": 500
    },
    "areaServed": "Europe",
    "foundingLocation": {
      "@type": "Place",
      "name": "Vienna, Austria"
    }
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "@id": "https://www.thealawites.com/#website",
    "url": "https://www.thealawites.com",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace",
    "publisher": {
      "@id": "https://www.thealawites.com/#organization"
    }
  }
  </script>

</head>


<body class="{{ app()->getLocale() === 'en' ? 'ltr' : '' }} {{ $pageClass ?? '' }}">

  @yield('content')

  @if(empty($hideFooter))
      @if (app()->getLocale() === 'en')
          @include('components.footer-en')
      @else
          @include('components.footer-ar')
      @endif
  @endif


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

@yield('map-scripts')

@yield('home-scripts')

</body>
</html>