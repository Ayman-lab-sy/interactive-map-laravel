@extends('layouts.main')

@section('title', 'Update Added')

@section('content')

<header class="hero">
    <h1>✅ Information Added Successfully</h1>
    <p>The update has been linked to the same case and saved securely</p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
           class="btn btn-outline">
            Back to Home Page
        </a>
    </div>
</header>

<main class="main-content container">
    <div class="form-container" style="text-align:center">

        <p>
            You can add more updates at any time using
            <br>
            <strong>your Case Number</strong> and <strong>Follow-up Code</strong>
        </p>

        <div style="margin-top:30px">
            <a href="{{ url(app()->getLocale().'/documentation/follow-up') }}"
               class="btn btn-outline">
                ➕ Add another update
            </a>
        </div>

    </div>
</main>

@endsection
