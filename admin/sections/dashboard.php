<?php
$dashboard = [
    'total_users' => (int)(db_fetch("SELECT COUNT(*) c FROM users")['c'] ?? 0),
    'active_users' => (int)(db_fetch("SELECT COUNT(*) c FROM users WHERE status=1 AND is_blocked=0")['c'] ?? 0),
    'blocked_users' => (int)(db_fetch("SELECT COUNT(*) c FROM users WHERE is_blocked=1")['c'] ?? 0),
    'approved_vendors' => (int)(db_fetch("SELECT COUNT(*) c FROM vendors WHERE status='approved'")['c'] ?? 0),
    'pending_vendors' => (int)(db_fetch("SELECT COUNT(*) c FROM vendors WHERE status='pending'")['c'] ?? 0),
    'approved_drivers' => (int)(db_fetch("SELECT COUNT(*) c FROM drivers WHERE status='approved'")['c'] ?? 0),
    'pending_drivers' => (int)(db_fetch("SELECT COUNT(*) c FROM drivers WHERE status='pending'")['c'] ?? 0),
    'total_orders' => (int)(db_fetch("SELECT COUNT(*) c FROM orders")['c'] ?? 0),
    'today_orders' => (int)(db_fetch("SELECT COUNT(*) c FROM orders WHERE DATE(created_at)=CURDATE()")['c'] ?? 0),
    'delivered_orders' => (int)(db_fetch("SELECT COUNT(*) c FROM orders WHERE status='delivered'")['c'] ?? 0),
    'cancelled_orders' => (int)(db_fetch("SELECT COUNT(*) c FROM orders WHERE status='cancelled'")['c'] ?? 0),
    'total_revenue' => (float)(db_fetch("SELECT SUM(total) s FROM orders WHERE status='delivered'")['s'] ?? 0),
    'today_revenue' => (float)(db_fetch("SELECT SUM(total) s FROM orders WHERE status='delivered' AND DATE(created_at)=CURDATE()")['s'] ?? 0),
];

$recent_orders = db_fetch_all(
    "SELECT o.id,o.order_number,o.total,o.status,o.created_at,u.name user_name,v.business_name vendor_name
     FROM orders o
     JOIN users u ON u.id=o.user_id
     JOIN vendors v ON v.id=o.vendor_id
     ORDER BY o.created_at DESC LIMIT 8"
);
?>

<div class="stat-grid">
    <div class="stat"><div class="k">إجمالي المستخدمين</div><div class="v"><?= $dashboard['total_users'] ?></div></div>
    <div class="stat"><div class="k">نشطون</div><div class="v"><?= $dashboard['active_users'] ?></div></div>
    <div class="stat"><div class="k">محظورون</div><div class="v"><?= $dashboard['blocked_users'] ?></div></div>
    <div class="stat"><div class="k">التجار (موافق)</div><div class="v"><?= $dashboard['approved_vendors'] ?></div></div>
    <div class="stat"><div class="k">التجار (معلق)</div><div class="v"><?= $dashboard['pending_vendors'] ?></div></div>
    <div class="stat"><div class="k">المناديب (معلق)</div><div class="v"><?= $dashboard['pending_drivers'] ?></div></div>
    <div class="stat"><div class="k">إجمالي الطلبات</div><div class="v"><?= $dashboard['total_orders'] ?></div></div>
    <div class="stat"><div class="k">طلبات اليوم</div><div class="v"><?= $dashboard['today_orders'] ?></div></div>
    <div class="stat"><div class="k">إيراد إجمالي</div><div class="v"><?= format_price($dashboard['total_revenue']) ?></div></div>
    <div class="stat"><div class="k">إيراد اليوم</div><div class="v"><?= format_price($dashboard['today_revenue']) ?></div></div>
</div>

<div class="card">
    <div class="card-header"><strong>آخر الطلبات</strong></div>
    <div class="card-body table-wrap">
        <table class="table">
            <thead><tr><th>الطلب</th><th>العميل</th><th>المتجر</th><th>الحالة</th><th>الإجمالي</th><th>التاريخ</th></tr></thead>
            <tbody>
            <?php foreach ($recent_orders as $row): ?>
                <tr>
                    <td><?= escape($row['order_number']) ?></td>
                    <td><?= escape($row['user_name']) ?></td>
                    <td><?= escape($row['vendor_name']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'delivered'): ?><span class="chip ok">تم التوصيل</span>
                        <?php elseif ($row['status'] === 'cancelled'): ?><span class="chip bad">ملغي</span>
                        <?php else: ?><span class="chip warn"><?= escape($row['status']) ?></span><?php endif; ?>
                    </td>
                    <td><?= format_price($row['total']) ?></td>
                    <td><?= escape($row['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
