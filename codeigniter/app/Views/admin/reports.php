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

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
                    <a href="<?= base_url('logout') ?>">
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

                <form
                    action="<?= site_url('admin/reports') ?>"
                    method="get"
                    id="reportFiltersForm">

                    <!-- Friendly Filter Header -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

                        <div>
                            <h5 class="mb-1 fw-semibold">
                                <i class="bi bi-search me-2 text-success"></i>
                                Find Reports
                            </h5>

                            <p class="text-muted mb-0 small">
                                Search and narrow down community reports easily.
                            </p>
                        </div>

                        <a
                            href="<?= site_url('admin/reports') ?>"
                            class="btn btn-link text-secondary text-decoration-none px-0 mt-2 mt-md-0">

                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Clear Filters

                        </a>

                    </div>


                    <!-- ROW 1 -->
                    <div class="row g-3 mb-3">

                        <!-- SEARCH -->
                        <div class="col-lg-6">

                            <label
                                for="reportSearch"
                                class="form-label fw-medium">
                                Search Reports
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control border-start-0"
                                    id="reportSearch"
                                    name="search"
                                    value="<?= esc($filters['search'] ?? '') ?>"
                                    placeholder="Report ID, title, category, or location">

                            </div>

                        </div>


                        <!-- CATEGORY -->
                        <div class="col-lg-3">

                            <label
                                for="reportCategory"
                                class="form-label fw-medium">
                                Category
                            </label>

                            <select
                                class="form-select"
                                id="reportCategory"
                                name="category">

                                <option value="0">
                                    All Categories
                                </option>

                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= esc($category['category_id']) ?>"
                                        <?= (int) ($filters['category'] ?? 0) === (int) $category['category_id']
                                            ? 'selected'
                                            : '' ?>>

                                        <?= esc($category['category_name']) ?>

                                        <?= isset($category['is_active']) && (int) $category['is_active'] === 0
                                            ? ' (Inactive)'
                                            : '' ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- STATUS -->
                        <div class="col-lg-3">

                            <label
                                for="reportStatusFilter"
                                class="form-label fw-medium">
                                Status
                            </label>

                            <select
                                class="form-select"
                                id="reportStatusFilter"
                                name="status">

                                <option
                                    value="all"
                                    <?= ($filters['status'] ?? 'all') === 'all'
                                        ? 'selected'
                                        : '' ?>>
                                    All Status
                                </option>

                                <option
                                    value="Pending"
                                    <?= ($filters['status'] ?? '') === 'Pending'
                                        ? 'selected'
                                        : '' ?>>
                                    Pending
                                </option>

                                <option
                                    value="In Progress"
                                    <?= ($filters['status'] ?? '') === 'In Progress'
                                        ? 'selected'
                                        : '' ?>>
                                    In Progress
                                </option>

                                <option
                                    value="Resolved"
                                    <?= ($filters['status'] ?? '') === 'Resolved'
                                        ? 'selected'
                                        : '' ?>>
                                    Resolved
                                </option>

                                <option
                                    value="Rejected"
                                    <?= ($filters['status'] ?? '') === 'Rejected'
                                        ? 'selected'
                                        : '' ?>>
                                    Rejected
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- ROW 2 -->
                    <div class="row g-3 align-items-end">

                        <!-- FROM DATE -->
                        <div class="col-lg-3">

                            <label
                                for="reportFromDate"
                                class="form-label fw-medium">
                                From Date
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="reportFromDate"
                                name="from_date"
                                value="<?= esc($filters['from_date'] ?? '') ?>">

                        </div>


                        <!-- TO DATE -->
                        <div class="col-lg-3">

                            <label
                                for="reportToDate"
                                class="form-label fw-medium">
                                To Date
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="reportToDate"
                                name="to_date"
                                value="<?= esc($filters['to_date'] ?? '') ?>">

                        </div>


                        <!-- SORT -->
                        <div class="col-lg-3">

                            <label
                                for="reportSort"
                                class="form-label fw-medium">
                                Sort By
                            </label>

                            <select
                                class="form-select"
                                id="reportSort"
                                name="sort">

                                <option
                                    value="newest"
                                    <?= ($filters['sort'] ?? 'newest') === 'newest'
                                        ? 'selected'
                                        : '' ?>>
                                    Newest First
                                </option>

                                <option
                                    value="oldest"
                                    <?= ($filters['sort'] ?? '') === 'oldest'
                                        ? 'selected'
                                        : '' ?>>
                                    Oldest First
                                </option>

                            </select>

                        </div>



                    </div>

                </form>

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

                                    $isAnonymous = (int) ($report['is_anonymous'] ?? 0) === 1;

                                    $realResidentName = trim(
                                        (string) ($report['full_name'] ?? 'Unknown Resident')
                                    );
                                    $displayResidentName = $realResidentName;

                                    $userId = (int) ($report['user_id'] ?? 0);

                                    if ($isAnonymous) {
                                        $displayResidentId = 'R-***';
                                    } else {
                                        $displayResidentId = $userId > 0
                                            ? 'R-' . str_pad((string) $userId, 3, '0', STR_PAD_LEFT)
                                            : 'Unknown';
                                    }

                                    if ($isAnonymous && $realResidentName !== 'Unknown Resident') {

                                        $nameParts = preg_split('/\s+/', $realResidentName);

                                        $maskedParts = array_map(function ($part) {

                                            if ($part === '') {
                                                return '';
                                            }

                                            return mb_strtoupper(
                                                mb_substr($part, 0, 1)
                                            ) . '***';
                                        }, $nameParts);

                                        $displayResidentName = implode(' ', $maskedParts);
                                    }

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
                                        data-resident="<?= esc($displayResidentName) ?>"
                                        data-resident-id="<?= esc($displayResidentId) ?>"
                                        data-title="<?= esc($report['title'] ?? '') ?>"
                                        data-category="<?= esc($report['category_name'] ?? 'No Category') ?>"
                                        data-location="<?= esc($location) ?>"
                                        data-latitude="<?= esc($report['latitude'] ?? '') ?>"
                                        data-longitude="<?= esc($report['longtitude'] ?? '') ?>"
                                        data-address="<?= esc($address) ?>"
                                        data-date="<?= date('F d, Y', strtotime($report['date_reported'])) ?>"
                                        data-status="<?= esc($status) ?>"
                                        data-priority="<?= esc($report['priority'] ?? '') ?>"
                                        data-description="<?= esc($report['description'] ?? '') ?>"
                                        data-photo="<?= !empty($report['image_path']) ? base_url($report['image_path']) : '' ?>">

                                        <!-- ID -->
                                        <td>
                                            #<?= esc($report['report_id']) ?>
                                        </td>

                                        <!-- Resident -->
                                        <td>
                                            <?= esc($displayResidentName) ?>
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
                                                    class="btn btn-sm btn-success view-btn"
                                                    title="View Report Details">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary update-status-btn"
                                                    title="Update Status and Priority"
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

                    <?php
                    $currentPage = (int) ($pagination['current_page'] ?? 1);
                    $totalPages  = (int) ($pagination['total_pages'] ?? 1);

                    $queryParams = [
                        'search'    => $filters['search'] ?? '',
                        'category'  => $filters['category'] ?? 0,
                        'status'    => $filters['status'] ?? 'all',
                        'from_date' => $filters['from_date'] ?? '',
                        'to_date'   => $filters['to_date'] ?? '',
                        'sort'      => $filters['sort'] ?? 'newest',
                    ];

                    $buildPageUrl = function ($page) use ($queryParams) {
                        $queryParams['page'] = $page;

                        return site_url('admin/reports')
                            . '?'
                            . http_build_query($queryParams);
                    };
                    ?>

                    <?php if ($totalPages > 1): ?>

                        <nav aria-label="Report pagination">

                            <ul class="pagination justify-content-end mb-0">

                                <!-- PREVIOUS -->
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">

                                    <a
                                        class="page-link"
                                        href="<?= $currentPage > 1
                                                    ? esc($buildPageUrl($currentPage - 1))
                                                    : '#' ?>">

                                        Previous

                                    </a>

                                </li>

                                <!-- PAGE NUMBERS -->
                                <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>

                                    <li class="page-item <?= $pageNumber === $currentPage ? 'active' : '' ?>">

                                        <a
                                            class="page-link"
                                            href="<?= esc($buildPageUrl($pageNumber)) ?>">

                                            <?= $pageNumber ?>

                                        </a>

                                    </li>

                                <?php endfor; ?>

                                <!-- NEXT -->
                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">

                                    <a
                                        class="page-link"
                                        href="<?= $currentPage < $totalPages
                                                    ? esc($buildPageUrl($currentPage + 1))
                                                    : '#' ?>">

                                        Next

                                    </a>

                                </li>

                            </ul>

                        </nav>

                    <?php endif; ?>

                </div>

            </div>
            <!-- ========================================= -->
            <!-- REPORT DETAILS MODAL -->
            <!-- ========================================= -->

            <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">

                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

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
                                        <strong>Resident ID:</strong>
                                        <span id="reportResidentId"></span>
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


                            <hr class="my-4">

                            <!-- REPORTED LOCATION MAP -->
                            <div class="mt-3">

                                <h6 class="mb-3">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    Reported Location
                                </h6>

                                <div
                                    id="reportMap"
                                    style="
            width: 100%;
            height: 340px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        ">
                                </div>

                                <small
                                    id="reportMapMessage"
                                    class="text-muted d-none">
                                    No valid map location is available for this report.
                                </small>
                            </div>
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

    <!-- Leaflet JS -->
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <!-- Report JS -->

    <script src="<?= base_url('assets/js/report A.js') ?>"></script>


</body>

</html>