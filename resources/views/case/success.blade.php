@extends('layouts.main')

@section('title', 'تم استلام الحالة')

@section('content')

<header class="hero">
    <h1>✅ تم استلام حالتك بنجاح</h1>
    <p>يرجى الاحتفاظ بالمعلومات التالية لإضافة أي تحديث لاحقًا</p>
  <div class="hero-buttons">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
  </div>
</header>

<main class="main-content container">
    <div class="form-container" style="text-align:center">

        <h3>رقم الحالة</h3>
        <div style="font-size:20px;font-weight:bold;margin-bottom:15px">
            {{ session('case_number') }}
        </div>

        <h3>رمز المتابعة</h3>
        <div style="font-size:20px;font-weight:bold;margin-bottom:25px">
            {{ session('followup_token') }}
        </div>

        <p style="color:#c0392b;font-weight:bold">
            ⚠️ الرجاء حفظ رقم الحالة ورمز المتابعة في مكان آمن
        </p>

        <div style="margin-top:30px">
            <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
               class="btn btn-outline">
                ➕ إضافة معلومات لاحقًا
            </a>
        </div>

    </div>
</main>

@endsection
