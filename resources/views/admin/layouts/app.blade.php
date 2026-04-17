<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --primary: #1f3a5f;
            --secondary: #f1f5f9;
            --accent: #0d9488;
            --danger: #b91c1c;
            --text-dark: #0f172a;
            --border: #e2e8f0;
            --bg: #ffffff;
        }

        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }

        body {
            margin: 0;
            background: var(--secondary);
            color: var(--text-dark);
        }

        /* ===== Header ===== */
        .header {
            background: var(--primary);
            color: #fff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        /* ===== Layout ===== */
        .container {
            display: flex;
            min-height: calc(100vh - 64px);
        }

        /* ===== Sidebar ===== */
        .sidebar {
            background: #12295e;
            color: #e5e7eb;
            width: 240px;
            min-height: 100vh;
            transition: width 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar--collapsed {
            width: 70px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px;
            border-bottom: 1px solid #1e293b;
        }

        .sidebar-title {
            font-size: 14px;
            font-weight: 600;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #e5e7eb;
            font-size: 18px;
            cursor: pointer;
        }

        .sidebar-nav {
            padding: 10px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            outline: none;
            color: #cbd5f5;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
        }

        .sidebar-link:hover {
            background: #478fd3;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: inset 4px 0 0 #93c5fd;
        }

        .sidebar-link.active .icon {
            color: #ffffff;
        }

        .sidebar-link .icon {
            font-size: 18px;
            min-width: 24px;
            text-align: center;
        }

        .sidebar--collapsed .label {
            display: none;
        }

        .sidebar--collapsed .sidebar-title {
            display: none;
        }

        .sidebar--collapsed .sidebar-link.active {
            background: #1d4ed8;
            box-shadow: inset 4px 0 0 #93c5fd;
        }

        /* ===== Content ===== */
        .content {
            flex: 1;
            padding: 32px;
        }

        /* ===== Dashboard Cards ===== */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
                    transition: all 0.15s ease;
            text-decoration: none;
            color: inherit;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 22px rgba(0,0,0,.08);
            transform: translateY(-2px);
        }

        .dashboard-card--alert {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
        }

        .pulse-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #ea580c;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(234, 88, 12, 0); }
            100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
        }

        /* Card content */
        .dashboard-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .dashboard-card-number {
            font-size: 28px;
            font-weight: 700;
            margin-top: 10px;
            color: var(--text-dark);
        }

        /* Variants */
        .dashboard-card--info {
            border-right: 4px solid var(--primary);
        }

        .dashboard-card--warning {
            border-right: 4px solid #f59e0b;
        }

        .dashboard-card--success {
            border-right: 4px solid var(--accent);
        }

        .dashboard-card--neutral {
            border-right: 4px solid #64748b;
        }

        .dashboard-card--reports {
            border-right: 4px solid #7c3aed; /* بنفسجي هادئ */
        }

        /* =========================
           Tables – Unified System
        ========================= */

        .table-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 16px;
        }

        .table thead {
            background: var(--secondary);
        }

        .table th {
            text-align: center;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            border-bottom: 1px solid var(--border);
        }

        .table td {
            text-align: center;
            padding: 18px;
            font-size: 16px;
            line-height: 1.8;
            border-bottom: 1px solid var(--border);
        }

        /* row hover */
        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* clickable rows */
        .referral-row {
            cursor: pointer;
            position: relative;
        }

        /* status bar */
        .referral-main-cell {
            position: relative;
            transition: transform .15s ease;
            font-weight: 600;
            white-space: nowrap;
        }

        .referral-row:hover .referral-main-cell {
            transform: translateY(-4px);
        }

        .referral-main-cell::before {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: transparent;
            transition: background .15s ease;
        }

        .referral-row[data-status="prepared"]:hover .referral-main-cell::before {
            background: #f59e0b;
        }

        .referral-row[data-status="ready_for_generation"]:hover .referral-main-cell::before {
            background: #2563eb;
        }

        .referral-row[data-status="generated"]:hover .referral-main-cell::before {
            background: #16a34a;
        }

        /* =========================
           Case status hover colors
        ========================= */

        .referral-row[data-case-status="new"]:hover .referral-main-cell::before {
            background: #64748b; /* رمادي */
        }

        .referral-row[data-case-status="under_review"]:hover .referral-main-cell::before {
            background: #f59e0b; /* أصفر */
        }

        .referral-row[data-case-status="documented"]:hover .referral-main-cell::before {
            background: #0d9488; /* أخضر مزرق */
        }

        .referral-row[data-case-status="archived"]:hover .referral-main-cell::before {
            background: #334155; /* داكن */
        }

        /* =========================
           Status Badges – Unified
        ========================= */

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* الحالات */
        .status-badge[data-status="prepared"] {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-badge[data-status="ready_for_generation"] {
            background: #E0F2FE;
            color: #075985;
        }

        .status-badge[data-status="generated"] {
            background: #DCFCE7;
            color: #166534;
        }

        /* =========================
           Case Status Badges
        ========================= */

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Case statuses */
        .status-badge[data-case-status="new"] {
            background: #e0f2fe;
            color: #075985;
        }

        .status-badge[data-case-status="under_review"] {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge[data-case-status="documented"] {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge[data-case-status="archived"] {
            background: #e5e7eb;
            color: #374151;
        }

        /* ===== Track Badges (Unified) ===== */
        .track-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
        }

        /* Track variants */
        .track-badge[data-track="SPECIAL_PROCEDURES"] {
            background: #F3E8FF;
            color: #6B21A8;
        }

        .track-badge[data-track="HUMANITARIAN_PROTECTION"] {
            background: #E0F2FE;
            color: #075985;
        }

        .track-badge[data-track="NGO_LEGAL"] {
            background: #FFEDD5;
            color: #9A3412;
        }

        .track-badge[data-track="UN_ACCOUNTABILITY"] {
            background: #ECFDF5;
            color: #065F46;
        }

        /* =========================
           Case Summary Panel
        ========================= */

        .case-summary {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
        }

        /* Status Bar */
        .case-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .case-status-label {
            font-size: 16px;
        }

        .case-number {
            font-size: 14px;
            opacity: .85;
        }

        /* Status colors */
        .case-status-bar[data-case-status="documented"] {
            background: #dcfce7;
            color: #166534;
        }

        .case-status-bar[data-case-status="under_review"] {
            background: #fef3c7;
            color: #92400e;
        }

        .case-status-bar[data-case-status="new"] {
            background: #e0f2fe;
            color: #075985;
        }

        .case-status-bar[data-case-status="archived"] {
            background: #e5e7eb;
            color: #374151;
        }

        /* تنبيه الحالات المتاخره */
        .review-warning {
            border-right: 4px solid #f59e0b;
        }

        .review-danger {
            border-right: 4px solid #dc2626;
        }

        .referral-row.review-danger:hover {
            background: #e68787 !important;
        }


        /* Info Grid */
        .case-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
           gap: 16px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 16px;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
        }

        .info-value.emphasis {
            text-transform: uppercase;
        }
        
        /* =========================
           اجراءات الحالة
        ========================= */
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
        }

        .info-box.muted {
            color: #64748b;
        }

        /* =========================
        Success Message
        ========================= */
        .info-box.info-success {
            background: #ecfdf5;              /* أخضر فاتح احترافي */
            border: 1px solid #10b981;        /* أخضر واضح بدون فلاش */
            color: #065f46;                   /* نص أخضر داكن مريح */
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-weight: 500;
        }

        .info-box.info-success .info-icon {
            color: #10b981;
            font-size: 18px;
            line-height: 1.2;
            margin-top: 1px;
        }
        
        /* =========================
           Primary Action Button
        ========================= */
        .btn-primary-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px 14px;
            background: #2563eb;          /* أزرق احترافي */
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-primary-action:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 14px rgba(37, 99, 235, .25);
            transform: translateY(-1px);
        }

        /* =========================
        Action Buttons – Unified
        ========================= */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-action.full-width {
            width: 100%;
        }

        /* Primary */
        .btn-action.btn-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-action.btn-primary:hover {
            background: #1d4ed8;
        }

        /* Success */
        .btn-action.btn-success {
            background: #10b981;
            color: #fff;
        }
        .btn-action.btn-success:hover {
            background: #059669;
        }

        /* Secondary / Archive */
        .btn-action.btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5f5;
        }
        .btn-action.btn-secondary:hover {
            background: #e2e8f0;
        }

        a.btn-action,
        button.btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 12px 20px;
            min-height: 44px;

            font-size: 14px;
            font-weight: 600;
            line-height: 1;

            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        /* مهم: إزالة ستايل الزر الافتراضي */
        button.btn-action {
            appearance: none;
        }   

        /* Action stack */
        .action-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
        }

        /* =========================
           Pagination – Hard Fix
        ========================= */
        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            direction: rtl;
        }

        /* كل العناصر */
        .pagination li {
        list-style: none;
        }

        /* الروابط والأرقام */
        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #1f2937;
            text-decoration: none;
            transition: all .15s ease;
        }

        /* Hover */
        .pagination a:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* الصفحة الحالية */
        .pagination .active span {
            background: #0d9488;
            color: #fff;
            border-color: #0d9488;
            font-weight: 600;
        }

        /* disabled (… أو السابق/التالي غير المتاح) */
        .pagination .disabled span {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: default;
        }

        /* إزالة أي SVG */
        .pagination svg {
            display: none !important;
        }

        /* =========================
           صفحة انشاء الاحالة
        ========================= */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        /* ===== Page Title (if needed) ===== */
        .page-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        /* ===== Info Block (Read-only data) ===== */
        .info-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 16px;
            margin-bottom: 24px;
        }

        .info-block .label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .info-block .value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }

        /* ===== Form Actions (Buttons Area) ===== */
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            margin-top: 28px;
        }

        .page-subtitle {
            margin-top: 4px;
            font-size: 14px;
            color: #64748b;
        }

        /* ===== عناوين الفقرات ===== */
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .section-title .subtitle {
            display: block;
            margin-top: 2px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
        }

        .card {
            background: #ffffff;
            border-radius: 10px;

            border: 1px solid #e2e8f0;

            padding: 24px;
            padding-right: 28px;
            margin-bottom: 40px; /* فصل قوي وواضح */

            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 8px 24px rgba(15, 23, 42, 0.06);

            position: relative;
        }

        .card::before {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 0 10px 10px 0;
            background: #e5e7eb;
        }

        .card[data-block="A"]::before {
            background: #c7d2fe; /* أزرق هادئ */
        }

        .card[data-block="B"]::before {
            background: #bae6fd; /* Soft Sky */
        }

        .card[data-block="C"]::before {
            background: #bbf7d0; /* Soft Mint */
        }
 
        .card[data-block="D"]::before {
            background: #fde68a; /* Soft Amber */
        }

        .card[data-block="E"]::before {
            background: #fed7aa; /* Soft Orange */
        }

        .card[data-block="F"]::before {
            background: #e9d5ff; /* Soft Purple */
        }

        .card[data-block="G"]::before {
            background: #99f6e4; /* Soft Teal */
        }

        .card[data-block="H"]::before {
            background: #a6d9ee; /* Soft Slate */
        }

        .card[data-block="I"]::before {
            background: #fecdd3; /* Soft Rose */
        }

        .card[data-block="J"]::before {
            background: #ddd6fe; /* Muted Lavender */
        }


        /* ===== Referral Create ===== */
        .referral-create-wrapper {
            display: flex;
            justify-content: center;
        }

        .referral-create-card {
            width: 100%;
            max-width: 520px;
            padding: 24px;
        }

        /* ===== Form ===== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: #0d9488;
        }

        .form-control--sm {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
        }

        /* errors */
        .form-error {
            margin-top: 6px;
            font-size: 14px;
            color: #b91c1c;
        }

        /* ===== Actions ===== */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        /* =========================
           الازرار الرئيسية
        ========================= */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            font-family: inherit;
            font-weight: 600;
            line-height: 1;

            padding: 8px 16px;
            border-radius: 8px;

            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;

            border: 1px solid transparent;
            background: transparent;

            transition:
                background-color .15s ease,
                color .15s ease,
                border-color .15s ease,
                opacity .15s ease;
        }

        /* ---------- Sizes ---------- */
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
        }

        .btn-md {
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-lg {
            padding: 12px 20px;
            font-size: 15px;
        }

        /* ---------- Primary ---------- */
        .btn-primary {
            background: #1f3a5f;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #e2e8f0;
        }

        /* ---------- Secondary (Soft) ---------- */
        .btn-secondary {
            background: #f1f5f9;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        /* ---------- Outline (Edit / Neutral Action) ---------- */
        .btn-outline-primary {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #1f3a5f;
        }

        .btn-outline-primary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* ---------- Success ---------- */
        .btn-success {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-success:hover {
            background: #15803d;
        }

        /* ---------- Danger ---------- */
        .btn-danger {
            background: #b91c1c;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #991b1b;
        }
        
        /* ---------- Disabled ---------- */
        .btn:disabled,
        .btn.disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* =========================
           نوع الجهة
        ========================= */
        .entity-type-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* منظمة حقوقية */
        .entity-type-badge[data-entity-type="NGO"] {
            background: #ffe4e6;
            color: #be123c;
        }

        /* جهة أممية */
        .entity-type-badge[data-entity-type="UN"] {
            background: #ecfeff;
            color: #0369a1;
        }

        /* جهة إنسانية */
        .entity-type-badge[data-entity-type="Humanitarian"] {
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* =========================
           ايقونات
        ========================= */
        .info-box {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            line-height: 1.7;
        }

        .info-icon {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #3730a3;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-info {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .info-info .info-icon {
            background: #e0e7ff;
            color: #3730a3;
        }

        .info-pin {
            background: #f8fafc;
            border-color: #c7d2fe;
        }

        .info-pin .info-icon {
            background: #eef2ff;
            color: #4338ca;
        }

        .info-danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .info-danger .info-icon {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           المساعد
        ========================= */
        .decision-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .decision-factors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .toggle-row:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .toggle-label {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            line-height: 1.4;
        }

        /* Toggle Switch */
        .toggle-row input[type="checkbox"] {
            display: none;
        }

        .toggle-ui {
            position: relative;
            width: 42px;
            height: 22px;
            background: #d1d5db;
            border-radius: 999px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .toggle-ui::after {
            content: "";
            position: absolute;
            top: 2px;
            right: 2px;
            width: 18px;
            height: 18px;
            background: #ffffff;
            border-radius: 50%;
            transition: transform 0.2s ease;
        }

        .toggle-row input:checked + .toggle-ui {
            background: #2563eb;
        }

        .toggle-row input:checked + .toggle-ui::after {
            transform: translateX(-20px);
        }

        .toggle-row input:checked ~ .toggle-label {
            color: #1e3a8a;
            font-weight: 600;
        }

        /* =========================
        Decision Result – Assistant
        ========================= */

        .decision-result-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
        }

        /* Summary */
        .decision-summary {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            margin-top: 16px;
        }

        .summary-icon {
            font-size: 32px;
        }

        .summary-title {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
        }

        .summary-sub {
            font-size: 14px;
            color: #334155;
        }

        /* Decision Groups */
        .decision-group {
            margin-top: 24px;
        }

        .decision-group h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .decision-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 10px;
        }

        .decision-item {
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid;
        }

        /* Mandatory */
        .decision-group.mandatory .decision-item {
            background: #fef2f2;
            border-color: #fecaca;
            color: #7f1d1d;
        }

        /* Supporting */
        .decision-group.supporting .decision-item {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e3a8a;
        }

        /* =========================
        Flash Success Message
        ========================= */
        .flash-success {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 14px 18px;
            margin-bottom: 20px;

            background: #ecfdf5;           /* أخضر خفيف */
            color: #065f46;

            border: 1px solid #a7f3d0;
            border-radius: 10px;

            box-shadow: 0 0 0 rgba(16,185,129, 0.4);
            animation: flashGlow 1.5s ease-in-out infinite alternate;
        }

        .flash-success .flash-icon {
            font-size: 18px;
            font-weight: bold;
        }

        .flash-success .flash-text {
            font-size: 14px;
            font-weight: 500;
        }

        /* نبض خفيف */
        @keyframes flashGlow {
            from {
                box-shadow: 0 0 0 rgba(16,185,129, 0.25);
            }
            to {
                box-shadow: 0 0 12px rgba(16,185,129, 0.45);
            }
        }

        /* Error Message */
        .flash-error {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 14px 18px;
            margin-bottom: 20px;

            background: #fef2f2;
            color: #7f1d1d;

            border: 1px solid #fecaca;
            border-radius: 10px;

            animation: flashGlowRed 1.5s ease-in-out infinite alternate;
        }

        @keyframes flashGlowRed {
            from {
                box-shadow: 0 0 0 rgba(239,68,68, 0.25);
            }
            to {
                box-shadow: 0 0 12px rgba(239,68,68, 0.45);
            }
        }

        /* ===============================
        Cases Search Bar
        ================================ */

        .cases-search-bar {
            margin-bottom: 20px;
        }

        .search-wrapper {
            position: relative;
            max-width: 420px;
        }

        .search-input {
            width: 100%;
            padding: 12px 40px 12px 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-clear {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            font-size: 14px;
            color: #94a3b8;
            text-decoration: none;
            cursor: pointer;
        }

        .search-clear:hover {
            color: #ef4444;
        }

        /* ستايل البحث */
        .search-highlight {
            background-color: #fde68a; /* أصفر هادئ */
            color: #1f2933;
            padding: 0 4px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* ===============================
        رسالة التوثيق
        ================================ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-box {
            background: white;
            padding: 25px;
            width: 450px;
            border-radius: 8px;
        }

        /* ===============================
                زر نسخ الايميل
        ================================ */
        .copyable-email {
            font-family: monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .copy-btn {
           background: #2563eb;
           color: #fff;
          border: none;
          padding: 4px 10px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 13px;
        }

        .copy-btn:hover {
          background: #1d4ed8;
        }

    </style>

    @stack('styles')
</head>

<body>

<!-- ===== Header ===== -->
<div class="header">
    <div class="header-title">
        نظام إدارة القضايا الحقوقية
    </div>

    <div class="header-user">
        {{ auth()->user()->name ?? '' }}
    </div>
</div>

<!-- ===== Main Layout ===== -->
<div class="container">

    <!-- ===== Sidebar ===== -->
    @php
        $route = request()->route() ? request()->route()->getName() : '';
    @endphp

    <aside class="sidebar sidebar--expanded" id="sidebar">

        <div class="sidebar-header">
            <span class="sidebar-title">لوحة التحكم</span>

            <button class="sidebar-toggle" onclick="toggleSidebar()">
                ☰
            </button>
        </div>

        <nav class="sidebar-nav">

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ $route === 'admin.dashboard' ? 'active' : '' }}">
                <span class="icon">📊</span>
                <span class="label">نظرة عامة</span>
            </a>

            <a href="{{ route('admin.cases.index') }}"
                class="sidebar-link {{ str_starts_with($route, 'admin.cases') ? 'active' : '' }}">
                <span class="icon">🗂️</span>
                <span class="label">الحالات</span>
            </a>

            <a href="{{ route('admin.referrals.index') }}"
                class="sidebar-link {{ str_starts_with($route, 'admin.referrals') ? 'active' : '' }}">
                <span class="icon">🔁</span>
                <span class="label">الإحالات</span>
            </a>

            <a href="{{ route('admin.reports.generated') }}"
                class="sidebar-link {{ str_starts_with($route, 'admin.reports') ? 'active' : '' }}">
                <span class="icon">📄</span>
                <span class="label">التقارير</span>
            </a>

            <a href="{{ route('admin.entities.index') }}"
                class="sidebar-link {{ str_starts_with($route, 'admin.entities') ? 'active' : '' }}">
                <span class="icon">🏛️</span>
                <span class="label">الجهات</span>
            </a>

        </nav>

    </aside>

    <!-- ===== Content ===== -->
    <main class="content">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flash-success" id="flash-success">
                <span class="flash-icon">✓</span>
                <div class="flash-text">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-error" id="flash-error">
                <span class="flash-icon">⚠</span>
                <div class="flash-text">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Page Content --}}
        @yield('content')
        
    </main>

</div>
<script>
(function () {
    const sidebar = document.getElementById('sidebar');

    // استرجاع الحالة عند التحميل
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('sidebar--collapsed');
    }

    window.toggleSidebar = function () {
        sidebar.classList.toggle('sidebar--collapsed');

        // حفظ الحالة
        localStorage.setItem(
            'sidebar-collapsed',
            sidebar.classList.contains('sidebar--collapsed')
        );
    };
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const flash = document.getElementById('flash-success') 
               || document.getElementById('flash-error');
    if (!flash) return;

    setTimeout(() => {
        flash.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            flash.remove();
        }, 700);

    }, 8000);
});

</script>


</body>
</html>
