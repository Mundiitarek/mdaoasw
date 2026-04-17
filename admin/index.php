<?php
/**
 * Admin Router (Modular)
 * يحتفظ بنفس فكرة ?section= مع بنية نظيفة قابلة للتوسعة
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/helpers.php';

if (!is_admin()) {
    redirect(BASE_URL . 'login.php?type=admin');
}

$admin = get_current_admin();

$sections = [
    'dashboard', 'statistics',
    'users', 'vendors', 'drivers', 'orders',
    'zones', 'banners', 'offers', 'categories', 'products', 'loyalty', 'wheel',
    'ratings', 'reports', 'tips', 'notifications',
    'activity', 'settings'
];

$page_titles = [
    'dashboard' => 'لوحة التحكم',
    'statistics' => 'الإحصائيات',
    'users' => 'العملاء',
    'vendors' => 'المطاعم والمتاجر',
    'drivers' => 'المناديب',
    'orders' => 'الطلبات',
    'zones' => 'المناطق والأسعار',
    'banners' => 'البانرات',
    'offers' => 'العروض والخصومات',
    'categories' => 'التصنيفات',
    'products' => 'المنتجات',
    'loyalty' => 'برنامج الولاء',
    'wheel' => 'دولاب الحظ',
    'ratings' => 'التقييمات',
    'reports' => 'البلاغات',
    'tips' => 'البقشيش',
    'notifications' => 'إرسال إشعارات',
    'activity' => 'سجل النشاط',
    'settings' => 'الإعدادات',
];

$section = isset($_GET['section']) ? clean_input($_GET['section']) : 'dashboard';
if (!in_array($section, $sections, true)) {
    $section = 'dashboard';
}

/**
 * تحميل عدادات مشتركة آمنة للـ Layout
 * يمنع أي Undefined variable / array offset على كل الصفحات
 */
function admin_load_layout_stats(): array {
    $defaults = [
        'pending_vendors' => 0,
        'pending_drivers' => 0,
        'pending_reports' => 0,
        'new_orders_today' => 0,
        'total_users' => 0,
        'online_drivers' => 0,
    ];

    $loaded = [
        'pending_vendors' => (int)(db_fetch("SELECT COUNT(*) AS c FROM vendors WHERE status='pending'")['c'] ?? 0),
        'pending_drivers' => (int)(db_fetch("SELECT COUNT(*) AS c FROM drivers WHERE status='pending'")['c'] ?? 0),
        'pending_reports' => (int)(db_fetch("SELECT COUNT(*) AS c FROM reports WHERE status='pending'")['c'] ?? 0),
        'new_orders_today' => (int)(db_fetch("SELECT COUNT(*) AS c FROM orders WHERE DATE(created_at)=CURDATE()")['c'] ?? 0),
        'total_users' => (int)(db_fetch("SELECT COUNT(*) AS c FROM users")['c'] ?? 0),
        'online_drivers' => (int)(db_fetch("SELECT COUNT(*) AS c FROM drivers WHERE is_online=1")['c'] ?? 0),
    ];

    return array_merge($defaults, $loaded);
}

$stats = admin_load_layout_stats();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        redirect_with_message(BASE_URL . 'admin/index.php?section=' . urlencode($section), 'رمز الحماية غير صالح، حدّث الصفحة وحاول مرة أخرى', 'error');
    }

    // Phase 2 actions only (users + vendors)
    require_once __DIR__ . '/actions/users_actions.php';
    require_once __DIR__ . '/actions/vendors_actions.php';

    admin_handle_users_actions($section, $admin);
    admin_handle_vendors_actions($section, $admin);
}

$page_title = APP_NAME . ' - ' . ($page_titles[$section] ?? 'لوحة التحكم');

$section_file = __DIR__ . '/sections/' . $section . '.php';

require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="panel-main">
    <header class="panel-header">
        <div class="panel-header__left">
            <button class="icon-btn" id="mobileSidebarBtn" type="button" aria-label="فتح القائمة">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="panel-title"><?= escape($page_titles[$section] ?? 'لوحة التحكم') ?></h1>
                <p class="panel-subtitle">إدارة تشغيلية كاملة للنظام</p>
            </div>
        </div>

        <div class="panel-header__right">
            <div class="header-stat-chip">
                <i class="fas fa-user-clock"></i>
                <span>موافقات معلّقة: <?= (int)($stats['pending_vendors'] ?? 0) + (int)($stats['pending_drivers'] ?? 0) ?></span>
            </div>
            <div class="header-admin">
                <div class="avatar"><?= escape(mb_substr($admin['name'] ?? 'A', 0, 1)) ?></div>
                <div>
                    <strong><?= escape($admin['name'] ?? 'Admin') ?></strong>
                    <small><?= escape(($admin['role'] ?? 'manager') === 'super' ? 'مدير عام' : 'مدير') ?></small>
                </div>
            </div>
        </div>
    </header>

    <section class="panel-content">
        <?php if (is_file($section_file)): ?>
            <?php require $section_file; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <h3>القسم قيد التفعيل</h3>
                    <p>سيتم نقل هذا القسم ضمن مراحل refactor القادمة مع الحفاظ على كامل الوظائف.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>
