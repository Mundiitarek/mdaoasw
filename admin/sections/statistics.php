<?php
$ordersByMonth = db_fetch_all(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') ym, COUNT(*) orders_count, SUM(total) revenue
     FROM orders WHERE status='delivered'
     GROUP BY DATE_FORMAT(created_at, '%Y-%m')
     ORDER BY ym DESC LIMIT 12"
);
$ordersByHour = db_fetch_all(
    "SELECT HOUR(created_at) hr, COUNT(*) c
     FROM orders
     GROUP BY HOUR(created_at)
     ORDER BY hr"
);
?>

<div class="card">
    <div class="card-header"><strong>إحصائيات شهرية (طلبات مكتملة + إيراد)</strong></div>
    <div class="card-body table-wrap">
        <table class="table">
            <thead><tr><th>الشهر</th><th>عدد الطلبات</th><th>الإيراد</th></tr></thead>
            <tbody>
            <?php foreach ($ordersByMonth as $m): ?>
                <tr>
                    <td><?= escape($m['ym']) ?></td>
                    <td><?= (int)$m['orders_count'] ?></td>
                    <td><?= format_price($m['revenue']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>الطلبات حسب الساعة</strong></div>
    <div class="card-body">
        <canvas id="ordersHourChart" height="120"></canvas>
    </div>
</div>

<script>
(() => {
    const ctx = document.getElementById('ordersHourChart');
    if (!ctx || typeof Chart === 'undefined') return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($x) => str_pad((string)$x['hr'],2,'0',STR_PAD_LEFT).':00', $ordersByHour), JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'عدد الطلبات',
                data: <?= json_encode(array_map(fn($x) => (int)$x['c'], $ordersByHour), JSON_UNESCAPED_UNICODE) ?>,
                backgroundColor: '#ff6b35',
                borderRadius: 8
            }]
        },
        options: {responsive: true, plugins: {legend: {display: false}}}
    });
})();
</script>
