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

                <?= view('resident/notification_menu') ?>

                <li>

                    <a href="<?= base_url('resident/profile') ?>">

                        <i class="bi bi-person-circle"></i>

                        My Profile

                    </a>

                </li>

                <li class="logout">

                    <a href="<?= base_url('logout') ?>">

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

                    <p>
                        Track the status of your submitted community reports.
                    </p>
                </div>

            </div>




            <!-- Error Message -->
            <?php if ($errorMessage = session()->getFlashdata('error')): ?>

                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <?= esc($errorMessage) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                    </button>

                </div>

            <?php endif; ?>


            <!-- Search -->

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
                                    <th>Photo</th>
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

                                            <!-- Title -->
                                            <td>
                                                <?= esc($report['title']) ?>
                                            </td>

                                            <!-- Category -->
                                            <td>
                                                <?= esc($report['category_name'] ?? 'No Category') ?>
                                            </td>

                                            <!-- Date -->
                                            <td>
                                                <?= date('F d, Y', strtotime($report['date_reported'])) ?>
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= esc($status) ?>
                                                </span>
                                            </td>

                                            <!-- Photo -->
                                            <td>
                                                <?php if (!empty($report['image_path'])): ?>

                                                    <img
                                                        src="<?= base_url($report['image_path']) ?>"
                                                        alt="Report Photo"
                                                        style=" width: 80px; height: 60px;object-fit: cover; border-radius: 8px;">

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        No photo
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <!-- Action -->
                                            <td>
                                                <a href="<?= site_url('resident/report-details/' . $report['report_id']) ?>"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-eye-fill"></i>
                                                    View
                                                </a>
                                                <?php if (($report['status'] ?? '') === 'Pending'): ?>

                                                    <a href="<?= site_url('resident/report/edit/' . $report['report_id']) ?>"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-pencil-square"></i>
                                                        Edit
                                                    </a>

                                                    <form
                                                        action="<?= site_url('resident/report/delete/' . $report['report_id']) ?>"
                                                        method="post"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this report? This action cannot be undone.');">

                                                        <?= csrf_field() ?>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-outline-danger btn-sm">

                                                            <i class="bi bi-trash-fill"></i>
                                                            Delete

                                                        </button>

                                                    </form>

                                                <?php endif; ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
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
    <?php $successMessage = session()->getFlashdata('success'); ?>

    <?php if ($successMessage): ?>

        <div
            class="modal fade"
            id="successModal"
            tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content text-center">

                    <div class="modal-body p-5">

                        <i
                            class="bi bi-check-circle text-success mb-3"
                            style="font-size: 70px;"></i>

                        <h2 class="fw-bold mt-3 mb-3">
                            Success!
                        </h2>

                        <p class="text-muted mb-4">
                            <?= esc($successMessage) ?>
                        </p>

                        <button
                            type="button"
                            class="btn btn-success px-5"
                            data-bs-dismiss="modal">
                            OK
                        </button>

                    </div>

                </div>

            </div>
        </div>

    <?php endif; ?>
    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <?php if (!empty($successMessage)): ?>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const successModalElement =
                    document.getElementById("successModal");

                if (successModalElement) {
                    const successModal =
                        new bootstrap.Modal(successModalElement);

                    successModal.show();
                }

            });
        </script>

    <?php endif; ?>


    <!-- Custom JS -->

    <script defer src="<?= base_url('assets/js/myreport.js') ?>"></script>

</body>

</html>