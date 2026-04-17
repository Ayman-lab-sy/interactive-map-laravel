@extends('layouts.main')

@section('title', 'Privacy Policy | The Alawites & Syrian Minorities Organization for Justice & Peace')

@section('meta')

@php
$description = "This Privacy Policy explains how The Alawites & Syrian Minorities Organization for Justice & Peace collects, uses, and protects personal data in compliance with European data protection standards (GDPR).";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="Privacy Policy | The Alawites & Syrian Minorities Organization">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Privacy Policy | The Alawites & Syrian Minorities Organization">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Privacy Policy",
  "description": "{{ $description }}",
  "url": "{{ request()->fullUrl() }}"
}
</script>

@endsection

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">
  <h1>Privacy Policy</h1>
  <p>Your privacy matters to us – this page explains how we handle your data.</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      Back to Home
    </a>
  </div>
</header>

<section class="section about">
  <h2>Introduction</h2>
  <p>
    The Alawites & Syrian Minorities Organization is committed to protecting the
    privacy of visitors to our website. This policy explains what data we collect,
    why we collect it, and how we protect it.
  </p>

  <h2>Data We Collect</h2>
  <p>
    We may collect certain information voluntarily provided by users, such as
    names, email addresses, or messages submitted through our contact forms.
  </p>

  <h2>Use of Data</h2>
  <p>
    Collected data is used solely to respond to inquiries or improve our services.
    We do not sell, rent, or share personal data with any third party.
  </p>

  <h2>Data Protection</h2>
  <p>
    We apply appropriate technical and organizational security measures to protect
    your data from unauthorized access, misuse, or disclosure.
  </p>

  <h2>User Rights</h2>
  <p>
    Users have the right to request the deletion of their personal data at any time
    by contacting us via email:
    <a href="mailto:info@thealawites.com">info@thealawites.com</a>
  </p>

  <h2>Compliance with the General Data Protection Regulation (GDPR)</h2>
  <p>
  As an organization registered in Austria, we comply with the European Union’s
  General Data Protection Regulation (GDPR). Users have the right to request access,
  correction, restriction, or deletion of their personal data at any time.
  </p>

  <h2>Confidentiality of Documented Cases</h2>
  <p>
  All humanitarian case submissions are treated with strict confidentiality.
  Information is never shared with external parties without explicit and informed consent
  from the case owner.
  </p>

  <h2>Confidentiality of Documented Cases</h2>
  <p>
  All humanitarian case submissions are treated with strict confidentiality.
  Information is never shared with external parties without explicit and informed consent
  from the case owner.
  </p>

</section>

@endsection
