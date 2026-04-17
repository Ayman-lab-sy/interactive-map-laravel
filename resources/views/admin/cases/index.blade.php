@extends('admin.layouts.app')

@section('title', 'قائمة الحالات')

@section('content')


@php
    function highlight($text, $term) {
        if (!$term || !$text) return $text;
        return preg_replace(
            '/(' . preg_quote($term, '/') . ')/i',
            '<mark class="search-highlight">$1</mark>',
            e($text)
        );
    }
@endphp

<div class="page-title" style="margin-bottom:20px; font-size:20px; font-weight:600;">
    📂 قائمة الحالات الحقوقية
</div>

@php
    $statusMap = [
        'new' => ['label' => 'جديدة', 'color' => '#64748b'],
        'under_review' => ['label' => 'قيد المراجعة', 'color' => '#f59e0b'],
        'documented' => ['label' => 'موثّقة', 'color' => '#0d9488'],
        'archived' => ['label' => 'مؤرشفة', 'color' => '#334155'],
    ];

@endphp

<form method="GET" action="{{ route('admin.cases.index') }}" class="cases-search-bar">
    <div class="search-wrapper">
        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="🔍 ابحث برقم الحالة أو الاسم…"
            class="search-input"
        >

        @if(request()->filled('q'))
            <a href="{{ route('admin.cases.index') }}" class="search-clear">
                ✕
            </a>
        @endif
    </div>
</form>



    <h3 class="page-title">جميع الحالات المسجّلة</h3>



    @if(request()->filled('q'))
        <div class="info-box info-info" style="margin-bottom:12px;">
            🔍 تم العثور على <strong>{{ $cases->total() }}</strong> حالة مطابقة للبحث
        </div>
    @endif

    @if($cases->count())
    <div class="table-card">
        <table class="table">
            <thead style="background:#f1f5f9;">
                <tr>
                    <th style="width:7%; text-align:center;">#</th>
                    <th style="width:15%; text-align:center;">رقم الحالة</th>
                    <th style="width:20%; text-align:center;">الموقع</th>
                    <th style="width:15%; text-align:center;">تاريخ الإبلاغ</th>
                    <th style="width:20%; text-align:center;">الحالة</th>
                    <th style="width:10%; text-align:center;">مدة المراجعة</th>
                    <th style="width:13%; text-align:center;">الإحالات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cases as $index => $case)
                    @php
                        $status = $statusMap[$case->status] ?? [
                            'label' => $case->status,
                            'color' => '#64748b'
                        ];

                        $rowClass = '';

                        if ($case->status === 'under_review' && $case->review_days !== null) {
                            if ($case->review_days >= 3 && $case->review_days <= 7) {
                                $rowClass = 'review-warning';
                            } elseif ($case->review_days > 7) {
                                $rowClass = 'review-danger';
                            }
                        }
                    @endphp

                    <tr
                        class="referral-row {{ $rowClass }}"
                        data-case-status="{{ $case->status }}"
                        onclick="window.location='{{ route('admin.cases.show', $case->id) }}'"
                    >

                        <td class="referral-main-cell">{{ $index + 1 }}</td>
                        <td class="referral-main-cell">{!! highlight($case->case_number, request('q')) !!}</td>
                        <td>{{ $case->location ?? '—' }}</td>
                        <td>{{ optional($case->created_at)->toDateString() }}</td>
                        <td>
                            <span class="status-badge" data-case-status="{{ $case->status }}">
                                {{ match($case->status) {
                                    'new'          => 'جديدة',
                                    'under_review' => 'قيد المراجعة',
                                    'documented'   => 'موثّقة',
                                    'archived'     => 'مؤرشفة',
                                    default        => $case->status
                                } }}
                            </span>

                        </td>
                        <td>
                            @if($case->status === 'under_review' && $case->review_days !== null)

                                @php
                                    $days = $case->review_days;
                                @endphp

                                @if($days < 3)
                                    <span style="color:#64748b;">
                                        {{ $days }} يوم
                                    </span>

                                @elseif($days <= 7)
                                    <span style="color:#b45309; font-weight:600;">
                                        ⏳ {{ $days }} يوم
                                    </span>

                                @else
                                    <span style="color:#dc2626; font-weight:700;">
                                        ⚠ {{ $days }} يوم
                                    </span>
                                @endif

                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if(($case->referrals_count ?? 0) > 0)
                                <span class="status-badge" data-status="has-referrals">
                                    {{ $case->referrals_count }} إحالة
                                </span>
                            @else
                                <span style="color:#94a3b8;font-size:13px;">لا توجد إحالات</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px;">
            {{ $cases->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @else
        <p>لا توجد حالات مسجّلة حاليًا.</p>
    @endif


@endsection
