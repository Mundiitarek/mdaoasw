<?php

function admin_handle_vendors_actions(string $section, array $admin): void {
    $action = $_POST['action'] ?? '';

    if ($action === 'vendor_change_status') {
        $vendorId = (int)($_POST['vendor_id'] ?? 0);
        $status = clean_input($_POST['status'] ?? 'pending');
        $allowed = ['pending', 'approved', 'rejected', 'suspended'];

        if ($vendorId <= 0 || !in_array($status, $allowed, true)) {
            redirect_with_message(BASE_URL . 'admin/index.php?section=vendors', 'بيانات الحالة غير صالحة', 'error');
        }

        $data = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];
        if ($status === 'rejected') {
            $data['rejection_reason'] = trim($_POST['rejection_reason'] ?? 'رفض من الإدارة');
        }
        if ($status === 'suspended') {
            $data['is_open'] = 0;
        }

        db_update('vendors', $data, 'id = ?', [':id' => $vendorId]);

        send_notification($vendorId, 'vendor', 'تحديث حالة الحساب', 'تم تحديث حالة حساب المتجر إلى: ' . $status, 'system');
        log_activity('admin', (int)($admin['id'] ?? 0), 'vendor_change_status', 'تغيير حالة متجر #' . $vendorId . ' إلى ' . $status);

        redirect_with_message(BASE_URL . 'admin/index.php?section=vendors', 'تم تحديث الحالة بنجاح', 'success');
    }

    if ($action === 'vendor_update_commission') {
        $vendorId = (int)($_POST['vendor_id'] ?? 0);
        $commission = (float)($_POST['commission_rate'] ?? 0);

        if ($vendorId <= 0 || $commission < 0 || $commission > 100) {
            redirect_with_message(BASE_URL . 'admin/index.php?section=vendors', 'قيمة العمولة غير صالحة', 'error');
        }

        db_update('vendors', [
            'commission_rate' => $commission,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [':id' => $vendorId]);

        log_activity('admin', (int)($admin['id'] ?? 0), 'vendor_update_commission', 'تعديل عمولة متجر #' . $vendorId . ' إلى ' . $commission);

        redirect_with_message(BASE_URL . 'admin/index.php?section=vendors', 'تم تحديث العمولة بنجاح', 'success');
    }
}
