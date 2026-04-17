@extends('layouts.main')

@section('title', 'Case Received')

@section('content')

<header class="hero">
    <h1>✅ Your case has been successfully received</h1>
    <p>Please keep the following information to add any future updates</p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
           class="btn btn-outline">
            Back to Home Page
        </a>
    </div>
</header>

<main class="main-content container">
    <div class="form-container" style="text-align:center">

        <h3>Case Number</h3>
        <div style="font-size:20px;font-weight:bold;margin-bottom:15px">
            {{ session('case_number') }}
        </div>

        <h3>Follow-up Code</h3>
        <div style="font-size:20px;font-weight:bold;margin-bottom:25px">
            {{ session('followup_token') }}
        </div>

        <p style="color:#c0392b;font-weight:bold">
            ⚠️ Please keep your case number and follow-up code in a safe place
        </p>

        <div style="margin-top:30px">
            <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
               class="btn btn-outline">
                ➕ Add information later
            </a>
        </div>

    </div>
</main>

@endsection
