<?php

$db = \Config\Database::connect();

$adminUserId = session()->get('user_id');

$adminUnreadCount = 0;

if ($adminUserId) {
    $adminUnreadCount = $db->table('notifications')
        ->where('user_id', $adminUserId)
        ->where('status', 'Unread')
        ->countAllResults();
}
?>

<li class="<?= ($notificationActive ?? false) ? 'active' : '' ?>">
    <a href="<?= base_url('admin/notifications') ?>">
        <i class="bi bi-bell"></i>
        Notifications

        <?php if ($adminUnreadCount > 0): ?>
            <span class="badge rounded-pill bg-danger ms-2">
                <?= $adminUnreadCount ?>
            </span>
        <?php endif; ?>
    </a>
</li>
