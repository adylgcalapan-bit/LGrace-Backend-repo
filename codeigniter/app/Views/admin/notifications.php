<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/notifications.css') ?>">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <i class="bi bi-geo-alt-fill"></i>
            <h4>CPVS</h4>
        </div>
        <ul class="menu">
            <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
            <li><a href="<?= base_url('admin/reports') ?>"><i class="bi bi-file-earmark-text"></i>Reports</a></li>
            <li><a href="#"><i class="bi bi-map"></i>Map View</a></li>
            <li><a href="<?= base_url('admin/residents') ?>"><i class="bi bi-people"></i>Residents</a></li>
            <li><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
           <li class="active">
    <a href="<?= base_url('admin/notifications') ?>">
        <i class="bi bi-bell"></i>
        Notifications

        <?php if (($unreadCount ?? 0) > 0): ?>
            <span class="badge rounded-pill bg-danger ms-2">
                <?= (int) $unreadCount ?>
            </span>
        <?php endif; ?>
    </a>
</li>
            <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
            <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
            <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <h2>Notifications</h2>
                <p>Stay updated with recent admin and resident activities.</p>
            </div>
            <div class="top-actions">
                <button class="btn btn-outline-success" id="markAllBtn">Mark All as Read</button>
                <div class="profile">
                    <img src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                    <div><strong>Admin</strong><br><small>Administrator</small></div>
                </div>
            </div>
        </header>

        <section class="card page-card p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label">Search Notifications</label>
                    <input type="text" class="form-control" id="searchNotification" placeholder="Search notification...">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Filter</label>
                    <select class="form-select" id="filterNotification">
                        <option value="all">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>
                <div class="col-lg-3">
                   <div class="badge-count">
    <?= (int) ($unreadCount ?? 0) ?> Unread
</div>
                </div>
            </div>
        </section>

       <section class="list-group" id="notificationList">

    <?php if (!empty($notifications)): ?>

        <?php foreach ($notifications as $notification): ?>

            <?php
                $isUnread = ($notification['status'] ?? '') === 'Unread';

                $notificationDate = 'Date unavailable';

                if (!empty($notification['date'])) {
                    $time = new \DateTime(
                        $notification['date'],
                        new \DateTimeZone('UTC')
                    );

                    $time->setTimezone(
                        new \DateTimeZone('Asia/Manila')
                    );

                    $notificationDate = $time->format('F d, Y - h:i A');
                }

                $openUrl = site_url(
                    'admin/notifications/open/' .
                    $notification['notification_id']
                );
            ?>

            <div
                class="list-group-item notification-item <?= $isUnread ? 'unread' : '' ?>"
                onclick="window.location.href='<?= esc($openUrl) ?>'"
                style="cursor: pointer;"
            >

                <div class="d-flex justify-content-between align-items-start gap-3">

                    <div class="d-flex gap-3 align-items-start">

                        <div class="icon-box bg-success">
                            <i class="bi bi-bell-fill"></i>
                        </div>

                        <div>
                            <h6 class="mb-1">
                                <?= $isUnread ? 'New Notification' : 'Notification' ?>
                            </h6>

                            <p class="mb-1">
                                <?= esc($notification['message']) ?>
                            </p>

                            <small class="text-muted">
                                <?= esc($notificationDate) ?>
                            </small>
                        </div>

                    </div>

                    <span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                        <?= esc($notification['status']) ?>
                    </span>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="list-group-item text-center text-muted">
            No notifications yet.
        </div>

    <?php endif; ?>

</section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
