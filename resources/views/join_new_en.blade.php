@extends('layouts.main')

@section('title', 'Join the Alawites & Syrian Minorities Organization')

@section('meta')

@php
$description = "Join the Alawites & Syrian Minorities Organization for Justice and Peace and contribute to human rights advocacy and minority protection efforts.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="Join the Organization | Alawites & Syrian Minorities">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Join the Organization">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "The Alawites & Syrian Minorities Organization for Justice and Peace",
  "url": "https://www.thealawites.com",
  "potentialAction": {
    "@type": "JoinAction",
    "target": "{{ request()->fullUrl() }}"
  }
}
</script>

@endsection


@section('content')

<header class="hero">
    <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">

    <h1>Join the Organization</h1>
    <p>
        Be part of our efforts to support justice, human rights, and the protection of Syrian minorities.
    </p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
            Back to Home
        </a>
    </div>
</header>

<section class="form-section">
    <div class="form-container">

        <h2>Membership Application</h2>
        <p style="max-width:600px; margin:auto; text-align:center; color:#555;">
         We welcome individuals interested in contributing to the advancement of the organization’s mission. All submissions are treated with strict confidentiality.
        </p>

        <form method="POST" action="{{ route('join.post', ['locale' => app()->getLocale()]) }}">
            @csrf

            <!-- Name -->
            <div class="form-row">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" required>
                </div>

                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required>
                <small>This email will be used to send the verification code.</small>
            </div>

            <!-- Birth date & Gender -->
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth *</label>
                    <input type="date" name="birth_date" required>
                </div>

                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="none">Prefer not to say</option>
                    </select>
                </div>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label>Phone Number (optional)</label>
                <input type="text" name="phone" placeholder="+43 681 10868580">
            </div>

            <!-- Additional info -->
            <h4 class="section-subtitle">Additional Information (optional)</h4>

            <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="street">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postcode">
                </div>

                <div class="form-group">
                    <label>City / Location</label>
                    <input type="text" name="location">
                </div>
            </div>

            <!-- Agreements -->
            <div class="form-checkbox">
                <label>
                    <input type="checkbox" name="aggreement_2" value="1">
                    I agree to the use of my data for communication purposes only
                </label>
            </div>

            <label>
              <input type="checkbox" name="aggrement_1" value="1" required>
              I agree to the 
              <a href="{{ route('privacy.new', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener">
                Privacy Policy
              </a>
            </label>

            <!-- Submit -->
            <div class="form-submit" style="text-align: center;">
                <button type="submit" class="btn btn-outline">
                    Submit Membership Request
                </button>

                <p class="form-note">
                    A verification code will be sent to your email after submission.
                </p>
            </div>

        </form>

    </div>
</section>

@endsection
