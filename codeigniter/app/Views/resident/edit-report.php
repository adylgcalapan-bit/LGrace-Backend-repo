<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Report | Community Problems Visibility System</title>

    <!-- Bootstrap -->
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
        href="<?= base_url('assets/css/report R.css') ?>">

    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/resident-sidebar.css') ?>">

</head>

<body>

    <?php $report = $report ?? []; ?>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div class="logo">

                <i class="bi bi-geo-alt-fill"></i>

                <h4>
                    Community Visibility System
                </h4>

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


        <!-- MAIN CONTENT -->
        <main class="main-content">

            <div class="topbar">

                <div>

                    <h2>
                        Edit Report
                    </h2>

                    <p>
                        Update your pending community report.
                    </p>

                </div>

                <a
                    href="<?= base_url('resident/dashboard') ?>"
                    class="btn btn-outline-secondary back-link">

                    <i class="bi bi-arrow-left"></i>

                    Back to Dashboard

                </a>

            </div>


            <!-- REPORT FORM -->
            <div class="card report-card">

                <div class="card-header">

                    <h4>

                        <i class="bi bi-pencil-square"></i>

                        Report Information

                    </h4>

                </div>

                <div class="card-body">

                    <form
                        id="reportForm"
                        action="<?= site_url(
                                    'resident/report/edit/' .
                                        $report['report_id']
                                ) ?>"
                        method="POST"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <div class="row">


                            <!-- REPORT TITLE -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Report Title

                                </label>

                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="<?= esc(
                                                old(
                                                    'title',
                                                    $report['title'] ?? ''
                                                )
                                            ) ?>"
                                    placeholder="Enter report title">

                                <div
                                    class="invalid-feedback"
                                    id="titleError">
                                </div>

                            </div>


                            <!-- CATEGORY -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Category

                                </label>

                                <select
                                    class="form-select"
                                    id="category"
                                    name="category_id"
                                    required>

                                    <option
                                        value=""
                                        disabled>

                                        Select category

                                    </option>

                                    <?php
                                    $categories =
                                        $categories ?? [];

                                    $selectedCategory =
                                        (int) old(
                                            'category_id',
                                            $report['category_id'] ?? 0
                                        );
                                    ?>

                                    <?php foreach ($categories as $category): ?>

                                        <option
                                            value="<?= esc(
                                                        $category['category_id']
                                                    ) ?>"
                                            <?= (int) $category['category_id']
                                                === $selectedCategory
                                                ? 'selected'
                                                : '' ?>>

                                            <?= esc(
                                                $category['category_name']
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <div
                                    class="invalid-feedback"
                                    id="categoryError">
                                </div>

                            </div>


                            <!-- DESCRIPTION -->
                            <div class="col-12 mb-3">

                                <label class="form-label">

                                    Description

                                </label>

                                <textarea
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    rows="5"
                                    placeholder="Describe the community problem in detail..."><?= esc(
                                                                                                    old(
                                                                                                        'description',
                                                                                                        $report['description'] ?? ''
                                                                                                    )
                                                                                                ) ?></textarea>

                                <div
                                    class="invalid-feedback"
                                    id="descriptionError">
                                </div>

                            </div>

                            <!-- DATE OF INCIDENT -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Date of Incident
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="incidentDate"
                                    name="incident_date"
                                    value="<?= esc(
                                                old(
                                                    'incident_date',
                                                    $report['incident_date'] ?? ''
                                                )
                                            ) ?>"
                                    max="<?= date('Y-m-d') ?>"
                                    required>

                            </div>

                            <!-- PHOTO -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Upload Photos
                                </label>

                                <?php $images = $images ?? []; ?>

                                <?php if (!empty($images)): ?>

                                    <div class="mb-3">

                                        <label class="form-label">
                                            Current Photos
                                        </label>

                                        <div class="d-flex flex-wrap gap-2">

                                            <?php foreach ($images as $image): ?>

                                                <?php
                                                $imagePath = trim(
                                                    (string) ($image['image_path'] ?? '')
                                                );
                                                ?>

                                                <?php if ($imagePath !== ''): ?>

                                                    <img
                                                        src="<?= esc(
                                                                    base_url(
                                                                        ltrim($imagePath, '/\\')
                                                                    )
                                                                ) ?>"
                                                        alt="Current Report Photo"
                                                        class="img-fluid rounded"
                                                        style="
                                width: 120px;
                                height: 100px;
                                object-fit: cover;
                            ">

                                                <?php endif; ?>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                <?php endif; ?>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="photos"
                                    name="photos[]"
                                    accept="image/jpeg,image/png,image/webp"
                                    multiple>

                                <div
                                    class="invalid-feedback"
                                    id="photosError">
                                </div>

                                <small
                                    class="text-muted d-block mt-2"
                                    id="photoCount">
                                    0 of 5 photos selected
                                </small>

                                <small class="text-muted">
                                    Optional. You may select up to 5 replacement photos.
                                    JPG, JPEG, PNG, or WebP only.
                                    Maximum 5 MB per photo.
                                    If you do not select new photos, the current photos will remain.
                                </small>

                            </div>


                            <!-- ANONYMOUS REPORT -->

                            <?php

                            $isAnonymousChecked =
                                old('anonymous_form_present') !== null
                                ? (string) old(
                                    'is_anonymous'
                                ) === '1'
                                : (int) (
                                    $report['is_anonymous'] ?? 0
                                ) === 1;

                            ?>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Anonymous Report

                                </label>


                                <!--
                                    Used so an unchecked checkbox
                                    can still be remembered after
                                    validation errors.
                                -->
                                <input
                                    type="hidden"
                                    name="anonymous_form_present"
                                    value="1">


                                <div class="form-check mt-2">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_anonymous"
                                        name="is_anonymous"
                                        value="1"
                                        <?= $isAnonymousChecked
                                            ? 'checked'
                                            : '' ?>>

                                    <label
                                        class="form-check-label"
                                        for="is_anonymous">

                                        Submit this report anonymously

                                    </label>

                                </div>


                                <small class="text-muted">

                                    Your identity will be hidden from public and administrative report displays.

                                </small>

                            </div>


                            <!-- LOCATION -->
                            <div class="col-12 mb-4">

                                <label class="form-label">

                                    Pin the Problem Location

                                </label>


                                <div class="d-flex flex-wrap gap-2 mb-2">

                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-sm"
                                        id="useLocationBtn">

                                        <i class="bi bi-geo-alt-fill"></i>

                                        Use My Current Location

                                    </button>

                                </div>


                                <div
                                    id="locationStatus"
                                    class="text-muted small mb-2">

                                    Click the button to use your current location or click on the map to choose a location.

                                </div>


                                <div id="map"></div>


                                <div
                                    class="invalid-feedback"
                                    id="locationError">
                                </div>


                                <small class="text-muted">

                                    Click on the map to select the exact location of the reported problem.

                                </small>


                                <!-- HIDDEN COORDINATES -->

                                <input
                                    type="hidden"
                                    id="latitude"
                                    name="latitude"
                                    value="<?= esc(
                                                old(
                                                    'latitude',
                                                    $report['latitude'] ?? ''
                                                )
                                            ) ?>">

                                <input
                                    type="hidden"
                                    id="longitude"
                                    name="longitude"
                                    value="<?= esc(
                                                old(
                                                    'longitude',
                                                    $report['longtitude'] ?? ''
                                                )
                                            ) ?>">


                                <!-- ADDRESS -->
                                <div class="mt-3">

                                    <label
                                        for="address"
                                        class="form-label">

                                        Address of Report Location

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="address"
                                        name="address"
                                        value="<?= esc(
                                                    old(
                                                        'address',
                                                        $report['address'] ?? ''
                                                    )
                                                ) ?>"
                                        placeholder="Address will appear after selecting a location"
                                        readonly>

                                </div>


                                <!-- PUROK OF REPORT LOCATION -->
                                <div class="mt-3">

                                    <label
                                        for="reportPurok"
                                        class="form-label">

                                        Purok of Report Location

                                    </label>

                                    <?php
                                    $selectedReportPurokId = (string) old(
                                        'purok_id',
                                        $report['purok_id'] ?? ''
                                    );
                                    ?>

                                    <select
                                        class="form-select"
                                        id="reportPurok"
                                        name="purok_id"
                                        required>

                                        <option
                                            value=""
                                            disabled
                                            <?= $selectedReportPurokId === '' ? 'selected' : '' ?>>

                                            Pin a location to identify the Purok

                                        </option>

                                        <?php foreach (($puroks ?? []) as $purok): ?>

                                            <option
                                                value="<?= (int) $purok['purok_id'] ?>"
                                                data-latitude="<?= esc($purok['latitude']) ?>"
                                                data-longitude="<?= esc($purok['longitude']) ?>"
                                                <?= $selectedReportPurokId === (string) $purok['purok_id']
                                                    ? 'selected'
                                                    : '' ?>>

                                                <?= esc($purok['purok_name']) ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                    <small class="text-muted">
                                        This is the Purok where the reported problem is located, not the resident's home Purok.
                                    </small>

                                </div>


                                <!-- FORM BUTTONS -->
                                <div class="col-12 text-end mt-3">

                                    <div
                                        id="formSuccess"
                                        class="form-feedback success-feedback"
                                        role="status">
                                    </div>


                                    <button
                                        type="reset"
                                        class="btn btn-secondary">

                                        <i class="bi bi-arrow-clockwise"></i>

                                        Reset

                                    </button>


                                    <button
                                        type="submit"
                                        class="btn btn-success">

                                        <i class="bi bi-save-fill"></i>

                                        Save Changes

                                    </button>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- FOOTER -->
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


    <!-- Custom JS -->
    <script
        defer
        src="<?= base_url('assets/js/report R.js') ?>">
    </script>

</body>

</html>