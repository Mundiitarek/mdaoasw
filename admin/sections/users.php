<?php
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = clean_input($_GET['search'] ?? '');
$filter = clean_input($_GET['filter'] ?? 'all');

$where = "WHERE 1=1";
$params = [];

if ($search !== '') {
    $where .= " AND (u.name LIKE ? OR u.phone LIKE ? OR u.device_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter === 'blocked') {
    $where .= " AND u.is_blocked=1";
} elseif ($filter === 'active') {
    $where .= " AND u.status=1 AND u.is_blocked=0";
} elseif ($filter === 'recent') {
    $where .= " AND u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}

$total = (int)(db_fetch("SELECT COUNT(*) c FROM users u $where", $params)['c'] ?? 0);
$users = db_fetch_all(
    "SELECT u.*,
            (SELECT COUNT(*) FROM addresses a WHERE a.user_id=u.id) addresses_count,
            (SELECT COUNT(*) FROM notifications n WHERE n.user_id=u.id AND n.user_type='user') notifications_count
     FROM users u
     $where
     ORDER BY u.created_at DESC
     LIMIT ? OFFSET ?",
    array_merge($params, [$limit, $offset])
);
$totalPages = max(1, (int)ceil($total / $limit));
?>

<div class="card">
    <div class="card-header"><strong>إدارة العملاء</strong></div>
    <div class="card-body">
        <form method="get" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-bottom:12px;">
            <input type="hidden" name="section" value="users">
            <input name="search" value="<?= escape($search) ?>" placeholder="بحث اسم/هاتف/جهاز">
            <select name="filter">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>كل العملاء</option>
                <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>النشطون</option>
                <option value="blocked" <?= $filter === 'blocked' ? 'selected' : '' ?>>المحظورون</option>
                <option value="recent" <?= $filter === 'recent' ? 'selected' : '' ?>>آخر 7 أيام</option>
            </select>
            <button type="submit">تطبيق</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>العميل</th><th>الهاتف</th><th>الطلبات/الإنفاق</th><th>الولاء</th><th>النشاط</th><th>تحكم</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <strong><?= escape($u['name']) ?></strong><br>
                            <small>ID: <?= (int)$u['id'] ?></small>
                        </td>
                        <td><?= escape($u['phone']) ?><br><small><?= escape($u['device_id'] ?? '-') ?></small></td>
                        <td><?= (int)($u['total_orders'] ?? 0) ?> طلب<br><?= format_price($u['total_spent'] ?? 0) ?></td>
                        <td><?= (int)($u['loyalty_points'] ?? 0) ?> نقطة</td>
                        <td><small>عناوين: <?= (int)$u['addresses_count'] ?> · إشعارات: <?= (int)$u['notifications_count'] ?><br>آخر دخول: <?= escape($u['last_login'] ?? '-') ?></small></td>
                        <td>
                            <form method="post" style="display:grid;gap:6px;min-width:180px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="user_toggle_block">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="next_block" value="<?= !empty($u['is_blocked']) ? 0 : 1 ?>">
                                <input name="block_reason" value="<?= escape($u['block_reason'] ?? '') ?>" placeholder="سبب الحظر (اختياري)">
                                <button class="<?= !empty($u['is_blocked']) ? 'btn-outline' : '' ?>" onclick="return confirm('تأكيد العملية؟');">
                                    <?= !empty($u['is_blocked']) ? 'فك الحظر' : 'حظر المستخدم' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="btn <?= $i === $page ? '' : 'btn-outline' ?>" href="?section=users&page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>
