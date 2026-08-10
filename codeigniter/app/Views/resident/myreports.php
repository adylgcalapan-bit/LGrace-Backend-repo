<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Reports | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/myreport.css') ?>">

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

            <li class="active">

                <a href="<?= base_url('resident/my-reports') ?>">

                    <i class="bi bi-file-earmark-text"></i>

                    My Reports

                </a>

            </li>

            <li>

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

                <h2>My Reports</h2>

                <p>Track the status of your submitted community reports.</p>

            </div>

        </div>

        <!-- Search -->

        <div class="card search-card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8">

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search report title...">

                    </div>

                    <div class="col-md-4">

                        <select class="form-select">

                            <option selected>All Status</option>
                            <option>Pending</option>
                            <option>In Progress</option>
                            <option>Resolved</option>
                            <option>Rejected</option>

                        </select>

                    </div>

                </div>

            </div>

        </div>

        <!-- Reports Table -->

        <div class="card reports-card mt-4">

            <div class="card-header">

                <h4>

                    <i class="bi bi-clock-history"></i>

                    Report History

                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>Title</th>

                                <th>Category</th>

                                <th>Date</th>

                                <th>Status</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

<?php if (!empty($reports)): ?>

    <?php foreach ($reports as $report): ?>

        <?php
            $status = $report['status'];

            $badgeClass = match ($status) {
                'Pending'     => 'bg-warning text-dark',
                'In Progress' => 'bg-primary',
                'Resolved'    => 'bg-success',
                'Rejected'    => 'bg-danger',
                default       => 'bg-secondary',
            };
        ?>

        <tr>

            <td>
                <?= esc($report['title']) ?>
            </td>

            <td>
                <?= esc($report['category_name'] ?? 'No Category') ?>
            </td>

            <td>
                <?= date('F d, Y', strtotime($report['date_reported'])) ?>
            </td>

            <td>
                <span class="badge <?= $badgeClass ?>">
                    <?= esc($status) ?>
                </span>
            </td>

            <td>
                <a href="<?= base_url('resident/report-details') ?>"
                   class="btn btn-outline-success btn-sm">

                    <i class="bi bi-eye-fill"></i>
                    View

                </a>
            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="5" class="text-center text-muted">
            No reports submitted yet.
        </td>
    </tr>

<?php endif; ?>

</tbody>

                    </table>

                </div>

            </div>

        </div>

                <!-- Summary Cards -->

        <section class="mt-4">

            <div class="row">

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-file-earmark-text-fill text-success fs-1"></i>

                            <h5 class="mt-3">Total Reports</h5>

                            <h2>8</h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-hourglass-split text-warning fs-1"></i>

                            <h5 class="mt-3">Pending</h5>

                            <h2>2</h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-arrow-repeat text-primary fs-1"></i>

                            <h5 class="mt-3">In Progress</h5>

                            <h2>1</h2>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="card text-center shadow-sm">

                        <div class="card-body">

                            <i class="bi bi-check-circle-fill text-success fs-1"></i>

                            <h5 class="mt-3">Resolved</h5>

                            <h2>5</h2>

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

<!-- Custom JS -->

<script defer src="<?= base_url('assets/js/myreport.js') ?>"></script>

</body>

</html>
