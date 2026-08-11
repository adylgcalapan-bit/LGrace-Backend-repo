<?php

$db = \Config\Database::connect();

$residentUserId = session()->get('user_id');

$residentUnreadCount = 0;

if ($residentUserId) {
    $residentUnreadCount = $db->table('notifications')
        ->where('user_id', $residentUserId)
        ->where('status', 'Unread')
        ->countAllResults();
}
?>

<li class="<?= ($notificationActive ?? false) ? 'active' : '' ?>">
    <a href="<?= base_url('resident/notifications') ?>">
        <i class="bi bi-bell-fill"></i>
        Notifications

        <?php if ($residentUnreadCount > 0): ?>
    <span
        class="resident-notification-badge"
        style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            margin-left: 8px;
            background: #dc3545;
            color: white;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        "
    >
        <?= (int) $residentUnreadCount ?>
    </span>
<?php endif; ?>
    </a>
</li>