<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Notifications | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->

    <link rel="stylesheet"
          href="<?= base_url('assets/css/notification.css') ?>">

</head>

<body>

<div class="wrapper">

    <!-- Sidebar -->

    <aside class="sidebar">

        <div class="logo">

            <i class="bi bi-geo-alt-fill"></i>

            <h4>Community Visibility System</h4>

        </div>

        <ul class="menu">

            <li>

                <a href="<?= base_url('resident/dashboard') ?>">

                    <i class="bi bi-house-door-fill"></i>

                    Dashboard

                </a>

            </li>

            <li>

                <a href="<?= base_url('resident/report') ?>">

                    <i class="bi bi-pencil-square"></i>

                    Report a Problem

                </a>

            </li>

            <li>

                <a href="<?= base_url('resident/my-reports') ?>">

                    <i class="bi bi-file-earmark-text"></i>

                    My Reports

                </a>

            </li>

           <?= view('resident/notification_menu', [
    'notificationActive' => true
]) ?>
            <li>

                <a href="<?= base_url('resident/profile') ?>">

                    <i class="bi bi-person-circle"></i>

                    My Profile

                </a>

            </li>

            <li class="logout">

                <a href="<?= base_url('login') ?>">

                    <i class="bi bi-box-arrow-right"></i>

                    Logout

                </a>

            </li>

        </ul>

    </aside>

    <!-- Main Content -->

    <main class="main-content">

        <div class="topbar">

            <div>

                <h2>Notifications</h2>
              

                <p>Stay updated with your report status and administrator feedback.</p>

            </div>

        </div>

   
   
        <!-- Notification List -->

<div class="card notification-card">

    <div class="card-header">

        <h4 class="mb-0">
            <i class="bi bi-bell-fill"></i>
            Recent Notifications
        </h4>

    </div>

    <div class="card-body">

        <?php if (!empty($notifications)): ?>

            <?php foreach ($notifications as $notification): ?>

                <?php
                    $isUnread =
                        ($notification['status'] ?? '') === 'Unread';

                    $notificationDate = 'Date unavailable';

                    if (!empty($notification['date'])) {

                        $time = new \DateTime(
                            $notification['date'],
                            new \DateTimeZone('UTC')
                        );

                        $time->setTimezone(
                            new \DateTimeZone('Asia/Manila')
                        );

                        $notificationDate =
                            $time->format('F d, Y - h:i A');
                    }
                ?>

                <div
    class="notification-item <?= $isUnread ? 'unread' : '' ?>"
    onclick="window.location.href='<?= site_url('resident/notifications/open/' . $notification['notification_id']) ?>'"
    style="cursor: pointer;"
>
                    <div class="notification-icon bg-primary">
                        <i class="bi bi-bell-fill"></i>
                    </div>

                    <div class="notification-content">

                        <h5>
                            <?= $isUnread
                                ? 'New Notification'
                                : 'Notification'
                            ?>
                        </h5>

                        <p>
                            <?= esc($notification['message']) ?>
                        </p>

                        <small class="text-muted">
                            <?= esc($notificationDate) ?>
                        </small>

                    </div>

                    <div class="ms-auto">

                        <span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                            <?= esc($notification['status']) ?>
                        </span>

                    </div>

                </div>

                <hr>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center text-muted py-4">
                No notifications yet.
            </div>

        <?php endif; ?>

    </div>

</div>
                <!-- Notification Summary -->

        <section class="mt-4">

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-bell-fill text-success fs-1"></i>

                            <h5 class="mt-3">Total Notifications</h5>

                            <h2><?= (int) ($totalCount ?? 0) ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-envelope-fill text-warning fs-1"></i>

                            <h5 class="mt-3">Unread</h5>

                           <h2><?= (int) ($unreadCount ?? 0) ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-12 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-check2-circle text-primary fs-1"></i>

                            <h5 class="mt-3">Read</h5>

                           <h2><?= (int) ($readCount ?? 0) ?></h2>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- Footer -->

        <footer class="footer mt-5">

            <hr>

            <p class="text-center text-muted">

                © 2026 Community Problems Visibility System with Location Feature

                <br>

                Barangay Saguing

            </p>

        </footer>

    </main>

</div>

<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->

<script defer src="<?= base_url('assets/js/notification.js') ?>"></script>

</body>

</html>
