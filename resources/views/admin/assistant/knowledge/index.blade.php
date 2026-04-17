@extends('voyager::master')

@section('content')
<div class="page-content container-fluid assistant-knowledge-index">

    <h1 class="mb-4">📚 إدارة إجابات المساعد (حسب النية)</h1>

    {{-- KPI --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">إجمالي النوايا (Entries)</div>
                    <div class="kpi-value">{{ $stats->total_entries }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">إجمالي الإجابات</div>
                    <div class="kpi-value">{{ $stats->total_answers }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card kpi-card kpi-ok">
                <div class="card-body">
                    <div class="kpi-title">إجابات مفعّلة</div>
                    <div class="kpi-value">{{ $stats->active_answers }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card kpi-card kpi-warning">
                <div class="card-body">
                    <div class="kpi-title">نوايا بلا إجابة مرجعية</div>
                    <div class="kpi-value">{{ $stats->entries_without_primary }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Info --}}
    @if(request('filter'))
        <div class="alert alert-info mb-3 d-flex align-items-center gap-2">
            <span style="font-size:18px;">🔎</span>
            <span>
                يتم عرض النوايا حسب الفلتر:
                <strong class="text-dark">
                    @switch(request('filter'))
                        @case('no_primary') بدون إجابة مرجعية @break
                        @case('inactive') غير مفعّلة @break
                        @case('weak') ضعيفة @break
                        @default الكل
                    @endswitch
                </strong>
            </span>
        </div>
    @endif

    {{-- Warning --}}
    @if($entries->where('active_answers_count', 0)->count() > 0)
        <div class="alert alert-danger mb-3">
            ❗ توجد نوايا لا تحتوي على أي إجابة مفعّلة — هذه النوايا لن تعطي ردًا للمستخدم.
        </div>
    @endif

    {{-- TABLE --}}
    <table class="table table-hover table-bordered">
        <thead class="thead-light">
        <tr>
            <th style="min-width:180px;">التصنيف</th>
            <th style="min-width:160px;">النية (Entry)</th>
            <th style="min-width:160px;">الإجابات</th>
            <th style="min-width:140px;">الحالة</th>
            <th style="min-width:200px;">آخر تعديل فعلي</th>
            <th style="min-width:180px;">وضع الإجابة</th>
            <th style="min-width:160px;">إجراء</th>
        </tr>
        </thead>

        <tbody>
        @foreach($entries as $entry)
            <tr class="status-{{ $entry->status }}">
                <td><strong>{{ $entry->category }}</strong></td>

                <td>
                    <span class="badge badge-primary">
                        {{ $entry->tone }}
                    </span>
                </td>

                <td>
                    <span class="badge badge-success">
                        {{ $entry->active_answers_count }} مفعّلة
                    </span>
                    <span class="badge badge-secondary">
                        {{ $entry->inactive_answers_count }} غير مفعّلة
                    </span>
                </td>

                <td>
                    @if($entry->active_answers_count == 0)
                        <span class="badge badge-danger">❌ بدون إجابات مفعّلة</span>
                    @elseif($entry->answers_count < 2)
                        <span class="badge badge-warning">⚠️ إجابة واحدة فقط</span>
                    @else
                        <span class="badge badge-success">✔️ جاهز</span>
                    @endif
                </td>

                <td>
                    {{ $entry->last_update ?? '—' }}
                </td>

                <td>
                    @if($entry->has_primary)
                        <span class="badge badge-success">⭐ مرجعية</span>
                    @else
                        <span class="badge badge-secondary">🔁 عشوائي</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('admin.assistant.knowledge.edit', $entry->id) }}"
                       class="btn btn-primary btn-sm">
                        ✏️ تعديل الإجابات
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection

@section('css')
<style>
/* ===============================
   RTL SCOPE
   =============================== */
.assistant-knowledge-index {
    direction: rtl;
    background: #f4f6f9;
}

.assistant-knowledge-index h1,
.assistant-knowledge-index td,
.assistant-knowledge-index th {
    text-align: right;
}

/* ===============================
   REMOVE VOYAGER ACTIONS
   =============================== */
.voyager .bulk-actions,
.voyager .table-actions,
.voyager .actions {
    display: none !important;
}

/* ===============================
   TABLE
   =============================== */
.assistant-knowledge-index table th,
.assistant-knowledge-index table td {
    white-space: nowrap;
    vertical-align: middle;
}

/* ===============================
   KPI CARDS
   =============================== */
.kpi-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border-left: 5px solid #dee2e6;
}

.kpi-ok { border-left-color: #28a745; }
.kpi-warning { border-left-color: #dc3545; }

.kpi-title {
    font-size: 13px;
    color: #6c757d;
}

.kpi-value {
    font-size: 30px;
    font-weight: 700;
}

/* ===============================
   ROW STATUS
   =============================== */
.status-healthy { background: #f1fdf5; }
.status-weak { background: #fff8e6; }
.status-no_primary { background: #fff1f1; }
.status-inactive { background: #fbecec; }
.status-empty { background: #f1f3f5; }

/* ===============================
   BADGES
   =============================== */
.badge-success { background: #28a745; }
.badge-secondary { background: #6c757d; }
.badge-warning { background: #f0ad4e; color: #212529; }
.badge-danger { background: #dc3545; }
.badge-primary { background: #007bff; }

/* ===============================
   BUTTON
   =============================== */
.assistant-knowledge-index table .btn-primary {
    background: #0069d9;
    border-color: #0062cc;
    font-weight: 600;
}
</style>
@endsection
