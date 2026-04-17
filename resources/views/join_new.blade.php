@extends('layouts.main')

@section('title', 'انضم للمنظمة')

@section('meta')

@php
$description = "انضم إلى منظمة العلويين والأقليات السورية للعدالة والسلام وكن جزءًا من جهود الدفاع عن حقوق الإنسان والتمثيل السياسي والدعم القانوني.";
@endphp

<meta name="description" content="{{ $description }}">

<meta property="og:title" content="انضم إلى المنظمة | منظمة العلويين والأقليات السورية">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="انضم إلى المنظمة | منظمة العلويين والأقليات السورية">
<meta name="twitter:description" content="{{ $description }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "JoinAction",
  "name": "طلب الانضمام إلى منظمة العلويين والأقليات السورية",
  "description": "{{ $description }}",
  "target": "{{ request()->fullUrl() }}"
}
</script>

@endsection


@section('content')

<header class="hero">
    <img src="{{ asset('assets/logo.png') }}" alt="شعار المنظمة" class="logo">

    <h1>انضم إلى المنظمة</h1>
    <p>
        كن جزءًا من جهودنا في دعم العدالة وحقوق الإنسان للأقليات السورية.
    </p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
            العودة إلى الصفحة الرئيسية
        </a>
    </div>
</header>

<section class="form-section">
    <div class="form-container">

        <h2>طلب الانضمام</h2>
        <p style="max-width:600px; margin:auto; text-align:center;">
        نرحب بجميع الأفراد الراغبين بالمساهمة في دعم أهداف المنظمة.
        يتم التعامل مع جميع الطلبات بسرية تامة.
        </p>

        <form method="POST" action="{{ route('join.post', ['locale' => app()->getLocale()]) }}">
            @csrf

            <!-- الاسم -->
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم الأول *</label>
                    <input type="text" name="first_name" required>
                </div>

                <div class="form-group">
                    <label>اسم العائلة *</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <!-- البريد الإلكتروني -->
            <div class="form-group">
                <label>البريد الإلكتروني *</label>
                <input type="email" name="email" required>
                <small>سيتم استخدام هذا البريد لإرسال رمز التحقق.</small>
            </div>

            <!-- تاريخ الميلاد + الجنس -->
            <div class="form-row">
                <div class="form-group">
                    <label>تاريخ الميلاد *</label>
                    <input type="date" name="birth_date" required>
                </div>

                <div class="form-group">
                    <label>الجنس *</label>
                    <select name="gender" required>
                        <option value="">اختر</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                        <option value="none">أفضل عدم الإجابة</option>
                    </select>
                </div>
            </div>

            <!-- الهاتف -->
            <div class="form-group">
                <label>رقم الهاتف (اختياري)</label>
                <input type="text" name="phone" placeholder="+43 681 10868580">
            </div>

            <!-- معلومات إضافية -->
            <h4 class="section-subtitle">معلومات إضافية (اختيارية)</h4>

            <div class="form-group">
                <label>العنوان</label>
                <input type="text" name="street">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الرمز البريدي</label>
                    <input type="text" name="postcode">
                </div>

                <div class="form-group">
                    <label>المدينة / الموقع</label>
                    <input type="text" name="location">
                </div>
            </div>

            <!-- الموافقات -->
            <label>
              <input type="checkbox" name="aggrement_1" value="1" required>
              أوافق على 
              <a href="{{ route('privacy.new', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener">
                سياسة الخصوصية
              </a>
            </label>

            <div class="form-checkbox">
                <label>
                    <input type="checkbox" name="aggreement_2" value="1">
                    أوافق على استخدام بياناتي لغرض التواصل فقط
                </label>
            </div>

            <!-- زر الإرسال -->
            <div class="form-submit" style="text-align: center;">
                <button type="submit" class="btn btn-outline">
                    إرسال طلب الانضمام
                </button>

                <p class="form-note">
                    بعد الإرسال سيصلك رمز تحقق على بريدك الإلكتروني.
                </p>
            </div>

        </form>
    </div>
</section>

@endsection
