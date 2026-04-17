@extends('layouts.main')

@section('title', 'إضافة معلومات لاحقًا')

@section('content')

<header class="hero">
    <h1>➕ إضافة تحديث على حالة</h1>
    <p>أدخل رقم الحالة ورمز المتابعة</p>
    <div class="hero-buttons">
       <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">العودة للصفحة الرئيسية</a>
    </div>
</header>

<main class="main-content container">
    <div class="form-container">

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('case.followup.store', app()->getLocale()) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>رقم الحالة</label>
                <input type="text" name="case_number" required>
            </div>

            <div class="form-group">
                <label>رمز المتابعة</label>
                <input type="text" name="followup_token" required>
            </div>

            <div class="form-group">
                <label>التحديث الجديد</label>
                <textarea name="update_description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>ملفات إضافية (اختياري)</label>
                <input type="file" name="documents[]" multiple>
            </div>

            <div style="text-align:center">
                <button class="btn btn-outline">إرسال التحديث</button>
            </div>

        </form>
    </div>
</main>

@endsection
