@extends('admin.layouts.app')

@section('title', 'إنشاء إحالة')

@section('content')

<div class="page-header">
    <h2>إنشاء إحالة</h2>
    <div class="page-subtitle">
        رقم الحالة: <strong>{{ $case->case_number }}</strong>
    </div>
</div>

<div class="referral-create-wrapper">

    <div class="card referral-create-card">

        <form method="POST" action="{{ route('admin.referrals.store', $case->id) }}">
            @csrf

            <div class="form-group">
                <label for="entity_id">الجهة المستقبِلة</label>

                <select name="entity_id" id="entity_id" class="form-control" required>
                    <option value="">— اختر الجهة —</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->entity_name }}
                        </option>
                    @endforeach
                </select>

                @error('entity_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.cases.show', $case->id) }}" class="btn-secondary">
                    رجوع
                </a>

                <button type="submit" class="btn-primary">
                    إنشاء الإحالة
                </button>
            </div>
        </form>

    </div>

</div>

@endsection
