<?php
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$filter = clean_input($_GET['filter'] ?? 'all');
$search = clean_input($_GET['search'] ?? '');

$where = "WHERE 1=1";
$params = [];
if ($filter !== 'all') {
    $where .= " AND v.status = ?";
    $params[] = $filter;
}
if ($search !== '') {
    $where .= " AND (v.business_name LIKE ? OR v.name LIKE ? OR v.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$total = (int)(db_fetch("SELECT COUNT(*) c FROM vendors v $where", $params)['c'] ?? 0);
$vendors = db_fetch_all(
    "SELECT v.*, z.name_ar zone_name,
            (SELECT COUNT(*) FROM orders o WHERE o.vendor_id=v.id) total_orders,
            (SELECT SUM(o.total) FROM orders o WHERE o.vendor_id=v.id AND o.status='delivered') delivered_revenue,
            (SELECT COUNT(*) FROM products p WHERE p.vendor_id=v.id) products_count
     FROM vendors v
     LEFT JOIN zones z ON z.id=v.zone_id
     $where
     ORDER BY v.created_at DESC
     LIMIT ? OFFSET ?",
    array_merge($params, [$limit, $offset])
);
$totalPages = max(1, (int)ceil($total / $limit));
?>

<div class="card">
    <div class="card-header"><strong>إدارة المطاعم والمتاجر</strong></div>
    <div class="card-body">
        <form method="get" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-bottom:12px;">
            <input type="hidden" name="section" value="vendors">
            <input name="search" value="<?= escape($search) ?>" placeholder="بحث اسم/هاتف/متجر">
            <select name="filter">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>كل الحالات</option>
                <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>pending</option>
                <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>approved</option>
                <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>rejected</option>
                <option value="suspended" <?= $filter === 'suspended' ? 'selected' : '' ?>>suspended</option>
            </select>
            <button type="submit">تطبيق</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>المتجر</th><th>الاتصال</th><th>المنطقة</th><th>الحالة</th><th>مؤشرات</th><th>تحكم</th></tr></thead>
                <tbody>
                <?php foreach ($vendors as $v): ?>
                    <tr>
                        <td>
                            <strong><?= escape($v['business_name'] ?: $v['name']) ?></strong><br>
                            <small>ID: <?= (int)$v['id'] ?> · عمولة: <?= escape($v['commission_rate'] ?? '0') ?>%</small>
                        </td>
                        <td><?= escape($v['phone']) ?><br><small><?= !empty($v['is_open']) ? 'مفتوح' : 'مغلق' ?></small></td>
                        <td><?= escape($v['zone_name'] ?? '-') ?></td>
                        <td>
                            <?php if ($v['status'] === 'approved'): ?><span class="chip ok">approved</span>
                            <?php elseif ($v['status'] === 'pending'): ?><span class="chip warn">pending</span>
                            <?php else: ?><span class="chip bad"><?= escape($v['status']) ?></span><?php endif; ?>
                        </td>
                        <td><small>طلبات: <?= (int)$v['total_orders'] ?><br>إيراد: <?= format_price($v['delivered_revenue'] ?? 0) ?><br>منتجات: <?= (int)$v['products_count'] ?></small></td>
                        <td>
                            <div style="display:grid;gap:6px;min-width:190px;">
                                <form method="post" style="display:flex;gap:6px;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="vendor_change_status">
                                    <input type="hidden" name="vendor_id" value="<?= (int)$v['id'] ?>">
                                    <select name="status">
                                        <option value="approved">approved</option>
                                        <option value="rejected">rejected</option>
                                        <option value="suspended">suspended</option>
                                        <option value="pending">pending</option>
                                    </select>
                                    <button>حفظ</button>
                                </form>
                                <form method="post" style="display:grid;gap:6px;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="vendor_update_commission">
                                    <input type="hidden" name="vendor_id" value="<?= (int)$v['id'] ?>">
                                    <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="<?= escape((string)($v['commission_rate'] ?? 0)) ?>">
                                    <button class="btn-outline">تحديث العمولة</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="btn <?= $i === $page ? '' : 'btn-outline' ?>" href="?section=vendors&page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>
