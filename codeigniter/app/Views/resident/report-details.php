<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Report Details | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet CSS -->

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- Custom CSS -->

    <link rel="stylesheet"
          href="<?= base_url('assets/css/report-details.css') ?>">

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

            <h2>Report Details</h2>

            <p>View the complete information of your submitted report.</p>

        </div>

        <div class="card details-card">

            <div class="card-header">

                <h4>

                    <i class="bi bi-file-earmark-text-fill"></i>

                    Report Information

                </h4>

                <p class="mb-0 mt-2 text-white-50">
                    Report ID: <span id="report-id">RPT-001</span>
                </p>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Report Title

                        </label>

                        <input type="text"
                               id="report-title"
                               class="form-control"
                               value="Road Damage near Barangay Hall"
                               readonly>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Category

                        </label>

                        <input type="text"
                               id="report-category"
                               class="form-control"
                               value="Infrastructure"
                               readonly>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Date Submitted

                        </label>

                        <input type="text"
                               id="report-date"
                               class="form-control"
                               value="July 28, 2026"
                               readonly>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Current Status

                        </label>

                        <input type="text"
                               id="report-status"
                               class="form-control"
                               value="In Progress"
                               readonly>

                    </div>
                                        <div class="col-12 mb-4">

                        <label class="form-label">

                            Description

                        </label>

                        <textarea id="report-description"
                                  class="form-control"
                                  rows="5"
                                  readonly>

Large potholes were found near the Barangay Hall entrance. Vehicles and motorcycles have difficulty passing through the area, especially during rainy days. Immediate repair is requested to prevent accidents.

                        </textarea>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Uploaded Photo

                        </label>

                        <div class="image-container">

                            <img src="<?= base_url('assets/images/resident picture.jpg') ?>"
                                 alt="Report Image"
                                 class="img-fluid rounded">

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Report Location

                        </label>

                        <input type="text"
                               id="report-location"
                               class="form-control mb-3"
                               value="Barangay Saguing, Kidapawan City"
                               readonly>

                        <div id="report-map"></div>

                    </div>

                    <div class="col-12 mb-4">

                        <label class="form-label">

                            Administrator Feedback

                        </label>

                        <div class="feedback-box">

                            <i class="bi bi-chat-left-text-fill"></i>

                            <p>

                                Your report has been verified by the Barangay Administrator.
                                It has already been forwarded to the Municipal Engineering Office
                                for road inspection and repair scheduling.

                            </p>

                        </div>

                    </div>

                    <div class="col-12">

                        <label class="form-label">

                            Report Timeline

                        </label>

                        <ul class="timeline">

                            <li>

                                <span class="timeline-icon bg-success">

                                    <i class="bi bi-check-circle-fill"></i>

                                </span>

                                <div>

                                    <strong>Report Submitted</strong>

                                    <p>July 28, 2026 - 9:30 AM</p>

                                </div>

                            </li>

                            <li>

                                <span class="timeline-icon bg-primary">

                                    <i class="bi bi-search"></i>

                                </span>

                                <div>

                                    <strong>Under Review</strong>

                                    <p>July 28, 2026 - 11:15 AM</p>

                                </div>

                            </li>

                            <li>

                                <span class="timeline-icon bg-warning">

                                    <i class="bi bi-tools"></i>

                                </span>

                                <div>

                                    <strong>In Progress</strong>

                                    <p>July 29, 2026 - 8:00 AM</p>

                                </div>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>
                <!-- Action Buttons -->

        <div class="mt-4 d-flex justify-content-between">

            <a href="<?= base_url('resident/my-reports') ?>"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left-circle"></i>

                Back to My Reports

            </a>

            <button id="print-report"
                    class="btn btn-success"
                    type="button">

                <i class="bi bi-printer-fill"></i>

                Print Report

            </button>

        </div>

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

<!-- Leaflet JS -->

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Custom JavaScript -->

<script src="<?= base_url('assets/js/report-details.js') ?>"></script>

</body>

</html>
