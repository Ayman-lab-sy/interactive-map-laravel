@extends('layouts.main')

@section('title', 'Donate – The Alawites & Syrian Minorities Organization')

@section('meta')

@php
$description = "Support the Alawites & Syrian Minorities Organization for Justice and Peace by donating directly to our official bank account in Austria to fund human rights and humanitarian work.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="Donate | The Alawites & Syrian Minorities Organization">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Donate | Alawites Organization">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "DonateAction",
    "name": "Donate to the Alawites & Syrian Minorities Organization",
    "target": "{{ request()->fullUrl() }}"
  },
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "The Alawites & Syrian Minorities Organization for Justice and Peace",
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
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">
  <h1>Make a Donation</h1>
  <p>
    Support human rights and humanitarian efforts by donating directly to our official bank account.
  </p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      Back to Home
    </a>
  </div>
</header>

<section class="section news-section">
  <h2>📌 Bank Account Information</h2>

  <div class="card" style="max-width:600px; margin:auto; direction:ltr; line-height:2; text-align:left;">
    <p>
      <strong>Account Name:</strong><br>
      Humanitäre, Menschenrechtliche und Politische Verein Alawitis
    </p>

    <p>
      <strong>Bank:</strong><br>
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
      <strong>Country:</strong><br>
      Austria
    </p>

    <div style="margin-top:20px; font-size:15px; color:#555;">
      Please use the IBAN and account name exactly as shown above when making a bank transfer.
      <br>
      Thank you for your trust and support ❤️
    </div>
  </div>

  <div style="margin-top:25px; padding:15px; background:#f6fffa; border:1px solid #2ecc71;">
    <strong>🔒 Financial Transparency</strong>
    <p style="margin-top:8px;">
      All donations are used to support the organization's human rights advocacy,
      legal assistance, and humanitarian initiatives in accordance with our stated mission.
      Additional information regarding fund usage can be requested via our contact page.
    </p>
  </div>

</section>

@endsection
