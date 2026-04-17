@extends('voyager::master')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')
<div class="page-content container-fluid assistant-unanswered-index">
    <div class="assistant-header-card mb-4">
        <h1>🧠 إدارة الأسئلة غير المفهومة</h1>

        <div class="assistant-filters">
            <a href="{{ route('admin.assistant.index', ['status' => 'all']) }}" class="filter-btn">
                📋 الكل
            </a>
            <a href="{{ route('admin.assistant.index', ['status' => 'new']) }}" class="filter-btn filter-new">
                🆕 جديد
            </a>
            <a href="{{ route('admin.assistant.index', ['status' => 'ignored']) }}" class="filter-btn filter-ignored">
                🚫 متجاهل
            </a>
        </div>
    </div>

    @if(empty($entries))
        <div class="alert alert-secondary">
            لا توجد أسئلة حاليًا.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>السؤال</th>
                    <th>بعد التوسيع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $i => $entry)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $entry['question'] ?? '-' }}</td>
                        <td>{{ $entry['expanded'] ?? '-' }}</td>
                        <td>{{ $entry['status'] ?? 'new' }}</td>
                        <td>
                            {{ isset($entry['created_at'])
                                ? \Carbon\Carbon::parse($entry['created_at'])->format('Y-m-d H:i')
                                : '-' }}
                        </td>
                        <td>
                            @if(($entry['status'] ?? 'new') === 'new')
                                <a href="{{ route('admin.assistant.convert', $entry['id']) }}"
                                   class="btn btn-sm btn-success">
                                    🔁 تحويل إلى معرفة
                                </a>

                                <form action="{{ route('admin.assistant.ignore') }}"
                                      method="POST"
                                      style="display:inline-block">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $entry['id'] }}">
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('تأكيد التجاهل؟')">
                                        🚫 تجاهل
                                    </button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<style>
/* ===============================
   RTL SCOPE – UNANSWERED INDEX
   =============================== */
.assistant-unanswered-index {
    direction: rtl;
    background: #f4f6f9;
}

/* العناوين والنصوص */
.assistant-unanswered-index h1,
.assistant-unanswered-index h2,
.assistant-unanswered-index p,
.assistant-unanswered-index th,
.assistant-unanswered-index td,
.assistant-unanswered-index label {
    text-align: right;
}

/* ===============================
   FILTER LINKS
   =============================== */
.assistant-unanswered-index .mb-3 a {
    font-weight: 600;
    color: #007bff;
    margin-left: 10px;
}

.assistant-unanswered-index .mb-3 a:hover {
    text-decoration: underline;
}

/* ===============================
   TABLE
   =============================== */
.assistant-unanswered-index table {
    background: #ffffff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.assistant-unanswered-index table thead {
    background: #f1f3f5;
}

.assistant-unanswered-index table th {
    font-weight: 700;
    color: #343a40;
    white-space: nowrap;
}

.assistant-unanswered-index table td {
    vertical-align: middle;
    color: #495057;
}

/* ===============================
   STATUS COLORS
   =============================== */
.assistant-unanswered-index td:nth-child(4) {
    font-weight: 700;
}

.assistant-unanswered-index td:nth-child(4):contains("new") {
    color: #28a745;
}

.assistant-unanswered-index td:nth-child(4):contains("ignored") {
    color: #dc3545;
}

/* ===============================
   BUTTONS
   =============================== */
.assistant-unanswered-index .btn-success {
    background: #28a745;
    border-color: #28a745;
    font-weight: 700;
    border-radius: 10px;
}

.assistant-unanswered-index .btn-danger {
    background: #dc3545;
    border-color: #dc3545;
    font-weight: 700;
    border-radius: 10px;
}

/* ===============================
   EMPTY STATE
   =============================== */
.assistant-unanswered-index .alert-secondary {
    background: #ffffff;
    border-radius: 12px;
    color: #6c757d;
}
</style>
<style>
/* ===============================
   HEADER CARD
   =============================== */
.assistant-unanswered-index .assistant-header-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.assistant-unanswered-index .assistant-header-card h1 {
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    color: #212529;
}

/* ===============================
   FILTER BUTTONS
   =============================== */
.assistant-unanswered-index .assistant-filters {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.assistant-unanswered-index .filter-btn {
    background: #f1f3f5;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 700;
    color: #495057;
    text-decoration: none;
    border: 2px solid transparent;
    transition: all .2s ease;
}

.assistant-unanswered-index .filter-btn:hover {
    background: #e9ecef;
    border-color: #ced4da;
}

.assistant-unanswered-index .filter-new {
    color: #28a745;
}

.assistant-unanswered-index .filter-ignored {
    color: #dc3545;
}

/* ===============================
   TABLE ENHANCEMENTS
   =============================== */
.assistant-unanswered-index table {
    margin-top: 20px;
}

.assistant-unanswered-index table tbody tr {
    transition: background .15s ease;
}

.assistant-unanswered-index table tbody tr:hover {
    background: #f8f9fa;
}

.assistant-unanswered-index table td,
.assistant-unanswered-index table th {
    padding: 14px 12px;
}

/* ===============================
   ACTION BUTTONS
   =============================== */
.assistant-unanswered-index .btn {
    padding: 6px 14px;
}

/* ===============================
   EMPTY STATE
   =============================== */
.assistant-unanswered-index .alert-secondary {
    padding: 20px;
    font-size: 15px;
}
</style>


@endsection
