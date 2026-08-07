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

            <li class="active">

                <a href="<?= base_url('resident/notifications') ?>">

                    <i class="bi bi-bell-fill"></i>

                    Notifications

                </a>

            </li>

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

                <h4>

                    <i class="bi bi-bell-fill"></i>

                    Recent Notifications

                </h4>

            </div>

            <div class="card-body">
                                <!-- Notification 1 -->

                <div class="notification-item unread">

                    <div class="notification-icon bg-success">

                        <i class="bi bi-check-circle-fill"></i>

                    </div>

                    <div class="notification-content">

                        <h5>Report Submitted Successfully</h5>

                        <p>
                            Your report <strong>"Road Damage"</strong> has been
                            successfully submitted and is waiting for review.
                        </p>

                        <small class="text-muted">

                            July 28, 2026 • 9:30 AM

                        </small>

                    </div>

                    <button class="btn btn-outline-success btn-sm">

                        Mark as Read

                    </button>

                </div>

                <hr>

                <!-- Notification 2 -->

                <div class="notification-item">

                    <div class="notification-icon bg-primary">

                        <i class="bi bi-arrow-repeat"></i>

                    </div>

                    <div class="notification-content">

                        <h5>Report Status Updated</h5>

                        <p>

                            Your report
                            <strong>"Garbage Collection"</strong>
                            is now
                            <strong>In Progress.</strong>

                        </p>

                        <small class="text-muted">

                            July 27, 2026 • 2:15 PM

                        </small>

                    </div>

                    <button class="btn btn-outline-success btn-sm">

                        Mark as Read

                    </button>

                </div>

                <hr>

                <!-- Notification 3 -->

                <div class="notification-item">

                    <div class="notification-icon bg-success">

                        <i class="bi bi-check2-all"></i>

                    </div>

                    <div class="notification-content">

                        <h5>Report Resolved</h5>

                        <p>

                            Your report
                            <strong>"Broken Streetlight"</strong>
                            has been marked as
                            <strong>Resolved.</strong>

                        </p>

                        <small class="text-muted">

                            July 25, 2026 • 4:40 PM

                        </small>

                    </div>

                    <button class="btn btn-outline-success btn-sm">

                        Mark as Read

                    </button>

                </div>

                <hr>

                <!-- Notification 4 -->

                <div class="notification-item">

                    <div class="notification-icon bg-danger">

                        <i class="bi bi-x-circle-fill"></i>

                    </div>

                    <div class="notification-content">

                        <h5>Report Rejected</h5>

                        <p>

                            Your report
                            <strong>"Illegal Dumping"</strong>
                            was rejected due to
                            insufficient information.

                        </p>

                        <small class="text-muted">

                            July 22, 2026 • 10:20 AM

                        </small>

                    </div>

                    <button class="btn btn-outline-success btn-sm">

                        Mark as Read

                    </button>

                </div>

                <hr>

                <!-- Notification 5 -->

                <div class="notification-item">

                    <div class="notification-icon bg-warning">

                        <i class="bi bi-chat-left-text-fill"></i>

                    </div>

                    <div class="notification-content">

                        <h5>Administrator Feedback</h5>

                        <p>

                            The Barangay Administrator requested additional
                            information regarding your submitted report.

                        </p>

                        <small class="text-muted">

                            July 21, 2026 • 8:15 AM

                        </small>

                    </div>

                    <button class="btn btn-outline-success btn-sm">

                        Mark as Read

                    </button>

                </div>

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

                            <h2>12</h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-envelope-fill text-warning fs-1"></i>

                            <h5 class="mt-3">Unread</h5>

                            <h2>3</h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-4 col-md-12 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-check2-circle text-primary fs-1"></i>

                            <h5 class="mt-3">Read</h5>

                            <h2>9</h2>

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
