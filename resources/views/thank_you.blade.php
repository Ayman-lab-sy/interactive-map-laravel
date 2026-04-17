@extends('layouts.main')

@section('title', 'شكرًا لتواصلك معنا')

@section('content')
<header class="hero">
  <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">
  <h1>شكرًا لتواصلك معنا</h1>
  <p>لقد استلمنا رسالتك وسنقوم بمراجعتها والرد عليك في أقرب وقت ممكن.</p>

  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
      العودة للصفحة الرئيسية
    </a>
  </div>
</header>

@endsection
