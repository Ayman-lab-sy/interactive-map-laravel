@extends('admin.layouts.app')

@section('title', 'Entities Management')

@section('content')

<style>
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}
th, td {
    border: 1px solid #e2e8f0;
    padding: 10px;
    text-align: center;
    font-size: 14px;
}
th {
    background: #f1f5f9;
    font-weight: 600;
}
</style>

<div class="page-title">Entities Management</div>

<div style="margin-bottom:16px; display:flex; gap:12px;">
    <div style="background:#f1f5f9;padding:10px 16px;border-radius:8px;">
        <strong>إجمالي الجهات:</strong> {{ $totalEntities }}
    </div>
    <div style="background:#ecfeff;padding:10px 16px;border-radius:8px;">
        <strong>الجهات المفعّلة:</strong> {{ $activeEntities }}
    </div>
</div>

<form method="GET" style="margin-bottom:16px;">
    <select name="track" onchange="this.form.submit()" style="padding:8px;">
        <option value="">— كل المسارات —</option>
        @foreach($tracks as $key => $label)
            <option value="{{ $key }}" {{ request('track') === $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</form>

@if($entities->count())
<div class="table-card">
    <table class="table">
        <thead>
            <tr>
                <th style="width:5%; text-align:center;">#</th>
                <th style="width:10%; text-align:center;">نوع الجهة</th>
                <th style="width:30%; text-align:center;">اسم الجهة</th>
                <th style="width:10%; text-align:center;">مسار الإحالة</th>
                <th style="width:7%; text-align:center;">مفعّلة</th>
                <th style="width:15%; text-align:center;">القالب الافتراضي (للاطلاع فقط)</th>
                <th style="width:5%; text-align:center;">بيانات العائلة</th>
                <th style="width:18%; text-align:center;">ملاحظات داخلية</th>
                <th style="width:10%; text-align:center;">إجراءات</th>
            </tr>
        </thead>

        <tbody>
            @foreach($entities as $i => $entity)
                <tr>
                    <td class="referral-main-cell">{{ $i + 1 }}</td>
                    <td>
                        <span class="entity-type-badge" data-entity-type="{{ $entity->entity_type }}">
                            {{ match($entity->entity_type) {
                                'NGO' => 'منظمة حقوقية',
                                'UN' => 'جهة أممية',
                                'Humanitarian' => 'جهة إنسانية',
                                default => $entity->entity_type
                            } }}
                        </span>
                    </td>
                    <td>{{ $entity->entity_name }}</td>
                    <td style="text-align:center;">
                        <span class="track-badge" data-track="{{ $entity->referral_track }}">
                            {{ match($entity->referral_track) {
                                'SPECIAL_PROCEDURES'      => 'الإجراءات الخاصة',
                                'HUMANITARIAN_PROTECTION' => 'الحماية الإنسانية',
                                'NGO_LEGAL'               => 'المسار القانوني',
                                'UN_ACCOUNTABILITY'       => 'المساءلة الأممية',
                                default => $entity->referral_track
                            } }}
                        </span>
                    </td>
                    <td>
                        <span style="padding:4px 8px;border-radius:12px;color:#fff;
                            background:{{ $entity->is_active ? '#16a34a' : '#b91c1c' }}">
                            {{ $entity->is_active ? 'مفعّلة' : 'غير مفعّلة' }}
                        </span>
                    </td>
                    <td>{{ $entity->default_template ?? '—' }}</td>
                    <td>
                        <span style="padding:4px 8px;border-radius:12px;color:#fff;
                            background:{{ $entity->accepts_family_data ? '#2563eb' : '#64748b' }}">
                            {{ $entity->accepts_family_data ? 'نعم' : 'لا' }}
                        </span>
                    </td>
                    <td>{{ $entity->notes_internal ?? '—' }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.entities.edit', $entity->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            تعديل
                        </a>

                        <form method="POST"
                              action="{{ route('admin.entities.toggle', $entity->id) }}">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm {{ $entity->is_active ? 'btn-danger' : 'btn-success' }}">
                                {{ $entity->is_active ? 'تعطيل' : 'تفعيل' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No entities found.</p>
@endif
</div>

@endsection
