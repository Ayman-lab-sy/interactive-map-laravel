@extends('layouts.main')

@section('title', 'Add Information Later')

@section('content')

<header class="hero">
    <h1>➕ Add an Update to a Case</h1>
    <p>Enter your case number and follow-up code</p>

    <div class="hero-buttons">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
           class="btn btn-outline">
            Back to Home Page
        </a>
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

        <form method="POST"
              action="{{ route('case.followup.store', app()->getLocale()) }}"
              enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Case Number</label>
                <input type="text" name="case_number" required>
            </div>

            <div class="form-group">
                <label>Follow-up Code</label>
                <input type="text" name="followup_token" required>
            </div>

            <div class="form-group">
                <label>New Update</label>
                <textarea name="update_description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Additional Files (optional)</label>
                <input type="file" name="documents[]" multiple>
            </div>

            <div style="text-align:center">
                <button class="btn btn-outline">
                    Submit Update
                </button>
            </div>

        </form>
    </div>
</main>

@endsection
