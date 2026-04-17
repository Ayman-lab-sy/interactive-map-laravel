@extends('layouts.main')

@section('title', app()->getLocale() === 'ar' ? 'تم الانضمام بنجاح' : 'Membership Confirmed')

@section('content')

<header class="hero">
    <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">

    <h1>
        {{ app()->getLocale() === 'ar'
            ? 'تم تأكيد انضمامك بنجاح'
            : 'Your Membership Has Been Confirmed' }}
    </h1>

    <p>
        {{ app()->getLocale() === 'ar'
            ? 'شكرًا لانضمامك إلينا. تم تفعيل عضويتك بنجاح.'
            : 'Thank you for joining us. Your membership has been successfully activated.' }}
    </p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
            {{ app()->getLocale() === 'ar' ? 'العودة إلى الصفحة الرئيسية' : 'Back to Home' }}
        </a>
    </div>
</header>

<section class="section about" style="text-align:center;">
    <h2>
        {{ app()->getLocale() === 'ar'
            ? 'أهلاً بك في منظمتنا'
            : 'Welcome to Our Organization' }}
    </h2>

    <p>
        {{ app()->getLocale() === 'ar'
            ? 'يسعدنا انضمامك، وسنبقى على تواصل معك عبر البريد الإلكتروني.'
            : 'We are glad to have you with us. We will stay in touch via email.' }}
    </p>
</section>

@endsection
