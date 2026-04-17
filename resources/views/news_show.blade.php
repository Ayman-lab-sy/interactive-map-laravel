@extends('layouts.main')

@section('title', 
    (app()->getLocale() === 'en' && $news->title_en 
        ? $news->title_en 
        : $news->title
    ) . ' | Organization of Alawites and Syrian Minorities'
)

@section('meta')

@php
    $description = \Illuminate\Support\Str::limit(strip_tags(
        app()->getLocale() === 'en' && $news->content_en
            ? $news->content_en
            : $news->content
    ), 160);
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="{{ app()->getLocale() === 'en' && $news->title_en ? $news->title_en : $news->title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="article">
<meta property="article:published_time" content="{{ \Carbon\Carbon::parse($news->date)->toIso8601String() }}">
<meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($news->updated_at)->toIso8601String() }}">
<meta property="article:author" content="Organization of Alawites and Syrian Minorities for Justice and Peace">

<meta property="og:site_name" content="Organization of Alawites and Syrian Minorities for Justice and Peace">

<meta property="og:url" content="{{ request()->fullUrl() }}">

@if($news->image)
<meta property="og:image" content="{{ url('storage/'.$news->image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ app()->getLocale() === 'en' && $news->title_en ? $news->title_en : $news->title }}">
<meta name="twitter:description" content="{{ $description }}">
@if($news->image)
<meta name="twitter:image" content="{{ url('storage/'.$news->image) }}">
@endif


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{{ app()->getLocale() === 'en' && $news->title_en ? $news->title_en : $news->title }}",
  "description": "{{ $description }}",
  "datePublished": "{{ \Carbon\Carbon::parse($news->date)->toIso8601String() }}",
  "dateModified": "{{ \Carbon\Carbon::parse($news->updated_at)->toIso8601String() }}",
  "author": {
    "@type": "Organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Organization of Alawites and Syrian Minorities for Justice and Peace",
    "logo": {
      "@type": "ImageObject",
      "url": "https://www.thealawites.com/assets/logo.png"
    }
  }@if($news->image),
  "image": ["{{ url('storage/'.$news->image) }}"]@endif,
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ request()->fullUrl() }}"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "https://www.thealawites.com/{{ app()->getLocale() }}"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "News",
    "item": "https://www.thealawites.com/{{ app()->getLocale() }}/news-new"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "{{ app()->getLocale() === 'en' && $news->title_en ? $news->title_en : $news->title }}",
    "item": "{{ request()->fullUrl() }}"
  }]
}
</script>

@endsection

@section('content')

<section class="section">
    <h1>
        {{ app()->getLocale() === 'en' && $news->title_en
            ? $news->title_en
            : $news->title
        }}
    </h1>

    <small>
        {{ \Carbon\Carbon::parse($news->date)->format('F d, Y') }}
    </small>

    @if($news->image)
        <img src="{{ url('storage/'.$news->image) }}" loading="lazy" alt="{{ $news->title }}" style="max-width:100%; margin:20px 0;">
    @endif

    <div class="news-content">
        {!! nl2br(e(
            app()->getLocale() === 'en' && $news->content_en
                ? $news->content_en
                : $news->content
        )) !!}
    </div>

    <div style="text-align:center; margin-top:30px;">
        <a href="{{ url('/'.app()->getLocale().'/news-new') }}" class="btn btn-outline">
            {{ app()->getLocale() === 'en' ? 'Back to News' : 'العودة للأخبار' }}
        </a>
    </div>
</section>

@endsection
