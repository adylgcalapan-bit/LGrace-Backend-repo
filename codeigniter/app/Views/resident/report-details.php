<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Report Details | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/report-details.css') ?>">

</head>

<body>

<div class="wrapper">

    <!-- =========================
         SIDEBAR
    ========================== -->
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
                <a href="<?= base_url('login') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>

        </ul>

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">

            <h2>Report Details</h2>

            <p>
                View the complete information of your submitted report.
            </p>

        </div>


        <!-- =========================
             REPORT DETAILS CARD
        ========================== -->
        <div class="card details-card">

            <!-- Card Header -->
            <div class="card-header">

                <h4>
                    <i class="bi bi-file-earmark-text-fill"></i>
                    Report Information
                </h4>

                <p class="mb-0 mt-2 text-white-50">
                    Report ID:
                    <span id="report-id">
                        #<?= esc($report['report_id']) ?>
                    </span>
                </p>

            </div>


            <!-- Card Body -->
            <div class="card-body">

                <div class="row">

                    <!-- REPORT TITLE -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Report Title
                        </label>

                        <input
                            type="text"
                            id="report-title"
                            class="form-control"
                            value="<?= esc($report['title'] ?? '') ?>"
                            readonly>

                    </div>


                    <!-- CATEGORY -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Category
                        </label>

                        <input
                            type="text"
                            id="report-category"
                            class="form-control"
                            value="<?= esc($report['category_name'] ?? 'No Category') ?>"
                            readonly>

                    </div>


                    <!-- DATE -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Date Submitted
                        </label>

                        <input
                            type="text"
                            id="report-date"
                            class="form-control"
                            value="<?= !empty($report['date_reported'])
                                ? date(
                                    'F d, Y',
                                    strtotime($report['date_reported'])
                                )
                                : '' ?>"
                            readonly>

                    </div>


                    <!-- STATUS -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Current Status
                        </label>

                        <input
                            type="text"
                            id="report-status"
                            class="form-control"
                            value="<?= esc($report['status'] ?? '') ?>"
                            readonly>

                    </div>


                    <!-- DESCRIPTION -->
                    <div class="col-12 mb-4">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            id="report-description"
                            class="form-control"
                            rows="5"
                            readonly><?= esc($report['description'] ?? '') ?></textarea>

                    </div>


                    <!-- PHOTO -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Uploaded Photo
                        </label>

                        <div class="image-container">

                            <?php if (!empty($report['image_path'])): ?>

                                <img
                                    src="<?= base_url($report['image_path']) ?>"
                                    alt="Report Image"
                                    class="img-fluid rounded"
                                    style="
                                        width: 100%;
                                        max-height: 300px;
                                        object-fit: cover;
                                    ">

                            <?php else: ?>

                                <p class="text-muted">
                                    No photo available.
                                </p>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- LOCATION -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Report Location
                        </label>

                        <input
                            type="text"
                            id="report-location"
                            class="form-control mb-3"
                            value="<?= esc(
                                ($report['latitude'] ?? '') .
                                ', ' .
                                ($report['longtitude'] ?? '')
                            ) ?>"
                            readonly>


                        <label class="form-label">
                            Address
                        </label>

                        <input
                            type="text"
                            id="report-address"
                            class="form-control mb-3"
                            value="<?= esc(
                                !empty($report['address'])
                                    ? $report['address']
                                    : 'No address available'
                            ) ?>"
                            readonly>


                        <div
                            id="report-map"
                            data-latitude="<?= esc($report['latitude'] ?? '') ?>"
                            data-longitude="<?= esc($report['longtitude'] ?? '') ?>">
                        </div>

                    </div>


                    <!-- ADMINISTRATOR FEEDBACK -->
                    <div class="col-12 mb-4">

                        <label class="form-label">
                            Administrator Feedback
                        </label>

                        <div class="feedback-box">

                            <p class="text-muted mb-0">
                                No administrator feedback yet.
                            </p>

                        </div>

                    </div>


                    <!-- REPORT TIMELINE -->
                    <div class="col-12 mb-4">

                        <label class="form-label">
                            Report Timeline
                        </label>

                        <ul class="timeline">

                            <!-- Report Submitted -->
                            <li>

                                <span class="timeline-icon bg-success">

                                    <i class="bi bi-check-circle-fill"></i>

                                </span>

                                <div>

                                    <strong>
                                        Report Submitted
                                    </strong>

                                    <p class="mb-0">

                                        <?php if (!empty($report['date_reported'])): ?>

                                            <?php
                                                $reportedTime = new \DateTime(
                                                    $report['date_reported'],
                                                    new \DateTimeZone('UTC')
                                                );

                                                $reportedTime->setTimezone(
                                                    new \DateTimeZone(
                                                        'Asia/Manila'
                                                    )
                                                );
                                            ?>

                                            <?= $reportedTime->format(
                                                'F d, Y - h:i A'
                                            ) ?>

                                        <?php else: ?>

                                            Date unavailable

                                        <?php endif; ?>

                                    </p>

                                </div>

                            </li>


                            <!-- Current Status -->
                            <li>

                                <span class="timeline-icon bg-warning">

                                    <i class="bi bi-info-circle-fill"></i>

                                </span>

                                <div>

                                    <strong>
                                        Current Status
                                    </strong>

                                    <p class="mb-0">
                                        <?= esc(
                                            $report['status'] ?? 'Pending'
                                        ) ?>
                                    </p>

                                </div>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================
             BACK BUTTON
             Pinakaubos sa report details
        ========================== -->
        <div class="mt-4 mb-4">

            <a
                href="<?= site_url('resident/my-reports') ?>"
                class="btn btn-secondary">

                <i class="bi bi-arrow-left-circle"></i>

                Back to My Reports

            </a>

        </div>


        <!-- =========================
             FOOTER
        ========================== -->
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
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- Leaflet JS -->
<script
    src="https://unpkg.com/leaflet/dist/leaflet.js">
</script>

<!-- Custom JavaScript -->
<script
    src="<?= base_url('assets/js/report-details.js') ?>">
</script>

</body>

</html>