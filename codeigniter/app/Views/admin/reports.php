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

            <li>
                <a href="<?= base_url('admin/notifications') ?>">
                    <i class="bi bi-bell"></i>
                    Notifications
                </a>
            </li>

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

                        <tr data-state="pending" class="report-row" data-id="#1001" data-resident="Juan Dela Cruz" data-category="Infrastructure and Public Works Issues" data-location="Purok 1" data-date="July 26, 2026" data-status="Pending" data-priority="Medium" data-description="Large potholes were reported along the main road. Vehicles and motorcycles are having difficulty passing through the area.">
                            <td>#1001</td>
                            <td>Juan Dela Cruz</td>
                            <td>Infrastructure and Public Works Issues</td>
                            <td>Purok 1</td>
                            <td>July 26, 2026</td>
                            <td><span class="badge bg-warning text-dark status-badge">Pending</span></td>
                            <td>
                                <select class="form-select form-select-sm priority-select" disabled>
                                    <option>Low</option>
                                    <option selected>Medium</option>
                                    <option>High</option>
                                </select>
                            </td>
                            <td class="action-cell">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-success accept-btn">Accept</button>
                                    <button class="btn btn-sm btn-danger decline-btn">Decline</button>
                                </div>
                            </td>
                        </tr>

                        <tr data-state="in-progress" class="report-row" data-id="#1002" data-resident="Maria Santos" data-category="Waste Management Problems" data-location="Purok 2" data-date="July 26, 2026" data-status="In Progress" data-priority="Low" data-description="Garbage is piling up near the barangay route and the collection truck has not arrived on schedule.">
                            <td>#1002</td>
                            <td>Maria Santos</td>
                            <td>Waste Management Problems</td>
                            <td>Purok 2</td>
                            <td>July 26, 2026</td>
                            <td><span class="badge bg-primary status-badge">In Progress</span></td>
                            <td>
                                <select class="form-select form-select-sm priority-select">
                                    <option selected>Low</option>
                                    <option>Medium</option>
                                    <option>High</option>
                                </select>
                            </td>
                            <td class="action-cell">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-success resolve-btn">Resolve</button>
                                </div>
                            </td>
                        </tr>

                        <tr data-state="resolved" class="report-row" data-id="#1003" data-resident="Pedro Ramos" data-category="Environmental and Natural Issues" data-location="Purok 5" data-date="July 25, 2026" data-status="Resolved" data-priority="High" data-description="A fallen tree blocked the pathway and was removed by the local maintenance team.">
                            <td>#1003</td>
                            <td>Pedro Ramos</td>
                            <td>Environmental and Natural Issues</td>
                            <td>Purok 5</td>
                            <td>July 25, 2026</td>
                            <td><span class="badge bg-success status-badge">Resolved</span></td>
                            <td>
                                <select class="form-select form-select-sm priority-select">
                                    <option>Low</option>
                                    <option>Medium</option>
                                    <option selected>High</option>
                                </select>
                            </td>
                            <td class="action-cell">
                                <span class="text-success small">Completed</span>
                            </td>
                        </tr>

                        <tr data-state="rejected" class="report-row" data-id="#1004" data-resident="Ana Lopez" data-category="Public Safety and Security Issues" data-location="Purok 3" data-date="July 24, 2026" data-status="Rejected" data-priority="Low" data-description="A report was submitted about a broken streetlight that was not within the maintenance coverage area.">
                            <td>#1004</td>
                            <td>Ana Lopez</td>
                            <td>Public Safety and Security Issues</td>
                            <td>Purok 3</td>
                            <td>July 24, 2026</td>
                            <td><span class="badge bg-danger status-badge">Rejected</span></td>
                            <td>
                                <select class="form-select form-select-sm priority-select">
                                    <option selected>Low</option>
                                    <option>Medium</option>
                                    <option>High</option>
                                </select>
                            </td>
                            <td class="action-cell">
                                <span class="text-muted small">Declined</span>
                            </td>
                        </tr>

                        <tr data-state="pending" class="report-row" data-id="#1005" data-resident="Carlos Reyes" data-category="Utilities and Public Services" data-location="Purok 4" data-date="July 23, 2026" data-status="Pending" data-priority="Medium" data-description="Residents reported weak water pressure in the area and requested follow-up inspection.">
                            <td>#1005</td>
                            <td>Carlos Reyes</td>
                            <td>Utilities and Public Services</td>
                            <td>Purok 4</td>
                            <td>July 23, 2026</td>
                            <td><span class="badge bg-warning text-dark status-badge">Pending</span></td>
                            <td>
                                <select class="form-select form-select-sm priority-select" disabled>
                                    <option>Low</option>
                                    <option selected>Medium</option>
                                    <option>High</option>
                                </select>
                            </td>
                            <td class="action-cell">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-success accept-btn">Accept</button>
                                    <button class="btn btn-sm btn-danger decline-btn">Decline</button>
                                </div>
                            </td>
                        </tr>

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
                <!-- REPORT DETAILS MODAL -->

        <div class="modal fade" id="reportModal" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Report Details
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6">

                                <p><strong>Report ID:</strong> <span id="reportId">#1001</span></p>
                                <p><strong>Resident:</strong> <span id="reportResident">zoro</span></p>
                                <p><strong>Category:</strong> <span id="reportCategory">Road Damage</span></p>
                                <p><strong>Status:</strong> <span id="reportStatus">Pending</span></p>

                            </div>

                            <div class="col-md-6">

                                <p><strong>Location:</strong> <span id="reportLocation">Purok 1</span></p>
                                <p><strong>Date:</strong> <span id="reportDate">July 26, 2026</span></p>
                                <p><strong>Priority:</strong> <span id="reportPriority">High</span></p>

                            </div>

                        </div>

                        <hr>

                        <h6>Description</h6>

                        <p id="reportDescription">
                            Large potholes were reported along the main road.
                            Vehicles and motorcycles are having difficulty
                            passing through the area.
                        </p>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- UPDATE STATUS MODAL -->

        <div class="modal fade" id="statusModal" tabindex="-1">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Update Report Status
                        </h5>

                        <button class="btn-close"
                                data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <label class="form-label">
                            Select Status
                        </label>

                        <select class="form-select">

                            <option>Pending</option>
                            <option>In Progress</option>
                            <option>Resolved</option>
                            <option>Rejected</option>

                        </select>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button class="btn btn-success">
                            Save Changes
                        </button>

                    </div>

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