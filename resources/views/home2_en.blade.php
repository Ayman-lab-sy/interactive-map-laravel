@extends('layouts.main')

@section('title', 
'Alawites & Syrian Minorities Organization | Human Rights & Legal Advocacy in Europe'
)

@section('meta')

<meta name="description" content="Independent non-profit organization based in Austria advocating for Syrian minorities, providing human rights defense, legal aid, political representation and documentation of violations in Europe.">

<meta property="og:title" content="Alawites & Syrian Minorities Organization">
<meta property="og:description" content="Human rights advocacy, legal support and political representation for Syrian minorities in Europe.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="https://www.thealawites.com/assets/logo.png">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Alawites & Syrian Minorities Organization">
<meta name="twitter:description" content="Advocating for Syrian minorities and defending human rights in Europe.">
<meta name="twitter:image" content="https://www.thealawites.com/assets/logo.png">

@endsection

@section('content')

  <header class="hero">
    <img src="/assets/logo.png" alt="Organization Logo" class="logo">
    <h1>Alawites & Syrian Minorities Organization for Justice and Peace</h1>
    <p>Supporting human rights, political representation, legal aid, and documentation of violations.</p>
    <div class="hero-buttons">
      <a href="{{ route('donate', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
        Support Us
      </a>

      <a href="{{ route('join', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
        Join the Organization
      </a>

      <a href="{{ url(app()->getLocale().'/documentation-new') }}" class="btn btn-outline">
        Report a Case
      </a>

      <a href="{{ route('home', ['locale' => 'ar']) }}" class="btn">
        العربية
      </a>

    </div>
  </header>

  <div class="soft-launch-bar">
    <div class="soft-launch-text">
      This website is currently in a soft launch phase. We are continuously improving content and services. Thank you for your understanding.
    </div>
  </div>

  <section class="section documentation-cta" style="background:#f8fafc; text-align:center; padding:40px 20px;">

    <h2 style="font-size:26px; margin-bottom:15px;">
     Are you under direct threat or danger?
    </h2>

    <p style="font-size:16px; color:#444; max-width:700px; margin:0 auto 20px;">
     You can submit your case securely and confidentially, and it will be documented and followed up through international human rights channels.
    </p>

    <div style="margin-bottom:20px; color:#059669; font-size:14px;">
      ✔ All information is strictly confidential. 
      ✔ You can use a pseudonym 
      ✔ Your data will not be shared without your consent. 
    </div>

    <a href="{{ url(app()->getLocale().'/documentation-new') }}" 
       class="btn"
       style="font-size:18px; padding:12px 25px;">
      📩 Send my status now
    </a>

  </section>
  <section class="section goals">
    <h2>Our Goals</h2>
    <div class="grid">
      <div class="card">
        <img src="/assets/icons/awareness.svg" alt="Awareness">
        <h3>Awareness</h3>
        <p>Raise awareness in society about the challenges facing minorities.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/politics.svg" alt="Political Representation">
        <h3>Political Representation</h3>
        <p>Advocate for the right to self-determination and defend rights.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/legal.svg" alt="Legal Support">
        <h3>Legal Support</h3>
        <p>Provide legal aid and represent minorities internationally.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/aid.svg" alt="Humanitarian Aid">
        <h3>Humanitarian Aid</h3>
        <p>Deliver aid during crises (food, healthcare, education...)</p>
      </div>
      <div class="card">
        <img src="/assets/icons/training.svg" alt="Education & Training">
        <h3>Education & Training</h3>
        <p>Implement training programs to empower minorities in defending their rights.</p>
      </div>
      <div class="card">
        <img src="/assets/icons/docs.svg" alt="Documentation">
        <h3>Documentation</h3>
        <p>Document violations and crimes against minorities and report them to concerned authorities.</p>
      </div>
    </div>
  </section>

  <section class="section news-section">
    <h2>Our News</h2>
    <p style="text-align:center; margin-top:-20px; color:#555;">
      Follow the latest updates and announcements
    </p>

    <div class="grid">
      @forelse($latestNews ?? [] as $news)
        <div class="card">
          @if($news->image)
            <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title_en ?? $news->title }}">
          @endif

          <h3>{{ $news->title_en ?? $news->title }}</h3>
          <small>{{ $news->date }}</small>
          <p>{{ $news->summary_en ?? $news->summary }}</p>

          <div style="text-align:center; margin-top:15px;">
            <a class="btn btn-outline"
               href="{{ route('news.show', ['locale' => app()->getLocale(), 'slug' => $news->slug]) }}">
              Read more
            </a>
          </div>
        </div>
      @empty
        <p style="text-align:center;">No news available</p>
      @endforelse
    </div>

    <div style="text-align:center; margin-top:25px;">
      <a href="{{ route('news.new', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
        View all news
      </a>
    </div>
  </section>

  <section class="section about">
    <h2>Who Are We?</h2>
    <p>
      An independent non-profit organization registered in Austria, dedicated to protecting and empowering Syrian minorities across Europe. We advocate for human rights, provide legal aid, support political representation, and document violations affecting minority communities.
    </p>
    <div style="margin-top: 25px;">
      <a href="{{ route('about', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
        Learn More About Us
      </a>
    </div>
  </section>
  
@endsection

