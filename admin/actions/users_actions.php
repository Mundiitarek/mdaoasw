<?php

function admin_handle_users_actions(string $section, array $admin): void {
    $action = $_POST['action'] ?? '';
    if ($action !== 'user_toggle_block') {
        return;
    }

    $userId = (int)($_POST['user_id'] ?? 0);
    $nextBlock = (int)($_POST['next_block'] ?? 0) === 1 ? 1 : 0;
    $reason = trim($_POST['block_reason'] ?? '');

    if ($userId <= 0) {
        redirect_with_message(BASE_URL . 'admin/index.php?section=users', 'معرّف المستخدم غير صالح', 'error');
    }

    db_update('users', [
        'is_blocked' => $nextBlock,
        'block_reason' => $nextBlock ? $reason : null,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [':id' => $userId]);

    log_activity('admin', (int)($admin['id'] ?? 0), 'user_toggle_block', 'تغيير حالة حظر المستخدم #' . $userId);

    redirect_with_message(
        BASE_URL . 'admin/index.php?section=users',
        $nextBlock ? 'تم حظر المستخدم بنجاح' : 'تم فك حظر المستخدم بنجاح',
        'success'
    );
}
