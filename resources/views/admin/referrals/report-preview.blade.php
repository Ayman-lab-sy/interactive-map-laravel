@extends('admin.layouts.app')

@section('title', 'Report Preview')

@section('content')

<div class="page-title" style="font-size:20px; font-weight:600; margin-bottom:20px;">
    Report Preview – {{ $referral->entity_name }}
</div>

<div class="card" style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:6px;">

    <div style="margin-bottom:15px; color:#475569;">
        <strong>Track:</strong> {{ $referral->referral_track }}
    </div>

    <div style="border:1px solid #e2e8f0; padding:20px;">
        {!! $report_html !!}
    </div>

</div>

@endsection
