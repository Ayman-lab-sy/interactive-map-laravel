@extends('layouts.main')

@section('title',
    app()->getLocale() === 'en'
        ? 'Organization News | Alawites Organization'
        : 'أخبار المنظمة | منظمة العلويين'
)

@section('meta')

<meta name="description" content="{{ app()->getLocale() === 'en'
    ? 'Official news and updates from the Organization of Alawites and Syrian Minorities for Justice and Peace.'
    : 'الأخبار الرسمية والتحديثات الصادرة عن منظمة العلويين والأقليات السورية للعدالة والسلام.'
}}">

<meta property="og:title" content="{{ app()->getLocale() === 'en' ? 'Organization News' : 'أخبار المنظمة' }}">
<meta property="og:description" content="{{ app()->getLocale() === 'en'
    ? 'Official statements and latest news.'
    : 'البيانات الرسمية وآخر الأخبار.'
}}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:site_name" content="Organization of Alawites and Syrian Minorities for Justice and Peace">


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{{ app()->getLocale() === 'en' ? 'Organization News' : 'أخبار المنظمة' }}",
  "url": "{{ request()->fullUrl() }}",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      @foreach($news as $index => $item)
      {
        "@type": "ListItem",
        "position": {{ $news->firstItem() + $index }},
        "url": "{{ url(app()->getLocale() . '/news-new/' . $item->slug) }}",
        "name": "{{ app()->getLocale() === 'en' && $item->title_en ? $item->title_en : $item->title }}"
      }@if(!$loop->last),@endif
      @endforeach
    ]
  }
}
</script>

@endsection


@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">

  <h1>
    {{ app()->getLocale() === 'en'
        ? 'Organization News'
        : 'أخبار المنظمة'
    }}
  </h1>

  <p>
    {{ app()->getLocale() === 'en'
        ? 'Follow the official statements and news issued by the Alawites & Syrian Minorities Organization'
        : 'تابع أرشيف البيانات والأخبار الصادرة عن منظمة العلويين والأقليات السورية'
    }}
  </p>

  <div class="hero-buttons">
    <a href="{{ url('/' . app()->getLocale()) }}" class="btn btn-outline">
      {{ app()->getLocale() === 'en'
          ? 'Back to Home'
          : 'العودة للصفحة الرئيسية'
      }}
    </a>
  </div>
</header>

<section class="section news-section">
  <h2>
    {{ app()->getLocale() === 'en'
        ? 'All News'
        : 'كل الأخبار'
    }}
  </h2>

  <div class="grid">

    @forelse($news as $item)
      <div class="card">

        @if($item->image)
          <img
            src="{{ asset('storage/'.$item->image) }}"
            alt="{{ app()->getLocale() === 'en' ? $item->title_en : $item->title }}"
          >
        @endif

        <h3>
          {{ app()->getLocale() === 'en'
              ? ($item->title_en ?: $item->title)
              : $item->title
          }}
        </h3>

        <small>{{ $item->date }}</small>

        <p>
          {{ app()->getLocale() === 'en'
              ? ($item->summary_en ?: $item->summary)
              : $item->summary
          }}
        </p>

        <div style="display:flex; justify-content:center; margin-top:15px;">
          <a
            class="btn btn-outline"
            href="{{ route('news.show', ['locale' => app()->getLocale(), 'slug' => $item->slug]) }}"
          >
            {{ app()->getLocale() === 'en'
                ? 'Read more'
                : 'اقرأ المزيد'
            }}
          </a>
        </div>

      </div>
    @empty
      <p>
        {{ app()->getLocale() === 'en'
            ? 'No news available at the moment.'
            : 'لا يوجد أخبار حالياً.'
        }}
      </p>
    @endforelse

  </div>

  <div class="pagination-wrapper">
    {{ $news->links() }}
  </div>
</section>

@endsection
