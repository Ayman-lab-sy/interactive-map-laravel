<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">

    <title>{{ $event->title }}</title>

    <meta property="og:title" content="عاجل: {{ $event->title }}">
    <meta property="og:description" content="{{ trim(mb_substr(preg_replace('/\s+/', ' ', strip_tags($event->description ?? '')), 0, 100)) . '...' }}">
    <meta property="og:image" content="{{ url($locale . '/og-image/' . $event->id) }}?v={{ time() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="خريطة الأحداث - سوريا">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="عاجل: {{ $event->title }}">
    <meta name="twitter:description" content="{{ trim(mb_substr(preg_replace('/\s+/', ' ', strip_tags($event->description ?? '')), 0, 100)) . '...' }}">
    <meta name="twitter:image" content="{{ url($locale . '/og-image/' . $event->id) }}">

    <meta name="description" content="{{ trim(mb_substr(preg_replace('/\s+/', ' ', strip_tags($event->description ?? '')), 0, 120)) }}">
    <meta property="og:locale" content="{{ $locale == 'ar' ? 'ar_AR' : 'en_US' }}">

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <script>
        setTimeout(() => {
            window.location.href = "/{{ $locale }}/map?event={{ $event->id }}";
        }, 2000);
    </script>
</head>
<body></body>
</html>