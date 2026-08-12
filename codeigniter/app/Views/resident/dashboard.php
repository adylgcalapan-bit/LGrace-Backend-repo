<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resident Dashboard | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-resident.css') ?>">
</head>

<body>

    <div class="wrapper">

        <!-- ================= Sidebar ================= -->

        <aside class="sidebar">

            <div class="logo">

                <i class="bi bi-geo-alt-fill"></i>

                <h4>Community Visibility System</h4>

            </div>

            <ul class="menu">

                <li class="active">
                    <a href="#">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('resident/report') ?>">
                        <i class="bi bi-pencil-square"></i>
                        <span>Report a Problem</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('resident/my-reports') ?>">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>My Reports</span>
                    </a>
                </li>

                <?= view('resident/notification_menu') ?>

                <li>
                    <a href="<?= base_url('resident/profile') ?>">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li class="logout">
                    <a href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>

        </aside>

        <!-- ================= Main Content ================= -->

        <main class="main-content">

            <!-- Top Navigation -->

            <div class="topbar">

                <div>

                    <h2>Resident Dashboard</h2>

                    <p>Welcome to the Community Problems Visibility System</p>

                </div>

                <div class="resident-info">

                    <i class="bi bi-person-circle"></i>

                    <div>

                        <h6 class="mb-0">Juan Dela Cruz</h6>

                        <small>Resident</small>

                    </div>

                </div>

            </div>

            <!-- Welcome Banner -->

            <section class="welcome-banner">

                <div class="banner-text">

                    <h3>Hello, Juan Dela Cruz! 👋</h3>

                    <p>
                        Welcome back! You can report community concerns,
                        monitor the status of your reports,
                        and receive updates from the barangay administrator.
                    </p>

                    <a href="<?= base_url('resident/report') ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i>
                        Report a Problem
                    </a>

                    <a href="<?= base_url('resident/report-details') ?>" class="btn btn-outline-success ms-2">
                        <i class="bi bi-eye-fill"></i>
                        View Report Details
                    </a>

                </div>

            </section>

            <!-- Summary Cards -->

            <section class="summary-cards">

                <div class="row">
                    <!-- Pending -->
                    <div class="col-lg-3 col-md-6 mb-4">

                        <div class="card summary-card pending">

                            <div class="card-body">

                                <i class="bi bi-hourglass-split card-icon"></i>

                                <h5>Pending</h5>

                                <h2><?= (int) ($pendingReports ?? 0) ?></h2>

                                <p>Reports Waiting</p>

                            </div>

                        </div>

                    </div>

                    <!-- In Progress -->
                    <div class="col-lg-3 col-md-6 mb-4">

                        <div class="card summary-card progress-card">

                            <div class="card-body">

                                <i class="bi bi-arrow-repeat card-icon"></i>

                                <h5>In Progress</h5>

                                <h2><?= (int) ($progressReports ?? 0) ?></h2>

                                <p>Being Processed</p>

                            </div>

                        </div>

                    </div>

                    <!-- Resolved -->
                    <div class="col-lg-3 col-md-6 mb-4">

                        <div class="card summary-card resolved">

                            <div class="card-body">

                                <i class="bi bi-check-circle-fill card-icon"></i>

                                <h5>Resolved</h5>

                                <h2><?= (int) ($resolvedReports ?? 0) ?></h2>

                                <p>Completed Reports</p>

                            </div>

                        </div>

                    </div>

                    <!-- Total -->
                    <div class="col-lg-3 col-md-6 mb-4">

                        <div class="card summary-card total">

                            <div class="card-body">

                                <i class="bi bi-file-earmark-text-fill card-icon"></i>

                                <h5>Total Reports</h5>

                                <h2><?= (int) ($totalReports ?? 0) ?></h2>

                            </div>

            </section>

            <!-- Recent Reports -->

            <section class="recent-reports">

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h4 class="mb-0">

                            <i class="bi bi-clock-history"></i>

                            Recent Reports

                        </h4>

                        <a href="<?= base_url('resident/my-reports') ?>" class="btn btn-success btn-sm">

                            View All

                        </a>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead>

                                    <tr>

                                        <th>Report Title</th>

                                        <th>Category</th>

                                        <th>Date Submitted</th>

                                        <th>Status</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (!empty($recentReports)): ?>

                                        <?php foreach ($recentReports as $report): ?>

                                            <?php
                                            $status = $report['status'] ?? 'Pending';

                                            $badgeClass = match ($status) {
                                                'Pending'     => 'bg-warning text-dark',
                                                'In Progress' => 'bg-primary',
                                                'Resolved'    => 'bg-success',
                                                'Rejected'    => 'bg-danger',
                                                default       => 'bg-secondary'
                                            };
                                            ?>

                                            <tr>

                                                <td>
                                                    <?= esc($report['title'] ?? 'Untitled Report') ?>
                                                </td>

                                                <td>
                                                    <?= esc($report['category_name'] ?? 'Uncategorized') ?>
                                                </td>

                                                <td>
                                                    <?= !empty($report['date_reported'])
                                                        ? date('F d, Y', strtotime($report['date_reported']))
                                                        : 'N/A' ?>
                                                </td>

                                                <td>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= esc($status) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <a
                                                        href="<?= site_url('resident/report-details/' . $report['report_id']) ?>"
                                                        class="btn btn-outline-success btn-sm">
                                                        View
                                                    </a>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                No reports found.
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </section>
            <!-- Quick Tips -->

            <section class="mt-4">

                <div class="card">

                    <div class="card-header">

                        <h5 class="mb-0">

                            <i class="bi bi-lightbulb-fill text-warning"></i>

                            Quick Reminders

                        </h5>

                    </div>

                    <div class="card-body">

                        <ul class="mb-0">

                            <li>
                                Provide a clear and accurate report title.
                            </li>

                            <li>
                                Upload a photo as supporting evidence whenever possible.
                            </li>

                            <li>
                                Pin the exact location of the reported concern.
                            </li>

                            <li>
                                You can monitor your report status anytime through
                                <strong>My Reports</strong>.
                            </li>

                        </ul>

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

    <!-- Resident Dashboard JS -->

    <script defer src="<?= base_url('assets/js/dashboard-resident.js') ?>"></script>

</body>

</html>