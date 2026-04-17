<?php
$pendingVendors = (int)($stats['pending_vendors'] ?? 0);
$pendingDrivers = (int)($stats['pending_drivers'] ?? 0);
$pendingReports = (int)($stats['pending_reports'] ?? 0);
?>
<aside class="admin-sidebar" id="adminSidebar" style="width:280px;background:#0f172a;color:#e5e7eb;position:sticky;top:0;height:100vh;overflow:auto;padding:14px;border-left:1px solid #1f2937">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 6px 16px;">
        <div>
            <h2 style="margin:0;font-size:20px;color:#fff;">سريع</h2>
            <small style="color:#94a3b8;">لوحة التحكم</small>
        </div>
    </div>

    <nav>
        <div class="menu-group">
            <div class="menu-label">الرئيسية</div>
            <a class="menu-item <?= $section === 'dashboard' ? 'active' : '' ?>" href="?section=dashboard">لوحة التحكم</a>
            <a class="menu-item <?= $section === 'statistics' ? 'active' : '' ?>" href="?section=statistics">الإحصائيات</a>
        </div>

        <div class="menu-group">
            <div class="menu-label">الإدارة</div>
            <a class="menu-item <?= $section === 'users' ? 'active' : '' ?>" href="?section=users">العملاء</a>
            <a class="menu-item <?= $section === 'vendors' ? 'active' : '' ?>" href="?section=vendors">المطاعم والمتاجر <?= $pendingVendors > 0 ? '<span class="sb-badge">'.$pendingVendors.'</span>' : '' ?></a>
            <a class="menu-item <?= $section === 'drivers' ? 'active' : '' ?>" href="?section=drivers">المناديب <?= $pendingDrivers > 0 ? '<span class="sb-badge">'.$pendingDrivers.'</span>' : '' ?></a>
            <a class="menu-item <?= $section === 'orders' ? 'active' : '' ?>" href="?section=orders">الطلبات</a>
        </div>

        <div class="menu-group">
            <div class="menu-label">المحتوى</div>
            <a class="menu-item <?= $section === 'zones' ? 'active' : '' ?>" href="?section=zones">المناطق والأسعار</a>
            <a class="menu-item <?= $section === 'banners' ? 'active' : '' ?>" href="?section=banners">البانرات</a>
            <a class="menu-item <?= $section === 'offers' ? 'active' : '' ?>" href="?section=offers">العروض والخصومات</a>
            <a class="menu-item <?= $section === 'categories' ? 'active' : '' ?>" href="?section=categories">التصنيفات</a>
            <a class="menu-item <?= $section === 'products' ? 'active' : '' ?>" href="?section=products">المنتجات</a>
            <a class="menu-item <?= $section === 'loyalty' ? 'active' : '' ?>" href="?section=loyalty">برنامج الولاء</a>
            <a class="menu-item <?= $section === 'wheel' ? 'active' : '' ?>" href="?section=wheel">دولاب الحظ</a>
        </div>

        <div class="menu-group">
            <div class="menu-label">التفاعلات</div>
            <a class="menu-item <?= $section === 'ratings' ? 'active' : '' ?>" href="?section=ratings">التقييمات</a>
            <a class="menu-item <?= $section === 'reports' ? 'active' : '' ?>" href="?section=reports">البلاغات <?= $pendingReports > 0 ? '<span class="sb-badge">'.$pendingReports.'</span>' : '' ?></a>
            <a class="menu-item <?= $section === 'tips' ? 'active' : '' ?>" href="?section=tips">البقشيش</a>
            <a class="menu-item <?= $section === 'notifications' ? 'active' : '' ?>" href="?section=notifications">إرسال إشعارات</a>
        </div>

        <div class="menu-group">
            <div class="menu-label">النظام</div>
            <a class="menu-item <?= $section === 'activity' ? 'active' : '' ?>" href="?section=activity">سجل النشاط</a>
            <a class="menu-item <?= $section === 'settings' ? 'active' : '' ?>" href="?section=settings">الإعدادات</a>
        </div>
    </nav>

    <div style="margin-top:14px;padding:12px;border:1px solid #1f2937;border-radius:12px;background:#111827;">
        <div style="font-weight:700;color:#fff"><?= escape($admin['name'] ?? 'Admin') ?></div>
        <div style="font-size:12px;color:#94a3b8;"><?= escape(($admin['role'] ?? 'manager') === 'super' ? 'مدير عام' : 'مدير') ?></div>
    </div>
</aside>

<style>
.menu-group{margin-bottom:14px}.menu-label{font-size:12px;color:#94a3b8;margin:8px 8px}
.menu-item{display:flex;justify-content:space-between;gap:8px;padding:10px 12px;border-radius:10px;color:#e5e7eb;text-decoration:none;margin:2px 0;background:transparent}
.menu-item:hover{background:#1f2937}.menu-item.active{background:#ff6b35;color:#fff}
.sb-badge{background:#fff;color:#111827;border-radius:20px;padding:0 8px;font-size:11px;line-height:20px;height:20px;display:inline-flex;align-items:center}
@media (max-width: 992px){
  .admin-sidebar{position:fixed !important;inset:0 auto 0 0;transform:translateX(100%);transition:.25s;z-index:30;width:min(88vw,320px)!important}
  .admin-sidebar.open{transform:translateX(0)}
}
</style>
