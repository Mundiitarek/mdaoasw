<?php
if (!isset($stats) || !is_array($stats)) {
    $stats = [
        'pending_vendors' => 0,
        'pending_drivers' => 0,
        'pending_reports' => 0,
        'new_orders_today' => 0,
        'total_users' => 0,
        'online_drivers' => 0,
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($page_title ?? 'لوحة التحكم') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{--bg:#f6f8fb;--card:#fff;--text:#1f2937;--muted:#6b7280;--primary:#ff6b35;--border:#e5e7eb;--ok:#059669;--warn:#d97706;--bad:#dc2626}
        *{box-sizing:border-box} body{margin:0;font-family:'Cairo',sans-serif;background:var(--bg);color:var(--text)}
        .panel-shell{display:flex;min-height:100vh}
        .panel-main{flex:1;display:flex;flex-direction:column;min-width:0}
        .panel-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:#fff;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:10}
        .panel-header__left,.panel-header__right{display:flex;align-items:center;gap:12px}
        .panel-title{margin:0;font-size:20px} .panel-subtitle{margin:0;color:var(--muted);font-size:13px}
        .panel-content{padding:18px;display:grid;gap:16px}
        .card{background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 6px 20px rgba(15,23,42,.04)}
        .card-header{padding:16px;border-bottom:1px solid var(--border)} .card-body{padding:16px}
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px}
        .stat{background:#fff;border:1px solid var(--border);border-radius:14px;padding:14px}
        .stat .k{font-size:12px;color:var(--muted)} .stat .v{font-size:22px;font-weight:800}
        .chip{display:inline-flex;align-items:center;gap:6px;padding:4px 9px;border-radius:999px;font-size:12px;font-weight:700}
        .chip.ok{background:#dcfce7;color:#166534}.chip.warn{background:#fef3c7;color:#92400e}.chip.bad{background:#fee2e2;color:#991b1b}
        .table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse;min-width:900px}
        .table th,.table td{padding:10px;border-bottom:1px solid var(--border);text-align:right;vertical-align:top}
        .table th{color:#374151;background:#f9fafb}
        input,select,textarea,button{font-family:inherit}
        input,select,textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px;background:#fff}
        button,.btn{border:none;background:var(--primary);color:#fff;padding:9px 12px;border-radius:10px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
        .btn-outline{background:#fff;color:#374151;border:1px solid #d1d5db}.btn-danger{background:#dc2626}
        .icon-btn{display:none;border:1px solid var(--border);background:#fff;color:#111;padding:8px 10px;border-radius:10px}
        .header-stat-chip{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:6px 10px;border-radius:999px;font-size:13px;display:flex;align-items:center;gap:6px}
        .header-admin{display:flex;align-items:center;gap:10px}.avatar{width:38px;height:38px;border-radius:50%;background:#111827;color:#fff;display:grid;place-items:center;font-weight:700}
        @media (max-width: 992px){
          .icon-btn{display:inline-flex}.panel-header{padding:12px}.panel-content{padding:12px}
          .table{min-width:760px}
        }
    </style>
</head>
<body>
<div class="panel-shell">
