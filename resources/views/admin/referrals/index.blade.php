@extends('admin.layouts.app')

@section('title', 'Referrals')

@section('content')

<div class="page-title" style="font-size:20px; font-weight:600; margin-bottom:20px;">
    قائمة الإحالات
</div>

<div class="card" style="background:#fff; border:1px solid #e2e8f0; padding:0; border-radius:6px;">

    @if($referrals->count())
    <div class="table-wrapper">
        <table class="table">
            <thead>
            <tr style="background:#f8fafc;">
                <th style="width:7%; text-align:center;">رقم الإحالة</th>
                <th style="width:15%; text-align:center;">رقم الحالة</th>
                <th style="width:30%; text-align:center;">الجهة</th>
                <th style="width:20%; text-align:center;">المسار</th>
                <th style="width:20%; text-align:center;">وضع الإحالة</th>
                <th style="width:8%; text-align:center;">تاريخ الإنشاء</th>
            </tr>
            </thead>
            @foreach($referrals as $ref)

              @php
                  $url = \App\Helpers\ReferralRouteHelper::resolve($ref);
              @endphp
  
                <tr
                    onclick="window.location='{{ $url }}'"
                    class="referral-row"
                    data-status="{{ $ref->referral_status }}"
                >
                    <td class="referral-main-cell">
                        <span>{{ $ref->id }}</span>
                    </td>

                    <td class="referral-main-cell">{{ $ref->case_number }}</td>
                    <td>{{ $ref->entity_name }}</td>
                    <td>
                        <span class="track-badge" data-track="{{ $ref->referral_track }}">
                            {{ match($ref->referral_track) {
                                'SPECIAL_PROCEDURES'      => 'الإجراءات الخاصة',
                                'HUMANITARIAN_PROTECTION' => 'الحماية الإنسانية',
                                'NGO_LEGAL'               => 'المسار القانوني',
                                'UN_ACCOUNTABILITY'       => 'المساءلة الأممية',
                                default                   => $ref->referral_track
                            } }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge" data-status="{{ $ref->referral_status }}">
                            {{ match($ref->referral_status) {
                                'prepared' => 'قيد التحضير',
                                'ready_for_generation' => 'جاهزة للتوليد',
                                'generated' => 'تم توليد التقرير',
                                default => $ref->referral_status
                            } }}
                        </span>
                    </td>
                    <td> {{ \Carbon\Carbon::parse($ref->created_at)->toDateString() }}</td>
                </tr>

            @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $referrals->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @else
        <p style="color:#64748b;">لا توجد إحالات مسجّلة حتى الآن.</p>
    @endif

</div>

@endsection
