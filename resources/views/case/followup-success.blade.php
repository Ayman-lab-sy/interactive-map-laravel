@extends('layouts.main')

@section('title', 'تمت إضافة التحديث')

@section('content')

<header class="hero">
    <h1>✅ تمّت إضافة المعلومات بنجاح</h1>
    <p>تم ربط التحديث بالحالة نفسها وحفظه بأمان</p>
    <div class="hero-buttons">
       <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
    </div>
</header>

<main class="main-content container">
    <div class="form-container" style="text-align:center">

        <p>
            يمكنك إضافة تحديثات أخرى في أي وقت باستخدام
            <br>
            <strong>رقم الحالة</strong> و<strong>رمز المتابعة</strong>
        </p>

        <div style="margin-top:30px">
            <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
               class="btn btn-outline">
                ➕ إضافة تحديث آخر
            </a>
        </div>

        

    </div>
</main>

@endsection
