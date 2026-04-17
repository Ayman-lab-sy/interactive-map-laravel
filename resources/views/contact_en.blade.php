@extends('layouts.main')

@section('title', 'Contact Us')

@section('meta')

@php
$description = "Contact the Alawites & Syrian Minorities Organization for inquiries, cooperation, legal support, or human rights documentation.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="Contact Us | The Alawites & Syrian Minorities Organization">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Contact Us | The Alawites & Syrian Minorities Organization">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact Us",
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
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">
  <h1>Contact Us</h1>
  <p>We're happy to hear from you for any inquiry or cooperation.</p>

  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      Back to Home
    </a>
  </div>
</header>

<section class="section about">
  <h2>Send Us a Message</h2>

  <form action="{{ route('contact.send', ['locale' => app()->getLocale()]) }}" method="POST"
        style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px;">
    @csrf

    <!-- Honeypot field (لا يراه المستخدم) -->
    <input type="text" name="website"
           style="position:absolute; left:-9999px;"
           tabindex="-1"
           autocomplete="off">
           
    <input type="text" name="name" placeholder="Full Name" required
           style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <input type="email" name="email" placeholder="Email Address" required
           style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <textarea name="message" placeholder="Write your message here" rows="6" required
              style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

    <input type="hidden" name="locale" value="en">

    <button type="submit" class="btn">Send Message</button>

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
      <h3 style="margin-bottom:10px;">📧 Direct Contact</h3>

      <p style="margin-bottom:15px; color:#555;">
          If you prefer direct communication via email,
          you may contact us at our official address:
      </p>

      <a href="mailto:info@thealawites.com"
         class="btn">
          info@thealawites.com
      </a>
    </div>

    <div style="margin-top:30px; text-align:center; max-width:600px; margin-left:auto; margin-right:auto;">
      <h3>Response Time Policy</h3>
      <p>
       We aim to respond to all inquiries within a reasonable timeframe. Should you not receive a response within a few days, we kindly ask you to check your spam or junk folder.
      </p>
    </div>

  </form>
</section>
@endsection
