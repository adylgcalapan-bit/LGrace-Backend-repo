<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Report a Problem | Community Problems Visibility System</title>

    <!-- Bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet CSS -->

    <link rel="stylesheet"
        href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom CSS -->

    <link rel="stylesheet"
        href="<?= base_url('assets/css/report R.css') ?>">

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

                <li class="active">

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

                    <h2>Report a Community Problem</h2>

                    <p>Submit a concern to Barangay Saguing.</p>

                </div>

                <a href="<?= base_url('resident/dashboard') ?>" class="btn btn-outline-secondary back-link">

                    <i class="bi bi-arrow-left"></i>

                    Back to Dashboard

                </a>

            </div>

            <!-- Report Form -->

            <div class="card report-card">

                <div class="card-header">

                    <h4>

                        <i class="bi bi-pencil-square"></i>

                        Report Information

                    </h4>

                </div>

                <div class="card-body">

                    <form id="reportForm"
                        action="<?= site_url('resident/report') ?>"
                        method="POST"
                        enctype="multipart/form-data">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Report Title

                                </label>

                                <input type="text"
                                    class="form-control"
                                    id="title"
                                    name="title"
                                    placeholder="Enter report title">
                                <div class="invalid-feedback" id="titleError"></div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Category

                                </label>

                                <select class="form-select"
                                    id="category"
                                    name="category_id">

                                    <option value="" selected disabled>
                                        Select Category
                                    </option>

                                    <?php foreach (($categories ?? []) as $category): ?>

                                        <option value="<?= (int) $category['category_id'] ?>">
                                            <?= esc($category['category_name']) ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="categoryError"></div>

                            </div>
                            <div class="col-12 mb-3">

                                <label class="form-label">

                                    Description

                                </label>

                                <textarea class="form-control"
                                    id="description"
                                    name="description"
                                    rows="5"
                                    placeholder="Describe the community problem in detail..."></textarea>
                                <div class="invalid-feedback" id="descriptionError"></div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Date of Incident

                                </label>

                                <input type="date"
                                    class="form-control"
                                    id="incidentDate">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Upload Photo

                                </label>

                                <input type="file"
                                    class="form-control"
                                    id="photo"
                                    name="photo"
                                    accept="image/jpeg,image/png,image/webp">

                                <small class="text-muted">

                                    Upload a clear photo of the reported issue.

                                </small>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Anonymous Report

                                </label>

                                <div class="form-check mt-2">

                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="anonymous"
                                        name="is_anonymous"
                                        value="1">


                                    <label class="form-check-label"
                                        for="anonymous">

                                        Submit this report anonymously

                                    </label>

                                </div>

                                <small class="text-muted">

                                    Your identity will be hidden from public display.

                                </small>

                            </div>

                            <div class="col-12 mb-4">

                                <label class="form-label">

                                    Pin the Problem Location

                                </label>

                                <div class="d-flex flex-wrap gap-2 mb-2">

                                    <button type="button" class="btn btn-outline-success btn-sm" id="useLocationBtn">

                                        <i class="bi bi-geo-alt-fill"></i>

                                        Use My Current Location

                                    </button>

                                </div>

                                <div id="locationStatus" class="text-muted small mb-2">

                                    Click the button to use your current location or click on the map to choose a location.

                                </div>

                                <div id="map"></div>
                                <div class="invalid-feedback" id="locationError"></div>

                                <small class="text-muted">

                                    Click on the map to select the exact location of the reported problem.

                                </small>

                            </div>

                            <!-- Hidden Coordinates -->

                            <input type="hidden"
                                id="latitude"
                                name="latitude">

                            <input type="hidden"
                                id="longitude"
                                name="longitude">
                            <div class="mt-3">
                                <label for="address" class="form-label">
                                    Address of Report Location
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="address"
                                    name="address"
                                    placeholder="Address will appear after selecting a location"
                                    readonly>
                            </div>

                            <div class="col-12 text-end">

                                <div id="formSuccess" class="form-feedback success-feedback" role="status"></div>

                                <button type="reset"
                                    class="btn btn-secondary">

                                    <i class="bi bi-arrow-clockwise"></i>

                                    Reset

                                </button>

                                <button type="submit"
                                    class="btn btn-success">

                                    <i class="bi bi-send-fill"></i>

                                    Submit Report

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

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

    <!-- Custom JS -->

    <script defer src="<?= base_url('assets/js/report R.js') ?>"></script>

</body>

</html>