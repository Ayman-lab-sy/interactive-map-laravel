@extends('admin.layouts.app')

@section('title', 'Edit Entity')

@section('content')

<div class="page-header">
    <h2>تعديل إعدادات الجهة</h2>
    <div class="page-subtitle">
        ضبط خصائص الجهة ومسار الإحالة
    </div>
</div>

<div class="card" style="max-width: 820px;">

    {{-- ========================= --}}
    {{-- Entity Info --}}
    {{-- ========================= --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

        <div class="info-block">
            <div class="label">اسم الجهة</div>
            <div class="value">{{ $entity->entity_name }}</div>
        </div>
        <div class="info-block">
            <div class="label">مسار الإحالة</div>
            <div class="value">
                <span class="track-badge" data-track="{{ $entity->referral_track }}">
                    {{ match($entity->referral_track) {
                        'SPECIAL_PROCEDURES'      => 'الإجراءات الخاصة',
                        'HUMANITARIAN_PROTECTION' => 'الحماية الإنسانية',
                        'NGO_LEGAL'               => 'المسار القانوني',
                        'UN_ACCOUNTABILITY'       => 'المساءلة الأممية',
                        default => $entity->referral_track
                    } }}
                </span>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- Alerts --}}
    {{-- ========================= --}}
    @if(session('success'))
        <div class="info-block">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="info-block" style="border-color:#fecaca;background:#fef2f2;">
            <ul style="margin:0;padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li class="form-error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.entities.update', $entity->id) }}">
        @csrf
        @method('PUT')

        {{-- ========================= --}}
        {{-- Default Template --}}
        {{-- ========================= --}}
        <div class="form-group">
                القالب الافتراضي للتقارير
            </label>

            <input
                type="text"
                name="default_template"
                class="form-control"
                value="{{ old('default_template', $entity->default_template) }}"
                placeholder="مثال: ohchr-accountability"
            >

            <div class="form-help">
                يُستخدم كقالب افتراضي عند توليد التقرير.
            </div>
        </div>

        {{-- ========================= --}}
        {{-- Accepts Family Data --}}
        {{-- ========================= --}}
        <div class="form-group">
                قبول بيانات العائلة
            </label>

            <select name="accepts_family_data" class="form-control">
                <option value="1" {{ $entity->accepts_family_data ? 'selected' : '' }}>
                    نعم – تقبل بيانات العائلة
                </option>
                <option value="0" {{ !$entity->accepts_family_data ? 'selected' : '' }}>
                    لا – لا تقبل بيانات العائلة
                </option>
            </select>

            <div class="form-help">
                يؤثر على عرض حقول أفراد العائلة داخل النظام.
            </div>
        </div>

        {{-- ========================= --}}
        {{-- Internal Notes --}}
        {{-- ========================= --}}
        <div class="form-group">
                ملاحظات داخلية (إدارية)
            </label>

            <textarea
                name="notes_internal"
                rows="4"
                class="form-control"
            >{{ old('notes_internal', $entity->notes_internal) }}</textarea>

            <div class="form-help">
                هذه الملاحظات داخلية ولا تظهر في التقارير.
            </div>
        </div>

        {{-- ========================= --}}
        {{-- Actions --}}
        {{-- ========================= --}}
        <div style="display:flex;gap:10px;margin-top:28px;">
            <button type="submit" class="btn btn-primary">
                حفظ التعديلات
            </button>

            <a href="{{ route('admin.entities.index') }}"
               class="btn btn-secondary">
                إلغاء
            </a>
        </div>

    </form>

</div>

@endsection
