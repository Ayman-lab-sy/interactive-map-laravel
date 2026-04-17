@extends('voyager::master')

@section('content')
<div class="page-content container-fluid assistant-audit-index">

    <h1 class="mb-4">📜 سجل تحويلات المعرفة</h1>

    @if(empty($logs))
        <div class="alert alert-secondary">
            لا توجد عمليات تحويل بعد.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الإجراء</th>
                    <th>التصنيف</th>
                    <th>الكلمات المفتاحية</th>
                    <th>إجابة</th>
                    <th>المسؤول</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                    @php
                        $payload = !empty($log['payload'])
                            ? json_decode($log['payload'], true)
                            : [];
                    @endphp
                    <tr class="
                        @if($log['action'] === 'convert') audit-convert
                        @elseif($log['action'] === 'ignore') audit-ignore
                        @endif
                    ">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($log['action'] === 'convert')
                                <span class="badge badge-convert">تحويل</span>
                            @elseif($log['action'] === 'ignore')
                                <span class="badge badge-ignore">تجاهل</span>
                            @else
                                <span class="badge badge-secondary">{{ $log['action'] ?? '-' }}</span>
                            @endif
                        </td>
                        <td>{{ $log['category'] ?? '-' }}</td>
                        <td>
                            @if($log['action'] === 'ignore' && !empty($log['payload']))
                                @php
                                    $payload = json_decode($log['payload'], true);
                                @endphp

                                <div class="text-muted small">
                                    <strong>السؤال:</strong><br>
                                    {{ $payload['question'] ?? '—' }}

                                    @if(!empty($payload['expanded']))
                                        <hr class="my-1">
                                        <strong>بعد التوسيع:</strong><br>
                                        {{ $payload['expanded'] }}
                                    @endif
                                </div>
                            @endif

                            @if(!empty($payload['keywords_added']))
                               <div class="mb-1">
                                   <strong class="text-success">➕ أضيف:</strong><br>
                                   @foreach($payload['keywords_added'] as $word)
                                       <span class="badge badge-success">{{ $word }}</span>
                                   @endforeach
                               </div>
                           @endif

                           @if(!empty($payload['keywords_removed']))
                               <div>
                                   <strong class="text-danger">➖ حُذف:</strong><br>
                                   @foreach($payload['keywords_removed'] as $word)
                                       <span class="badge badge-danger">{{ $word }}</span>
                                   @endforeach
                               </div>
                           @endif

                           @if(
                               empty($payload['keywords_added']) &&
                               empty($payload['keywords_removed'])
                           )
                               —
                           @endif

                        </td>
                        <td>
                            @if(!empty($payload['answer_added']))
                                <span class="badge badge-success">✔ تمت إضافة إجابة</span>
                            @else
                                —
                            @endif

                            @if(!empty($payload['source_question_id']))
                                <div class="text-muted small mt-1">
                                    🔗 مرتبط بالسؤال رقم #{{ $payload['source_question_id'] }}
                                </div>
                            @endif
                        </td>

                        <td>{{ $log['admin'] ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($log['created_at'])->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>

<style>
/* ===============================
   RTL SCOPE – AUDIT LOG
   =============================== */
.assistant-audit-index {
    direction: rtl;
    background: #f4f6f9;
}

.assistant-audit-index h1,
.assistant-audit-index th,
.assistant-audit-index td,
.assistant-audit-index p {
    text-align: right;
}

/* ===============================
   HEADER CARD
   =============================== */
.assistant-audit-index h1 {
    background: #ffffff;
    border-radius: 16px;
    padding: 18px 24px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
}

/* ===============================
   TABLE
   =============================== */
.assistant-audit-index table {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.assistant-audit-index table thead {
    background: #f1f3f5;
}

.assistant-audit-index table th {
    font-weight: 700;
    color: #343a40;
    white-space: nowrap;
}

.assistant-audit-index table td {
    vertical-align: middle;
    color: #495057;
    padding: 14px 12px;
}

/* ===============================
   BADGES (KEYWORDS)
   =============================== */
.assistant-audit-index .badge-info {
    background: #17a2b8;
    font-weight: 600;
    margin: 2px;
}

/* ===============================
   IGNORE PAYLOAD BOX
   =============================== */
.assistant-audit-index .text-muted {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px;
}

/* ===============================
   EMPTY STATE
   =============================== */
.assistant-audit-index .alert-secondary {
    background: #ffffff;
    border-radius: 14px;
    padding: 20px;
    color: #6c757d;
}

/* ===============================
   AUDIT ROW STATES
   =============================== */

/* تحويل معرفة */
.audit-convert {
    background: #f0fff4; /* أخضر فاتح */
}

/* تجاهل */
.audit-ignore {
    background: #f8f9fa; /* رمادي هادئ */
}

/* تحسين قراءة الصف عند hover */
.assistant-audit-index table tbody tr:hover {
    background: #eef2f7;
}

/* ===============================
   AUDIT ACTION BADGES
   =============================== */

.badge-convert {
    background: #28a745;
    color: #fff;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 10px;
}

.badge-ignore {
    background: #adb5bd;
    color: #212529;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 10px;
}

</style>

@endsection
