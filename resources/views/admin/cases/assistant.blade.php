@extends('admin.layouts.app')

@section('title', 'مساعد الإحالات')

@section('content')

<div class="case-layout">

    <div class="card">
        <div class="section-title">
            🧭 مساعد الإحالات
            <span class="subtitle">
                تحديد الإحالات المناسبة للحالة
            </span>
        </div>

        <div class="info-box info-info">
            <span class="info-icon">ℹ</span>
            اختر التصنيف القانوني الداخلي للحالة، ثم شغّل المساعد لتحليل الإحالات.
        </div>

        {{-- معلومات سريعة عن الحالة --}}
        <div class="case-info-grid" style="margin-top:16px;">
            <div class="info-card">
                <span class="info-label">رقم الحالة</span>
                <span class="info-value">{{ $case->case_number }}</span>
            </div>

            <div class="info-card">
                <span class="info-label">حالة الملف</span>
                <span class="status-badge" data-case-status="{{ $case->status }}">
                    {{ match($case->status) {
                        'new'          => 'جديدة',
                        'under_review' => 'قيد المراجعة',
                        'documented'   => 'موثّقة',
                        'archived'     => 'مؤرشفة',
                        default        => $case->status
                   } }}
                </span>
            </div>
        </div>

        <div class="card">
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
                    <span class="info-value">
                       {{ $case->full_name ?? '—' }}
                   </span>
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
                <span class="info-value">
                    {{ $case->spouse_name ?? '—' }}
                </span>
            </div>
  
            {{-- الأولاد --}}
            <div class="info-card">
                <span class="info-label">الأولاد</span>
                <span class="info-value">
                    @if(!empty($case->children))
                        @php
                            $children = json_decode($case->children, true);
                                @endphp

                        {{ collect($children)
                            ->map(fn($c) =>
                                ($c['name'] ?? '—') . ' (' . ($c['age'] ?? '?') . ')'
                            )
                            ->implode('، ')
                        }}
                    @else
                        —
                    @endif
                </span>
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

        </div>

        {{-- نموذج المساعد --}}
        <form method="POST"
              action="{{ route('admin.case_assistant.analyze', $case->id) }}"
              style="margin-top:24px;">
            @csrf

            <div class="form-group">
                <label class="form-label">
                    التصنيف القانوني للحالة
                </label>

                <select name="legal_violation_type"
                        class="form-control"
                        required>
                    <option value="">— اختر التصنيف —</option>

                    <option value="torture"
                        @selected(optional($decision)->legal_violation_type === 'torture')>
                        التعذيب أو المعاملة القاسية
                    </option>

                    <option value="arbitrary_detention"
                        @selected(optional($decision)->legal_violation_type === 'arbitrary_detention')>
                        الاعتقال أو الاحتجاز التعسفي
                    </option>

                    <option value="enforced_disappearance"
                        @selected(optional($decision)->legal_violation_type === 'enforced_disappearance')>
                        الاختفاء القسري
                    </option>

                    <option value="human_rights_defenders"
                        @selected(optional($decision)->legal_violation_type === 'human_rights_defenders')>
                        استهداف مدافعين عن حقوق الإنسان
                    </option>

                    <option value="minority_issues"
                        @selected(optional($decision)->legal_violation_type === 'minority_issues')>
                        انتهاكات بحق أقلية
                    </option>

                    <option value="refugee_protection"
                        @selected(optional($decision)->legal_violation_type === 'refugee_protection')>
                        نزوح / لجوء / حماية دولية
                    </option>
                </select>
            </div>

            <div class="info-box info-pin">
                <span class="info-icon">●</span>
                التصنيف هنا هو تصنيف قانوني داخلي لأغراض الإحالة الدولية،
                وقد يختلف عن توصيف الضحية للانتهاك.
            </div>

            <div class="info-box info-pin">
                <span class="info-icon">●</span>
                 انتهاكات الممتلكات تُستخدم كعنصر سياقي داعم،
                ولا تُنشئ إحالة دولية مستقلة إلا إذا ارتبطت بانتهاك جسيم آخر.
            </div>


            <div class="card decision-card" style="margin-top:24px;">
                <div class="section-title">
                     ⚖️ عوامل مؤثرة على القرار
                    <span class="subtitle">
                        تقدير قانوني داخلي لتحسين دقة الإحالات
                    </span>
                </div>

                <div class="decision-factors-grid">

                    @php
                        $inputs = optional($decision)->decision_inputs ?? [];
                    @endphp

                    @foreach([
                        'urgent_risk' => 'خطر وشيك على الحياة أو السلامة',
                        'victim_deceased' => 'الضحية متوفاة',
                        'state_actor' => 'تورّط جهة دولة أو جهاز رسمي',
                        'minority' => 'استهداف أقلية',
                        'pattern_case' => 'حالة نمطية أو متكررة',
                        'human_rights_defender' => 'الضحية مدافع/ة عن حقوق الإنسان',
                        'multiple_violations' => 'تعدد الانتهاكات'
                    ] as $key => $label)
                        <label class="toggle-row">
                            <span class="toggle-label">{{ $label }}</span>
                            <input type="checkbox"
                                   name="decision_inputs[{{ $key }}]"
                                   value="1"
                                   @checked(data_get($inputs, $key))>
                            <span class="toggle-ui"></span>
                        </label>
                    @endforeach

                </div>
            </div>

            <div class="action-stack" style="margin-top:24px;">
                @if(empty($decision?->executed_at))
                    <button type="submit" class="btn-action btn-primary">
                        🔍 تحليل الحالة وتشغيل المساعد
                    </button>
                @else
                    <div class="info-box info-info">
                        <span class="info-icon">ℹ</span>
                        تم تنفيذ الإحالات بناءً على هذا التحليل.
                        لا يمكن إعادة التحليل بعد التنفيذ.
                    </div>
                @endif

                <a href="{{ url()->previous() }}"
                   class="btn-action btn-secondary">
                    العودة إلى الحالة
                </a>
            </div>
        </form>

        @if($decision)
            <div class="card" style="margin-top:24px;">

                <div class="section-title">
                  🕒 سجل قرار المساعد
                    <span class="subtitle">
                     معلومات تدقيقية داخلية
                    </span>
                </div>

                <div class="case-info-grid">

                    {{-- آخر تحليل --}}
                    @if($decision->decided_at)
                        <div class="info-card">
                            <span class="info-label">آخر تحليل</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($decision->decided_at)->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">بواسطة</span>
                            <span class="info-value">
                                {{ optional(\App\Models\User::find($decision->decided_by))->name ?? '—' }}
                            </span>
                        </div>
                    @endif

                    {{-- تنفيذ الإحالات --}}
                    @if($decision->executed_at)
                        <div class="info-card">
                            <span class="info-label">تنفيذ الإحالات</span>
                            <span class="info-value emphasis">
                                {{ \Carbon\Carbon::parse($decision->executed_at)->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">نُفّذت بواسطة</span>
                            <span class="info-value">
                                {{ optional(\App\Models\User::find($decision->executed_by))->name ?? '—' }}
                            </span>
                        </div>
                    @endif

                </div>
            </div>
        @endif

        @php
            $result = session('assistant_result')
                ?? optional($decision)->decision_payload;
        @endphp

        @if(!empty($result))
            <div class="card decision-result-card">

                <div class="section-title">
                 📊 نتيجة تحليل المساعد
                    <span class="subtitle">
                     الجهات المقترحة للإحالة
                    </span>
                </div>

                {{-- ملخص --}}
                <div class="decision-summary">
                    <div class="summary-icon">📊</div>
                    <div class="summary-text">
                        <div class="summary-title">تم تحليل الحالة بنجاح</div>
                        <div class="summary-sub">
                            عدد الإحالات المقترحة:
                            <strong>{{ $result['total'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>

                {{-- الإحالات الإلزامية --}}
                @if(!empty($result['mandatory']))
                    <div class="decision-group mandatory">
                        <h4>🔴 إحالات إلزامية</h4>
                        <div class="decision-items">
                            @foreach($result['mandatory'] as $entity)
                                <div class="decision-item">
                                    {{ config('referral.entities.' . $entity) ?? $entity }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- الإحالات الداعمة --}}
                @if(!empty($result['supporting']))
                    <div class="decision-group supporting">
                        <h4> 🔵 إحالات داعمة</h4>
                        <div class="decision-items">
                            @foreach($result['supporting'] as $entity)
                                <div class="decision-item">
                                    {{ config('referral.entities.' . $entity) ?? $entity }}
                                </div>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- أزرار الإجراء --}}
                <div class="action-stack" style="margin-top:24px;">
                    @if(empty($decision?->executed_at))
                        <form method="POST" action="{{ route('admin.case_assistant.execute', $case->id) }}">
                            @csrf
                            <button type="submit" class="btn-action btn-success full-width">
                                &lrm; البدء بتنفيذ الإحالات
                            </button>
                        </form>
                    @else
                        <div class="info-box info-success">
                            <span class="info-icon">✓</span>
                            تم تنفيذ الإحالات لهذه الحالة مسبقًا.
                            يمكنك الآن متابعة العمل على الإحالات.
                        </div>
                    @endif

                    <a href="{{ route('admin.cases.show', $case->id) }}"
                    class="btn-action btn-secondary">
                     العودة إلى الحالة
                    </a>
                </div>

            </div>
        @endif

    </div>

</div>

@endsection
