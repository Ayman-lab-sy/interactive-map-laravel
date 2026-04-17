@extends('layouts.main')

@section('title', __('home.verify.title'))

@section('content')

<header class="hero">
    <img src="{{ asset('assets/logo.png') }}" alt="Organization Logo" class="logo">

    <h1>{{ __('home.verify.title') }}</h1>
    <p>{{ __('home.verify.paragraph') }}</p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
            {{ app()->getLocale() === 'ar' ? 'العودة إلى الصفحة الرئيسية' : 'Back to Home' }}
        </a>
    </div>
</header>

<section class="section about">
    <h2 style="text-align:center; margin-bottom:20px;">
        {{ app()->getLocale() === 'ar' ? 'إدخال رمز التحقق' : 'Enter Verification Code' }}
    </h2>

    <form action="{{ route('verify.post', ['locale' => app()->getLocale(), 'member_id' => $user_id]) }}"
          method="POST"
          style="max-width: 400px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px;">
        @csrf

        <label for="validation_code">
            {{ __('form.validation_code') }} *
        </label>

        <input type="text"
               name="validation_code"
               required
               value="{{ old('validation_code') }}"
               style="padding:10px; border:1px solid #ccc; border-radius:5px; text-align:center; font-size:18px;">

        @if ($errors->has('validation_code'))
            <span style="color:#c00; font-size:14px;">
                {{ $errors->first('validation_code') }}
            </span>
        @endif

        <button type="submit" class="btn" style="margin-top:10px;">
            {{ __('form.submit') }}
        </button>
    </form>
</section>

@endsection
