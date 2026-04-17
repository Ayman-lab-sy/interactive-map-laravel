@extends('admin.layouts.app')

@section('title', 'تفاصيل الحالة')

@section('content')


<div class="case-layout">

  <div class="case-main">
      <div class="page-title" style="font-size:20px; font-weight:600; margin-bottom:20px;">
          تفاصيل الحالة – {{ $case->case_number }}
      </div>

      {{-- معلومات أساسية --}}
      <div class="case-summary">

          {{-- Status Header --}}
          <div class="case-status-bar" data-case-status="{{ $case->status }}">
              <span class="case-status-label">
                  {{ match($case->status) {
                      'new' => 'جديدة',
                      'under_review' => 'قيد المراجعة',
                      'documented' => 'موثّقة',
                      'archived' => 'مؤرشفة',
                      default => $case->status
                  } }}
              </span>
              <span class="case-number">{{ $case->case_number }}</span>
          </div>

          {{-- Info Cards --}}
          <div class="case-info-grid">

              <div class="info-card">
                  <span class="info-label">تاريخ الإبلاغ</span>
                  <span class="info-value">
                      {{ optional($case->created_at)->toDateString() }}
                  </span>
              </div>

              {{-- الاسم الكامل --}}
              <div class="info-card">
                  <span class="info-label">الاسم الكامل</span>

                  @if($case->full_name)
                      <div style="display:flex; align-items:center; gap:8px;">
                          <span id="field-full_name"
                                data-masked="{{ mb_substr($case->full_name, 0, 1) . '••••••' }}"
                                data-revealed="false">
                              {{ mb_substr($case->full_name, 0, 1) . '••••••' }}
                          </span>

                          @auth
                            @if(auth()->user()->canRevealSensitiveFields())
                                <button type="button"
                                        class="copy-btn"
                                        onclick="toggleField({{ $case->id }}, 'full_name', 'field-full_name', this)">
                                    إظهار
                                </button>
                            @endif
                          @endauth
                      </div>
                  @else
                      —
                  @endif
              </div>

              {{-- نوع الاسم --}}
              <div class="info-card">
                  <span class="info-label">نوع الاسم</span>
                  <span class="info-value">
                     {{ $case->name_type === 'alias' ? 'اسم مستعار' : 'اسم حقيقي' }}
                  </span>
             </div>

              <div class="info-card">
                  <span class="info-label">مكان الإقامة</span>
                  <span class="info-value">{{ $case->location ?? '—' }}</span>
              </div>

              <div class="info-card">
                  <span class="info-label">حالة نمطية</span>
                  <span class="info-value">
                      {{ $case->is_pattern_case ? 'نعم' : 'لا' }}
                  </span>
              </div>

          </div>

          <div class="case-info-grid">

              {{-- الطائفة / المكوّن --}}
              <div class="info-card">
                  <span class="info-label">الطائفة / المكوّن</span>
                  <span class="info-value">
                      {{ config('referral.components.' . $case->component) ?? '—' }}
                  </span>
             </div>

              {{-- نوع الانتهاك --}}
              <div class="info-card">
                  <span class="info-label">نوع الانتهاك</span>
                  <span class="info-value">
                      {{ config('referral.violation_types.' . $case->violation_type) ?? '—' }}
                  </span>
              </div>

              {{-- مكان الواقعة --}}
              <div class="info-card">
                  <span class="info-label">مكان الواقعة</span>
                  <span class="info-value">
                      {{ $case->threat_locations ?? '—' }}
                  </span>
              </div>

              {{-- تاريخ الواقعة --}}
              <div class="info-card">
                  <span class="info-label">تاريخ الواقعة</span>
                  <span class="info-value">
                      {{ $case->threat_date ?? '—' }}
                  </span>
              </div>

              {{-- حساسية الحالة --}}
              <div class="info-card">
                  <span class="info-label">حساسية الحالة</span>
                  <span class="info-value emphasis">
                      {{ strtoupper($case->case_sensitivity ?? '—') }}
                  </span>
              </div>
          </div> 
      </div>

      <div class="card" style="margin-top:20px;">
          <div class="section-title">
              معلومات التواصل
          </div>

          <div class="info-box info-pin" style="color: #a09035;">
            <span class="info-icon">●</span>
               عند الحاجة يجب أن يتم التواصل مع صاحب الحالة حصراً عبر البريد الرسمي المخصص لحالات التوثيق.
              يمنع استخدام اي بريد الكتروني آخر.
          </div>

          <div class="case-info-grid">

              <div class="info-card">
                  <span class="info-label">بريد صاحب الحالة</span>

                  @if($case->email)

                      @php
                          $maskedEmail = preg_replace('/(^.).*(@.*$)/', '$1••••$2', $case->email);
                      @endphp

                      <div style="display:flex; align-items:center; gap:8px;">

                          <span id="field-email"
                                data-masked="{{ $maskedEmail }}"
                                data-revealed="false">
                              {{ $maskedEmail }}
                          </span>

                          <button type="button"
                                  class="copy-btn"
                                  onclick="toggleField({{ $case->id }}, 'email', 'field-email', this)">
                              إظهار
                          </button>

                          <button type="button"
                                  class="copy-btn"
                    onclick="copyField('field-email')">
                              نسخ
                          </button>

                      </div>

                  @else
                      —
                  @endif
              </div>

              <div class="info-card">
                  <span class="info-label">البريد الرسمي للتوثيق</span>
                  <span class="info-value">
                      testimony@thealawites.com
                  </span>
              </div>

          </div>
      </div>

     
      {{-- بيانات العائلة --}}
      <div class="card">

          <div class="section-title">
              بيانات العائلة
              <span class="subtitle">
                  Family Information (Reference Only)
              </span>
          </div>

          {{-- الزوج / الزوجة --}}
          <div class="info-card">
              <span class="info-label">الزوج / الزوجة</span>

              @if($case->spouse_name)

                  @php
                      $maskedSpouse = mb_substr($case->spouse_name, 0, 1) . '••••••';
                  @endphp

                  <div style="display:flex; align-items:center; gap:8px;">

                      <span id="field-spouse_name"
                            data-masked="{{ $maskedSpouse }}"
                            data-revealed="false">
                          {{ $maskedSpouse }}
                      </span>

                      <button type="button"
                              class="copy-btn"
                              onclick="toggleField({{ $case->id }}, 'spouse_name', 'field-spouse_name', this)">
                          إظهار
                      </button>

                  </div>

              @else
                  —
              @endif
          </div>
  
          {{-- الأولاد --}}
          <div class="info-card">
              <span class="info-label">الأولاد</span>

              @if(!empty($case->children))

                  @php
                      $maskedChildren = '••••••';
                  @endphp

                  <div style="display:flex; align-items:center; gap:8px;">

                      <span id="field-children"
                            data-masked="{{ $maskedChildren }}"
                            data-revealed="false">
                          {{ $maskedChildren }}
                      </span>

                      <button type="button"
                              class="copy-btn"
                              onclick="toggleField({{ $case->id }}, 'children', 'field-children', this)">
                          إظهار
                      </button>

                  </div>

              @else
                  —
              @endif
          </div>
      </div>

      <div class="card">
          <div class="section-title"><strong>الوصف التفصيلي</strong></div>

          <p class="case-description">
              {{ $case->threat_description ?? 'لا يوجد وصف.' }}
          </p>

          <div class="case-impact">
              <strong>الأثر النفسي / الاجتماعي</strong>
              <p>{{ $case->impact_details ?? 'غير متوفر.' }}</p>
          </div>
      </div>

      {{-- الأدلة --}}
      <div class="card">
          <div class="section-title">الأدلة والمرفقات</div>

          @if($files->count())
              <div class="table-card">
                  <table class="table">
                      <thead>
                          <tr>
                              <th>عدد الأدلة</th>
                              <th>اسم الملف</th>
                              <th>النوع</th>
                              <th>تاريخ الإضافة</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach($files->whereNull('update_id') as $i => $file)
                              <tr onclick="window.open('{{ route('admin.files.view', $file->id) }}', '_blank')"
                                  class="referral-row">
                                  <td class="referral-main-cell">{{ $i + 1 }}</td>
                                  <td class="referral-main-cell" style="text-align:right; display:flex; align-items:center; gap:10px;">

                                      @php
                                          $isImage = str_contains($file->mime_type, 'image');
                                          $type = $isImage ? '🖼' :
                                                  (str_contains($file->mime_type, 'video') ? '🎥' :
                                                  (str_contains($file->mime_type, 'pdf') ? '📄' : '📎'));
                                      @endphp

                                      {{-- صورة مصغرة --}}
                                      @if($isImage)
                                          <img src="{{ route('admin.files.view', $file->id) }}"
                                               style="width:40px; height:40px; object-fit:cover; border-radius:6px;">
                                      @else
                                          <span style="font-size:18px;">{{ $type }}</span>
                                      @endif

                                      {{-- اسم الملف --}}
                                      <div>
                                          <div style="font-weight:600;">
                                              {{ $file->original_name }}
                                          </div>
                                          <div style="font-size:11px; color:#64748b;">
                                              {{ $file->mime_type }}
                                          </div>
                                      </div>

                                  </td>
                                  <td>{{ $file->mime_type }}</td>
                                  <td>{{ optional(\Carbon\Carbon::parse($file->created_at))->toDateString() }}</td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
          @else
              <p style="color:#94a3b8;">لا توجد مرفقات.</p>
          @endif
      </div>

      <div class="card" style="margin-top:20px;">
          <div class="section-title">تحديثات واردة من صاحب الحالة</div>

          @if($updates->count())

              @foreach($updates as $update)
                  <div style="padding:12px; border-bottom:1px solid #e5e7eb;">

                      <div style="font-size:13px; color:#64748b;">
                          {{ \Carbon\Carbon::parse($update->created_at)->format('Y-m-d H:i') }}
                      </div>

                      <div style="margin-top:6px; color:#334155;">
                          {{ $update->update_description }}
                      </div>

                      {{-- ملفات مرتبطة بهذا التحديث --}}
                      @php
                          $updateFiles = $files->where('update_id', $update->id);
                      @endphp

                      @if($updateFiles->count())
                          <div style="margin-top:10px; padding-right:10px;">

                              <strong style="font-size:13px;">المرفقات:</strong>

                              <div style="
                                  display:grid;
                                  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                                  gap:12px;
                                  margin-top:10px;
                              ">

                                  @foreach($updateFiles as $file)

                                      @php
                                          $isImage = str_contains($file->mime_type, 'image');
                                      @endphp

                                      <div style="
                                          border:1px solid #e5e7eb;
                                          border-radius:10px;
                                          padding:8px;
                                          background:#f9fafb;
                                          transition:0.2s;
                                      "
                                      onmouseover="this.style.background='#f1f5f9'"
                                      onmouseout="this.style.background='#f9fafb'">

                                          <a href="{{ route('admin.files.view', $file->id) }}" target="_blank" style="text-decoration:none; color:inherit;">

                                              @if($isImage)
                                                  <img src="{{ route('admin.files.view', $file->id) }}"
                                                       style="width:100%; height:120px; object-fit:cover; border-radius:6px;">
                                              @else
                                                  <div style="
                                                      height:120px;
                                                      display:flex;
                                                      align-items:center;
                                                      justify-content:center;
                                                      font-size:30px;
                                                  ">
                                                      📄
                                                  </div>
                                              @endif

                                              <div style="
                                                  font-size:12px;
                                                  margin-top:6px;
                                                  color:#334155;
                                                  word-break:break-word;
                                              ">
                                                  {{ \Illuminate\Support\Str::limit($file->original_name, 40) }}
                                              </div>

                                          </a>

                                      </div>

                                  @endforeach

                              </div>

                          </div>
                      @endif

                  </div>
              @endforeach

          @else
              <p style="color:#94a3b8;">لا توجد تحديثات.</p>
          @endif
      </div>
  </div>

  <div class="card">

      <div class="section-title">
          إجراءات الحالة
      </div>

      {{-- حالة جديدة --}}
      @if($case->status === 'new')

          <div class="info-box info-info">
              <span class="info-icon">ℹ</span>
              هذه الحالة جديدة ولم يتم البدء بمراجعتها بعد.
          </div>

          <form method="POST" action="{{ route('cases.under-review', $case->id) }}">
              @csrf
              <button class="btn-action btn-primary full-width">
                  نقل إلى قيد المراجعة
              </button>
          </form>

      {{-- قيد المراجعة --}}
      @elseif($case->status === 'under_review')
 
          <div class="info-box info-info">
              <span class="info-icon">ℹ</span>
             الحالة قيد المراجعة. يجب اتخاذ قرار نهائي بالتوثيق أو الأرشفة.
          </div>

          <div class="action-stack">

              <button type="button"
                        class="btn-action btn-success full-width"
                        onclick="openDocumentModal()">
                       تثبيت التوثيق
              </button>

              <!-- زر الأرشفة -->
              @can('archive', $case)
              <button type="button"
                      class="btn-action btn-secondary"
                      onclick="openArchiveModal()">
                       أرشفة الحالة
              </button>
              @endcan
          </div>

          {{-- متابعة المراجعة --}}
          <div class="card" style="margin-top:20px; border:1px dashed #e5e7eb;">
              <div class="section-title">
                  متابعة المراجعة
              </div>

              <div class="info-box info-info">
                   <span class="info-icon">📝</span>
                  يمكنك إضافة ملاحظات متابعة أثناء انتظار بيانات أو رد من صاحب الحالة.
              </div>

              <button type="button"
                      class="btn-action btn-primary"
                      onclick="openNoteModal()">
                  + إضافة ملاحظة متابعة
              </button>
          </div>



      {{-- موثّقة --}}
      @elseif($case->status === 'documented')

          <div class="info-box info-success">
              <span class="info-icon">✓</span>
              هذه الحالة موثّقة وجاهزة لإنشاء إحالات رسمية.
          </div>

          <div class="action-stack">

              {{-- زر مساعد الإحالات --}}
              <a href="{{ route('admin.case_assistant.show', $case->id) }}"
                class="btn-action btn-warning">
                🧭 تشغيل مساعد الإحالات
              </a>

              {{-- زر إنشاء إحالة يدوي --}}
              <a href="{{ route('admin.referrals.create', $case->id) }}"
                 class="btn-action btn-primary">
                  + إنشاء إحالة
              </a>
          </div>

      {{-- مؤرشفة --}}
      @elseif($case->status === 'archived')

          <div class="info-box muted">
              <span class="info-icon">🔒</span>
              هذه الحالة مؤرشفة ولا يمكن إجراء أي تعديل عليها.
          </div>

      @endif
  </div>


  {{-- Timeline --}}
  @if($case->events->count())
  <div class="card" style="margin-top:32px;">
      <div class="section-title">
          السجل الإداري للحالة
      </div>

      @foreach($case->events as $event)
          <div style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
            
              <div style="font-size:13px; color:#64748b;">
                  {{ $event->created_at->format('Y-m-d H:i') }} –
                  {{ $event->user->name ?? 'System' }}
              </div>
  
              <div style="margin-top:6px;">
                  @if($event->event_type === 'note_added')
                      <span style="
                          display:inline-block;
                          padding:4px 10px;
                          background:#dbeafe;
                          color:#1d4ed8;
                          border-radius:6px;
                          font-size:13px;
                          font-weight:600;
                      ">
                          📝 ملاحظة متابعة
                      </span>
                  @elseif($event->status_after)
                      <span class="status-badge"
                            data-case-status="{{ $event->status_after }}">
                          {{ match($event->status_after) {
                              'new' => 'جديدة',
                              'under_review' => 'قيد المراجعة',
                              'documented' => 'موثّقة',
                              'archived' => 'مؤرشفة',
                              default => $event->status_after
                          } }}
                     </span>
                  @endif

              </div>
              @if($event->status_before && $event->status_after && $event->status_before !== $event->status_after)
                  <div style="font-size:12px; color:#64748b; margin-top:6px;">
                      {{ match($event->status_before) {
                          'new' => 'جديدة',
                          'under_review' => 'قيد المراجعة',
                          'documented' => 'موثّقة',
                          'archived' => 'مؤرشفة',
                          default => $event->status_before
                      } }}
                      ⬅
                      {{ match($event->status_after) {
                          'new' => 'جديدة',
                          'under_review' => 'قيد المراجعة',
                          'documented' => 'موثّقة',
                          'archived' => 'مؤرشفة',
                          default => $event->status_after
                      } }}
                  </div>
              @endif
  
              @if($event->description)
                  <div style="margin-top:6px; color:#334155;">
                      {{ $event->description }}
                  </div>
              @endif
  
          </div>
      @endforeach
  </div>
  @endif


    {{-- الإحالات --}}
    <div class="card" style="margin-top:32px;">
        <h3>الإحالات (Referrals)</h3>

        @if($referrals->count())
        <div class="table-card">                  
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:7%; text-align:center;">رقم الإحالة</th>
                        <th style="width:20%; text-align:center;">المسار</th>
                        <th style="width:40%; text-align:center;">الجهة</th>
                        <th style="width:20%; text-align:center;">وضع الإحالة</th>
                        <th style="width:13%; text-align:center;">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referrals as $ref)
                          
                        @php
                           $url = \App\Helpers\ReferralRouteHelper::resolve($ref);
                        @endphp

                        <tr
                            onclick="window.location='{{ $url }}'"
                            class="referral-row"
                            data-status="{{ $ref->referral_status }}"
                        >
                            <td class="referral-main-cell">{{ $ref->id }}</td>
                            <td class="referral-main-cell">
                                <span class="track-badge" data-track="{{ $ref->referral_track }}">
                                    {{ match($ref->referral_track) {
                                        'SPECIAL_PROCEDURES'      => 'الإجراءات الخاصة',
                                        'HUMANITARIAN_PROTECTION' => 'الحماية الإنسانية',
                                        'NGO_LEGAL'               => 'المسار القانوني',
                                        'UN_ACCOUNTABILITY'       => 'المساءلة الأممية',
                                        default => $ref->referral_track
                                    } }}
                                </span>
                            </td>
                            <td>{{ $ref->entity_name ?? '—' }}</td>
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
                            <td>{{ optional(\Carbon\Carbon::parse($ref->created_at))->toDateString() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="margin-top:10px;">لا توجد إحالات.</p>
        @endif
        </div>
    </div>  

    @if($securityLogs->count())
  <div class="card" style="margin-top:32px;">
      <div class="section-title">
          سجل الوصول إلى البيانات الحساسة
      </div>

      @foreach($securityLogs as $log)
          {{ $log->action_context['field'] ?? 'غير معروف' }}

          <div style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
              <div style="font-size:13px; color:#64748b;">
                  {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}
                  – المستخدم {{ $log->user->name ?? 'System' }}
              </div>

              <div style="margin-top:6px;">
                  🔐 تم كشف الحقل:
                  <strong>{{ $log->action_context['field'] ?? 'غير معروف' }}</strong>
              </div>
          </div>
      @endforeach
  </div>
  @endif
</div>

<!-- Document Modal -->
<div id="documentModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3 style="margin-bottom:15px;">تأكيد توثيق الحالة</h3>

        <form method="POST" action="{{ route('cases.documented', $case->id) }}">
            @csrf

            <label style="font-weight:600;">مبرر قرار التوثيق (إلزامي)</label>
            <textarea name="decision_note"
                      required
                      rows="4"
                      class="form-control"
                      placeholder="اكتب سبب اعتماد الحالة كحالة موثّقة..."></textarea>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action btn-success">
                    تأكيد التوثيق
                </button>

                <button type="button"
                        class="btn-action btn-secondary"
                        onclick="closeDocumentModal()">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDocumentModal() {
    document.getElementById('documentModal').style.display = 'flex';
}

function closeDocumentModal() {
    document.getElementById('documentModal').style.display = 'none';
}
</script>

<!-- Archive Modal -->
<div id="archiveModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3 style="margin-bottom:15px;">تأكيد أرشفة الحالة</h3>

        <form method="POST" action="{{ route('cases.archive', $case->id) }}">
            @csrf

            <label style="font-weight:600;">سبب الأرشفة (إلزامي)</label>
            <textarea name="archive_reason"
                      required
                      rows="4"
                      class="form-control"
                      placeholder="اكتب سبب عدم اعتماد الحالة للتوثيق..."></textarea>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action btn-secondary">
                    تأكيد الأرشفة
                </button>

                <button type="button"
                        class="btn-action btn-success"
                        onclick="closeArchiveModal()">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openArchiveModal() {
    document.getElementById('archiveModal').style.display = 'flex';
}

function closeArchiveModal() {
    document.getElementById('archiveModal').style.display = 'none';
}
</script>

<!-- Note Modal -->
<div id="noteModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3 style="margin-bottom:15px;">إضافة ملاحظة متابعة</h3>

        <form method="POST" action="{{ route('cases.add-note', $case->id) }}">
            @csrf

            <label style="font-weight:600;">نص الملاحظة (إلزامي)</label>
            <textarea name="note_text"
                      required
                      rows="4"
                      class="form-control"
                      placeholder="مثال: تم التواصل مع صاحب الحالة وننتظر الرد..."></textarea>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action btn-primary">
                    حفظ الملاحظة
                </button>

                <button type="button"
                        class="btn-action btn-secondary"
                        onclick="closeNoteModal()">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNoteModal() {
    document.getElementById('noteModal').style.display = 'flex';
}

function closeNoteModal() {
    document.getElementById('noteModal').style.display = 'none';
}
</script>

<script>
function copyEmail() {
    const emailText = document.getElementById("case-email").innerText;
    navigator.clipboard.writeText(emailText).then(function() {
        const feedback = document.getElementById("copy-feedback");
        feedback.style.display = "inline";
        setTimeout(() => {
            feedback.style.display = "none";
        }, 2000);
    });
}
</script>

<script>
function toggleField(caseId, field, elementId, buttonElement) {

    const span = document.getElementById(elementId);

    if(span.dataset.revealed === "true") {
        span.innerText = span.dataset.masked;
        span.dataset.revealed = "false";
        buttonElement.innerText = "إظهار";
        return;
    }

    if(!confirm("⚠ سيتم تسجيل عملية الاطلاع على هذا الحقل ضمن سجل التدقيق.\nهل تريد المتابعة؟")) {
        return;
    }

    fetch(`/admin/cases/${caseId}/reveal-field`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ field: field })
    })
    .then(res => res.json())
    .then(data => {

        if(!data.success) {
            alert("حدث خطأ أثناء الكشف.");
            return;
        }

        if(!data.value) {
            alert("لا توجد قيمة لهذا الحقل.");
            return;
        }

        span.innerText = data.value;
        span.dataset.revealed = "true";
        buttonElement.innerText = "إخفاء";
    })
    .catch(err => {
        console.error(err);
        alert("خطأ في الاتصال.");
    });
}
</script>

<script>
function copyField(elementId) {

    const span = document.getElementById(elementId);

    if(span.dataset.revealed !== "true") {
        alert("يجب إظهار الحقل أولاً قبل نسخه.");
        return;
    }

    navigator.clipboard.writeText(span.innerText);
    alert("تم النسخ.");
}
</script>

@endsection
