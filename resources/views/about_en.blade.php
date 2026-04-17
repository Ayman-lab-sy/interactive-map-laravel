@extends('layouts.main')

@section('title', 'About Us | Alawites & Syrian Minorities Organization for Justice and Peace')

@section('meta')

@php
$description = "An independent non-profit organization registered in Austria dedicated to protecting Syrian minorities through human rights documentation, legal advocacy, and international representation.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="About Us | Alawites & Syrian Minorities Organization">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="About Us | Alawites & Syrian Minorities Organization">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "About Us",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}",
  "publisher": {
    "@type": "Organization",
    "name": "Alawites & Syrian Minorities Organization for Justice and Peace"
  }
}
</script>

@endsection


@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">
  <h1>Alawites & Syrian Minorities Organization for Justice and Peace</h1>
  <h2>Who Are We?</h2>
  <p>Learn about the vision, mission, and activities of the Alawites & Syrian Minorities Organization.</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      Back to Home
    </a>
  </div>
</header>

<section class="section about">
  <h2>About the Organization</h2>

  <p>
    We are an independent, Austria-registered non-profit organization dedicated to advancing human rights and protecting and empowering Syrian minorities, with particular focus on the Alawite community. Our work centers on justice, peacebuilding, political representation, legal advocacy, human rights documentation, and humanitarian assistance.
  </p>

  <br>

  <p>
    Our activities include defending human rights, providing legal assistance to victims, coordinating with international organizations, and documenting violations committed against minorities. We also promote awareness and train young leaders to effectively represent their communities.
  </p>

  <br>

  <p>
    Our vision is to build a just and pluralistic Syrian society that respects diversity and ensures full participation of all its components in shaping the country’s future.
  </p>
</section>

@endsection
