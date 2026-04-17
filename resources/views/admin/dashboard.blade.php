@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-title" style="font-size:20px; font-weight:600; margin-bottom:20px;">
    نظرة عامة على لوحة التحكم
</div>

<div style="margin-top:24px;">
    <div class="page-title" style="font-size:16px;font-weight:600;margin-bottom:12px;">
         الحالات
    </div>
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:20px;">

    <a href="{{ route('admin.cases.index') }}"
       class="dashboard-card dashboard-card--info">
        <div class="dashboard-card-title">عدد الحالات</div>
        <div class="dashboard-card-number">{{ $casesCount }}</div>
    </a>

    <a href="{{ route('admin.cases.index', ['status' => 'under_review']) }}"
       class="dashboard-card {{ $underReviewCount > 0 ? 'dashboard-card--warning' : 'dashboard-card--neutral' }}">

        <div class="dashboard-card-title">
            قيد المراجعة
            @if($underReviewCount > 0)
                <span class="pulse-dot"></span>
            @endif
        </div>

        <div class="dashboard-card-number">
            {{ $underReviewCount }}
        </div>
    </a>

    <a href="{{ route('admin.cases.index', ['overdue' => 1]) }}"
       class="dashboard-card {{ $overdueCasesCount > 0 ? 'dashboard-card--danger' : 'dashboard-card--neutral' }}">

        <div class="dashboard-card-title">
            حالات متأخرة
            @if($overdueCasesCount > 0)
                <span class="pulse-dot"></span>
            @endif
        </div>

        <div class="dashboard-card-number">
            {{ $overdueCasesCount }}
        </div>
    </a> 

    <a href="{{ route('admin.cases.index', ['status' => 'new']) }}"
        class="dashboard-card 
            {{ $newCasesCount > 0 ? 'dashboard-card--alert' : 'dashboard-card--neutral' }}">

        <div class="dashboard-card-title">
            حالات جديدة
            @if($newCasesCount > 0)
                <span class="pulse-dot"></span>
            @endif
        </div>

        <div class="dashboard-card-number">
            {{ $newCasesCount }}
        </div>
    </a>

</div>
</div>

{{-- ========================= --}}
{{-- Referral Status Overview --}}
{{-- ========================= --}}
<div style="margin-top:24px;">
    <div class="page-title" style="font-size:16px;font-weight:600;margin-bottom:12px;">
         الإحالات
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:20px;">

        <a href="{{ route('admin.referrals.index') }}"
            class="dashboard-card dashboard-card--neutral">
            <div class="dashboard-card-title">عدد الإحالات</div>
            <div class="dashboard-card-number">{{ $referralsCount }}</div>
        </a>

        <a href="{{ route('admin.referrals.index', ['status' => 'prepared']) }}"
           class="dashboard-card dashboard-card--neutral">
            <div class="dashboard-card-title">قيد التحضير</div>
            <div class="dashboard-card-number">
                {{ $referralsByStatus['prepared'] ?? 0 }}
            </div>
        </a>

        <a href="{{ route('admin.referrals.index', ['status' => 'ready_for_generation']) }}"
           class="dashboard-card dashboard-card--warning">
            <div class="dashboard-card-title">جاهزة للتوليد</div>
            <div class="dashboard-card-number">
                {{ $referralsByStatus['ready_for_generation'] ?? 0 }}
            </div>
        </a>

        <a href="{{ route('admin.referrals.index', ['status' => 'generated']) }}"
           class="dashboard-card dashboard-card--success">
            <div class="dashboard-card-title">تم توليد التقرير</div>
            <div class="dashboard-card-number">
                {{ $referralsByStatus['generated'] ?? 0 }}
            </div>
        </a>

        <a href="{{ route('admin.reports.generated') }}"
           class="dashboard-card dashboard-card--reports">
            <div class="dashboard-card-title">التقارير المولَّدة</div>
            <div class="dashboard-card-number">{{ $reportsCount }}</div>
        </a>

    </div>
</div>

{{-- ========================= --}}
{{-- Recent Activity --}}
{{-- ========================= --}}
<div style="margin-top:28px;">
    <div class="page-title" style="font-size:16px;font-weight:600;margin-bottom:12px;">
        آخر النشاطات
    </div>

    <div class="table-card">
        @if($recentItems->isEmpty())
            <p style="padding:16px;color:#64748b;">لا يوجد نشاط حديث.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:10%; text-align:center;">رقم الإحالة</th>
                        <th style="width:60%; text-align:center;">الجهة</th>
                        <th style="width:15%; text-align:center;">الحالة</th>
                        <th style="width:15%; text-align:center;">الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentItems as $item)
                        @php
                           $url = \App\Helpers\ReferralRouteHelper::resolve($item);
                           $time = $item->generated_at ?? $item->created_at;
                        @endphp
                        <tr onclick="window.location='{{ $url }}'" 
                            class="referral-row" 
                            data-status="{{ $item->referral_status }}">
                            <td class="referral-main-cell">
                                <a>
                                    {{ $item->referral_id }} 
                                </a>
                            </td>

                            <td class="referral-main-cell" style=text-align:center;>
                                <a>
                                    {{ $item->entity_name }} 
                                </a>
                            </td>

                            <td>
                                <span class="status-badge" data-status="{{ $item->referral_status }}">
                                    {{ match($item->referral_status) {
                                        'prepared' => 'قيد التحضير',
                                        'ready_for_generation' => 'جاهزة للتوليد',
                                        'generated' => 'تم توليد التقرير',
                                        default => $item->referral_status
                                    } }}
                                </span>
                            </td>

                            <td title="{{ \Carbon\Carbon::parse($time)->format('Y-m-d H:i') }}">
                                {{ \Carbon\Carbon::parse($time)->locale('ar')->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>


@endsection
