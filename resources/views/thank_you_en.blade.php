@extends('layouts.main')

@section('title', 'Thank You for Contacting Us')

@section('content')

<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">

  <h1>Thank You for Contacting Us</h1>
  <p>We have received your message and will get back to you as soon as possible.</p>

  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      Back to Home
    </a>
  </div>
</header>

@endsection
