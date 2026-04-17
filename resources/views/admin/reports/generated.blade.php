@extends('admin.layouts.app')

@section('title', 'Generated Reports')

@section('content')

<div class="page-title" style="font-size:20px;font-weight:600;margin-bottom:20px;">
    التقارير المولَّدة
</div>

<div class="card" style="background:#fff; border:1px solid #e2e8f0; padding:0; border-radius:6px;">

    @if($reports->isEmpty())
        <p style="color:#64748b;">لا توجد تقارير مولَّدة حتى الآن.</p>
    @else
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:3%; text-align:center;">#</th>
                    <th style="width:7%; text-align:center;">رقم الإحالة</th>
                    <th style="width:13%; text-align:center;">رقم الحالة</th>
                    <th style="width:25%; text-align:center;">الجهة</th>
                    <th style="width:8%; text-align:center;">المسار</th>
                    <th style="width:6%; text-align:center;">الحالة</th>
                    <th style="width:5%; text-align:center;">تم التحميل؟</th>
                    <th style="width:5%; text-align:center;">عدد التحميلات</th>
                    <th style="width:8%; text-align:center;">آخر تحميل</th>
                    <th style="width:8%; text-align:center;">وُلِّد بواسطة</th>
                    <th style="width:7%; text-align:center;">تاريخ التوليد</th>
                    <th style="width:5%; text-align:center;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $i => $r)
                  @php
                      $url = \App\Helpers\ReferralRouteHelper::resolve($r);
                  @endphp

                    <tr
                    onclick="window.location='{{ $url }}'"
                    class="referral-row"
                    data-status="{{ $r->referral_status }}"
                    >
                        <td class="referral-main-cell">{{ $i + 1 }}</td>
                        <td class="referral-main-cell">REF-{{ $r->referral_id }}</td>
                        <td>{{ $r->case_number }}</td>
                        <td>{{ $r->entity_name }}</td>
                        <td>
                            <span class="track-badge" data-track="{{ $r->referral_track }}">
                                {{ match($r->referral_track) {
                                    'SPECIAL_PROCEDURES'      => 'الإجراءات الخاصة',
                                    'HUMANITARIAN_PROTECTION' => 'الحماية الإنسانية',
                                    'NGO_LEGAL'               => 'المسار القانوني',
                                    'UN_ACCOUNTABILITY'       => 'المساءلة الأممية',
                                    default                   => $r->referral_track
                                } }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge" data-status="{{ $r->referral_status }}">
                                {{ match($r->referral_status) {
                                    'prepared' => 'قيد التحضير',
                                    'ready_for_generation' => 'جاهزة للتوليد',
                                    'generated' => 'تم توليد التقرير',
                                    default => $r->referral_status
                                } }}
                            </span>
                        </td>
                        <td>
                            @if($r->downloads_count > 0)
                                <span style="color:#16a34a;font-weight:600;">نعم</span>
                            @else
                                <span style="color:#dc2626;">لا</span>
                            @endif
                        </td>
                        <td>{{ $r->downloads_count }}</td>
                        <td>{{ $r->last_downloaded_at ? \Carbon\Carbon::parse($r->last_downloaded_at)->toDateTimeString() : '—' }}</td>
                        <td>{{ $r->generated_by_name ?? '—' }}</td>
                        <td>{{ $r->generated_at ? \Carbon\Carbon::parse($r->generated_at)->toDateTimeString() : '—' }}</td>
                        <td>
                            @if(in_array($r->referral_status, ['generated','exported']))
                                <a href="{{ route('admin.referrals.download-pdf', $r->referral_id) }}" 
                                class="btn btn-sm btn-outline-primary">
                                    PDF
                                </a>
                            @elseif(!$r->has_legal_narrative)
                                <span style="color:#dc2626;margin-right:6px;">صياغة ناقصة</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $reports->links('pagination::bootstrap-4') }}
        </div>
    @endif
    </div>

</div>
@endsection
