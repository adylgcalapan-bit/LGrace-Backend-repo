<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management | Community Problems Visibility System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Report CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/report A.css') ?>">
</head>

<body>

<div class="wrapper">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <div class="logo">
            <i class="bi bi-geo-alt-fill"></i>
            <h4>CPVS</h4>
        </div>

        <ul class="menu">

            <li>
                <a href="<?= base_url('admin/dashboard') ?>">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="active">
                <a href="<?= base_url('admin/reports') ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    Reports
                </a>
            </li>

            <li>
                <a href="<?= base_url('admin/map') ?>">
                    <i class="bi bi-map"></i>
                    Map View
                </a>
            </li>

            <li>
                <a href="<?= base_url('admin/residents') ?>">
                    <i class="bi bi-people"></i>
                    Residents
                </a>
            </li>

            <li>
                <a href="<?= base_url('admin/categories') ?>">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>
            </li>

           <?= view('admin/notification_menu') ?>

            <li>
                <a href="<?= base_url('admin/settings') ?>">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>

            <li>
                <a href="<?= base_url('admin/account') ?>">
                    <i class="bi bi-person-circle"></i>
                    Account / Profile
                </a>
            </li>

            <li class="logout">
                <a href="#">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>

        </ul>

    </aside>

    <!-- ================= MAIN CONTENT ================= -->

    <main class="main-content">

        <!-- HEADER -->

        <header class="topbar">

            <div class="welcome">
                <h2>Reports Management</h2>
                <p>Manage all community problem reports submitted by residents.</p>
            </div>

            <div class="top-actions"></div>

        </header>

        <!-- FILTERS -->

        <div class="card p-4 mb-4 border-0 shadow-sm">

            <div class="row g-3">

                <div class="col-lg-4">
                    <input type="text"
                           class="form-control"
                           placeholder="Search report...">
                </div>

                <div class="col-lg-3">

                    <select class="form-select">

                        <option>All Categories</option>

                        <option>Waste Management Problems</option>

                        <option>Infrastructure and Public Works Issues</option>

                        <option>Environmental and Natural Issues</option>

                        <option>Public Safety and Security Issues</option>

                        <option>Utilities and Public Services</option>

                        <option>Health and Sanitation Issues</option>

                        <option>Social and Community Conflicts</option>

                        <option>Transportation and Road Safety Issues</option>

                        <option>Public Facility Issues</option>

                        <option>Animal Control Issues</option>

                    </select>

                </div>

                <div class="col-lg-3">

                    <select class="form-select">

                        <option>All Status</option>

                        <option>Pending</option>

                        <option>In Progress</option>

                        <option>Resolved</option>

                        <option>Rejected</option>

                    </select>

                </div>

                <div class="col-lg-2 d-grid">

                    <button class="btn btn-success filter-btn">

                        <i class="bi bi-funnel-fill"></i>

                        Filter

                    </button>

                </div>

            </div>

        </div>

        <!-- REPORT TABLE -->

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <h4 class="mb-0">

                    Community Reports

                </h4>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

<thead class="table-success">
    <tr>
        <th>ID</th>
        <th>Resident</th>
        <th>Category</th>
        <th>Location</th>
        <th>Date Reported</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Action</th>
    </tr>
</thead>

<tbody>

<?php if (!empty($reports)): ?>

    <?php foreach ($reports as $report): ?>

        <?php
            $status = $report['status'] ?? 'Pending';

            $badgeClass = match ($status) {
                'Pending'     => 'bg-warning text-dark',
                'In Progress' => 'bg-primary',
                'Resolved'    => 'bg-success',
                'Rejected'    => 'bg-danger',
                default       => 'bg-secondary',
            };

            $location = $report['latitude'] . ', ' . $report['longtitude'];

$address = !empty($report['address'])
    ? $report['address']
    : 'No address available';
        ?>

       <tr
    class="report-row"
    data-id="#<?= esc($report['report_id']) ?>"
    data-resident="<?= esc($report['full_name'] ?? 'Unknown Resident') ?>"
    data-title="<?= esc($report['title'] ?? '') ?>"
    data-category="<?= esc($report['category_name'] ?? 'No Category') ?>"
    data-location="<?= esc($location) ?>"
    data-address="<?= esc($address) ?>"
    data-date="<?= date('F d, Y', strtotime($report['date_reported'])) ?>"
    data-status="<?= esc($status) ?>"
    data-priority="<?= esc($report['priority'] ?? '') ?>"
    data-description="<?= esc($report['description'] ?? '') ?>"
    data-photo="<?= !empty($report['image_path']) ? base_url($report['image_path']) : '' ?>"
>

    <!-- ID -->
    <td>
        #<?= esc($report['report_id']) ?>
    </td>

    <!-- Resident -->
    <td>
        <?= esc($report['full_name'] ?? 'Unknown Resident') ?>
    </td>

    <!-- Category -->
    <td>
        <?= esc($report['category_name'] ?? 'No Category') ?>
    </td>

    <!-- Location -->
    <td>
        <?= esc($location) ?>
    </td>

    <!-- Date Reported -->
    <td>
        <?= date('F d, Y', strtotime($report['date_reported'])) ?>
    </td>

    <!-- Status -->
    <td>
        <span class="badge <?= $badgeClass ?> status-badge">
            <?= esc($status) ?>
        </span>
    </td>

    <!-- Priority -->
<td>
    <?php if (!empty($report['priority'])): ?>

        <?php
            $priorityClass = match ($report['priority']) {
                'High'   => 'bg-danger',
                'Medium' => 'bg-warning text-dark',
                'Low'    => 'bg-success',
                default  => 'bg-secondary',
            };
        ?>

        <span class="badge <?= $priorityClass ?>">
            <?= esc($report['priority']) ?>
        </span>

    <?php else: ?>

        <span class="text-muted">
            Not set
        </span>

    <?php endif; ?>
</td>
  

    <!-- Action -->
   <td>
    <div class="d-flex gap-2">

        <button
            type="button"
            class="btn btn-sm btn-success view-btn">
            <i class="bi bi-eye-fill"></i>
        </button>

        <button
            type="button"
            class="btn btn-sm btn-primary update-status-btn"
            data-report-id="<?= esc($report['report_id']) ?>"
            data-current-status="<?= esc($status) ?>"
            data-current-priority="<?= esc($report['priority'] ?? '') ?>"
            data-bs-toggle="modal"
            data-bs-target="#statusModal">
            <i class="bi bi-pencil-square"></i>
        </button>

    </div>
</td>

</tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="8" class="text-center text-muted">
            No reports available.
        </td>
    </tr>

<?php endif; ?>

</tbody>
                </table>

            </div>

            <div class="card-footer bg-white">

                <nav>

                    <ul class="pagination justify-content-end mb-0">

                        <li class="page-item disabled">
                            <a class="page-link" href="#">Previous</a>
                        </li>

                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>

                    </ul>

                </nav>

            </div>

        </div>
   <!-- ========================================= -->
<!-- REPORT DETAILS MODAL -->
<!-- ========================================= -->

<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Report Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <!-- LEFT SIDE -->
                    <div class="col-md-6">

                        <p>
                            <strong>Report ID:</strong>
                            <span id="reportId"></span>
                        </p>

                        <p>
                            <strong>Resident:</strong>
                            <span id="reportResident"></span>
                        </p>

                        <p>
                            <strong>Title:</strong>
                            <span id="reportTitle"></span>
                        </p>

                        <p>
                            <strong>Category:</strong>
                            <span id="reportCategory"></span>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <span id="reportStatus"></span>
                        </p>

                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-md-6">

                        <p>
                            <strong>Location:</strong>
                            <span id="reportLocation"></span>
                        </p>

                        <p>
                            <strong>Address:</strong>
                            <span id="reportAddress"></span>
                        </p>

                        <p>
                            <strong>Date Reported:</strong>
                            <span id="reportDate"></span>
                        </p>

                        <div class="mb-3">

                            <strong>Photo:</strong>

                            <br>

                            <img
                                id="reportPhoto"
                                src=""
                                alt="Report Photo"
                                class="img-fluid rounded mt-2"
                                style="
                                    width: 100%;
                                    max-width: 300px;
                                    height: 180px;
                                    object-fit: cover;
                                    display: none;
                                ">

                            <span
                                id="reportNoPhoto"
                                class="text-muted"
                                style="display: none;">
                                No photo available
                            </span>

                        </div>

                    </div>

                </div>

                <hr>

                <h6>
                    Description
                </h6>

                <p id="reportDescription"></p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>

        </div>

    </div>

</div>


<!-- ========================================= -->
<!-- UPDATE REPORT STATUS MODAL -->
<!-- ========================================= -->

<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                action="<?= site_url('admin/reports/update-status') ?>"
                method="POST">

                <?= csrf_field() ?>

                <div class="modal-header">

                    <h5 class="modal-title">
                        Update Report
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- HIDDEN REPORT ID -->
                    <input
                        type="hidden"
                        id="statusReportId"
                        name="report_id">

                    <!-- STATUS -->
                    <div class="mb-3">

                        <label
                            for="statusSelect"
                            class="form-label">
                            Status
                        </label>

                        <select
                            class="form-select"
                            id="statusSelect"
                            name="status"
                            required>

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="In Progress">
                                In Progress
                            </option>

                            <option value="Resolved">
                                Resolved
                            </option>

                            <option value="Rejected">
                                Rejected
                            </option>

                        </select>

                    </div>

                    <!-- PRIORITY -->
                    <div class="mb-3">

                        <label
                            for="prioritySelect"
                            class="form-label">
                            Priority
                        </label>

                        <select
                            class="form-select"
                            id="prioritySelect"
                            name="priority"
                            required>

                            <option value="">
                                Select Priority
                            </option>

                            <option value="Low">
                                Low
                            </option>

                            <option value="Medium">
                                Medium
                            </option>

                            <option value="High">
                                High
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

        </main>

    </div>
<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Report JS -->

<script src="<?= base_url('assets/js/report A.js') ?>"></script>

</body>
</html>