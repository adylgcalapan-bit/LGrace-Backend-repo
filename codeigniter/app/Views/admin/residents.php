<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents Management | Community Problems Visibility System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-admin.css') ?>">

    <!-- Residents CSS -->

    <link rel="stylesheet" href="<?= base_url('assets/css/admin-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-responsive.css') ?>">

</head>

<body class="<?= esc(system_theme_class()) ?>">
    <button
        type="button"
        class="admin-mobile-toggle"
        aria-label="Open admin menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="admin-sidebar-overlay"></div>

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

                <li>
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

                <li class="active">
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
                        Account
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

            <!-- PAGE HEADER -->

            <header class="topbar">

                <div class="welcome">
                    <h2>Residents Management</h2>
                    <p>View and manage all registered residents.</p>
                </div>

                <div class="top-actions">

                    <button
                        type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#addResidentModal">
                        <i class="bi bi-person-plus me-1"></i>
                        Add Resident
                    </button>
                </div>

            </header>

            <!-- SUCCESS / ERROR NOTIFICATION -->

            <?php
            $successMessage = session()->getFlashdata('success');
            $errorMessage   = session()->getFlashdata('error');
            ?>

            <?php if ($successMessage || $errorMessage): ?>

                <div
                    class="modal fade"
                    id="residentMessageModal"
                    tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div
                            class="modal-content border-0 shadow text-center"
                            style="border-radius: 18px;">

                            <div class="modal-body p-5">

                                <?php if ($successMessage): ?>

                                    <div
                                        class="d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="
                            width: 70px;
                            height: 70px;
                            border-radius: 50%;
                            border: 3px solid #198754;
                            color: #198754;
                            font-size: 34px;
                        ">
                                        <i class="bi bi-check-lg"></i>
                                    </div>

                                    <h3 class="fw-bold text-success mb-3">
                                        Success!
                                    </h3>

                                    <p class="text-muted mb-4">
                                        <?= esc($successMessage) ?>
                                    </p>

                                    <button
                                        type="button"
                                        class="btn btn-success px-5"
                                        data-bs-dismiss="modal">
                                        Continue
                                    </button>

                                <?php else: ?>

                                    <div
                                        class="d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="
                            width: 70px;
                            height: 70px;
                            border-radius: 50%;
                            border: 3px solid #dc3545;
                            color: #dc3545;
                            font-size: 34px;
                        ">
                                        <i class="bi bi-x-lg"></i>
                                    </div>

                                    <h3 class="fw-bold text-danger mb-3">
                                        Oops!
                                    </h3>

                                    <p class="text-muted mb-4">
                                        <?= esc($errorMessage) ?>
                                    </p>

                                    <button
                                        type="button"
                                        class="btn btn-danger px-5"
                                        data-bs-dismiss="modal">
                                        Try Again
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>
                    </div>
                </div>

            <?php endif; ?>

            <!-- SEARCH & FILTERS -->

            <form
                action="<?= site_url('admin/residents') ?>"
                method="get"
                id="residentFiltersForm"
                class="card border-0 shadow-sm p-4 mb-4">

                <div class="row g-3">

                    <div class="col-lg-4">

                        <input
                            type="text"
                            class="form-control"
                            id="residentSearch"
                            name="search"
                            value="<?= esc($filters['search'] ?? '') ?>"
                            placeholder="Search resident...">

                    </div>

                    <div class="col-lg-3">

                        <?php
                        $currentResidentStatus =
                            $filters['status'] ?? 'all';

                        $residentStatusLabels = [
                            'all'      => 'All Status',
                            'active'   => 'Active',
                            'inactive' => 'Inactive',
                        ];

                        $currentResidentStatusLabel =
                            $residentStatusLabels[$currentResidentStatus]
                            ?? 'All Status';
                        ?>

                        <input
                            type="hidden"
                            id="statusFilter"
                            name="status"
                            value="<?= esc($currentResidentStatus) ?>">

                        <div class="dropdown report-filter-dropdown">

                            <button
                                class="btn report-filter-dropdown-btn dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <span id="residentStatusLabel">
                                    <?= esc($currentResidentStatusLabel) ?>
                                </span>

                            </button>

                            <ul class="dropdown-menu report-filter-menu">

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item resident-status-option"
                                        data-value="All Status">
                                        All Status
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item resident-status-option"
                                        data-value="Active">
                                        Active
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item resident-status-option"
                                        data-value="Inactive">
                                        Inactive
                                    </button>
                                </li>

                            </ul>

                        </div>

                    </div>

                    <div class="col-lg-3">
                        <?php
                        $currentPurok =
                            (string) ($filters['purok'] ?? '');

                        $currentPurokLabel = 'All Puroks';

                        if ($currentPurok === 'unassigned') {
                            $currentPurokLabel = 'Unassigned';
                        } elseif ($currentPurok !== '') {
                            foreach ($puroks ?? [] as $purok) {
                                if (
                                    (string) ($purok['purok_id'] ?? '') ===
                                    $currentPurok
                                ) {
                                    $currentPurokLabel =
                                        $purok['purok_name'] ?? 'All Puroks';

                                    break;
                                }
                            }
                        }
                        ?>

                        <input
                            type="hidden"
                            id="purokFilter"
                            name="purok"
                            value="<?= esc($currentPurok) ?>">

                        <div class="dropdown report-filter-dropdown">

                            <button
                                class="btn report-filter-dropdown-btn dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <span id="residentPurokLabel">
                                    <?= esc($currentPurokLabel) ?>
                                </span>

                            </button>

                            <ul class="dropdown-menu report-filter-menu">

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item resident-purok-option"
                                        data-value="">
                                        All Puroks
                                    </button>
                                </li>

                                <?php foreach (($puroks ?? []) as $purok): ?>

                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item resident-purok-option"
                                            data-value="<?= (int) ($purok['purok_id'] ?? 0) ?>">

                                            <?= esc($purok['purok_name'] ?? 'Unnamed Purok') ?>

                                        </button>
                                    </li>

                                <?php endforeach; ?>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item resident-purok-option"
                                        data-value="unassigned">
                                        Unassigned
                                    </button>
                                </li>

                            </ul>

                        </div>

                    </div>




                </div>
            </form>

            <!-- RESIDENTS TABLE -->

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white">

                    <h4 class="mb-0">

                        Registered Residents

                    </h4>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Purok</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Registered</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($residents)): ?>

                                <?php foreach ($residents as $resident): ?>

                                    <?php
                                    $residentImage = !empty($resident['profile_image'])
                                        ? base_url(ltrim($resident['profile_image'], '/\\'))
                                        : base_url('assets/images/resident picture.png');

                                    $residentId = 'R-' . str_pad(
                                        (string) $resident['user_id'],
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    );

                                    $isActive = (int) ($resident['is_active'] ?? 1) === 1;
                                    $isEmailVerified = !empty($resident['email_verified_at']);
                                    $isPendingVerification = !$isActive && !$isEmailVerified;
                                    ?>

                                    <tr class="resident-row"
                                        data-user-id="<?= (int) $resident['user_id'] ?>"
                                        data-id="<?= esc($residentId) ?>"
                                        data-name="<?= esc($resident['full_name'] ?? '') ?>"
                                        data-email="<?= esc($resident['email'] ?? '') ?>"
                                        data-contact="<?= esc($resident['mobile_number'] ?? '') ?>"
                                        data-address="<?= esc($resident['address'] ?? '') ?>"
                                        data-purok-id="<?= !empty($resident['purok_id']) ? (int) $resident['purok_id'] : 'unassigned' ?>"
                                        data-status="<?= $isPendingVerification ? 'pending' : ($isActive ? 'active' : 'inactive') ?>"
                                        data-image="<?= esc($residentImage) ?>">

                                        <td>
                                            R-<?= str_pad(
                                                    (string) $resident['user_id'],
                                                    3,
                                                    '0',
                                                    STR_PAD_LEFT
                                                ) ?>
                                        </td>

                                        <td>
                                            <img
                                                src="<?= esc($residentImage) ?>"
                                                alt="<?= esc($resident['full_name'] ?? 'Resident') ?>"
                                                class="rounded-circle"
                                                width="45"
                                                height="45"
                                                style="object-fit: cover;">
                                        </td>

                                        <td>
                                            <?= esc($resident['full_name'] ?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= esc($resident['email'] ?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= esc($resident['mobile_number'] ?? 'N/A') ?>
                                        </td>


                                        <td>
                                            <?= esc($resident['purok_name'] ?? 'Unassigned') ?>
                                        </td>

                                        <td>
                                            <?= esc($resident['username'] ?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?php if ($isPendingVerification): ?>

                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-envelope-exclamation me-1"></i>
                                                    Pending Verification
                                                </span>

                                            <?php elseif ($isActive): ?>

                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Active
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-slash-circle me-1"></i>
                                                    Inactive
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= !empty($resident['created_at'])
                                                ? esc(format_system_date($resident['created_at']))
                                                : 'N/A' ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No registered residents found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <!-- PAGINATION -->
                <div class="card-footer bg-white">

                    <?php
                    $currentPage = (int) ($pagination['current_page'] ?? 1);
                    $totalPages  = (int) ($pagination['total_pages'] ?? 1);

                    $buildPageUrl = function ($page) {
                        return site_url('admin/residents')
                            . '?page='
                            . (int) $page;
                    };
                    ?>

                    <?php if ($totalPages > 1): ?>


                        <nav aria-label="Residents pagination">

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


            <!-- ================= RESIDENT OVERVIEW MODAL ================= -->

            <div class="modal fade" id="residentModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-person-vcard me-2"></i>
                                Resident Overview
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <!-- PROFILE / ACCOUNT INFO -->
                            <div class="row align-items-center mb-4">

                                <div class="col-md-3 text-center mb-3 mb-md-0">

                                    <img
                                        id="residentModalImage"
                                        src="<?= base_url('assets/images/resident picture.png') ?>"
                                        alt="Resident"
                                        class="rounded-circle"
                                        style="
                                width: 140px;
                                height: 140px;
                                object-fit: cover;
                                border: 4px solid #198754;
                            ">

                                    <h5 class="mt-3 mb-1"
                                        id="residentModalName">
                                        Resident
                                    </h5>

                                    <span class="badge bg-success">
                                        Resident
                                    </span>

                                </div>

                                <div class="col-md-9">

                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <small class="text-muted">Resident ID</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalId">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Username</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalUsername">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Email Address</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalEmail">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Contact Number</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalContact">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Address</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalAddress">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Date Registered</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalRegistered">
                                                â€”
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Account Status</small>

                                            <p class="mb-0">
                                                <span
                                                    id="residentModalStatus"
                                                    class="badge bg-secondary">
                                                    Loading...
                                                </span>
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <hr>

                            <!-- REPORT STATISTICS -->

                            <h5 class="mb-3">
                                <i class="bi bi-bar-chart-fill me-2"></i>
                                Report Summary
                            </h5>

                            <div class="row g-3 mb-4">

                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body text-center">
                                            <small class="text-muted">
                                                Total Reports
                                            </small>

                                            <h2 class="mb-0 mt-2"
                                                id="residentStatTotal">
                                                0
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-warning h-100">
                                        <div class="card-body text-center">
                                            <small class="text-muted">
                                                Pending
                                            </small>

                                            <h2 class="mb-0 mt-2 text-warning"
                                                id="residentStatPending">
                                                0
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-primary h-100">
                                        <div class="card-body text-center">
                                            <small class="text-muted">
                                                In Progress
                                            </small>

                                            <h2 class="mb-0 mt-2 text-primary"
                                                id="residentStatProgress">
                                                0
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-success h-100">
                                        <div class="card-body text-center">
                                            <small class="text-muted">
                                                Resolved
                                            </small>

                                            <h2 class="mb-0 mt-2 text-success"
                                                id="residentStatResolved">
                                                0
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- RECENT REPORTS -->

                            <h5 class="mb-3">
                                <i class="bi bi-clock-history me-2"></i>
                                Recent Reports
                            </h5>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>Report</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody id="residentRecentReports">

                                        <tr>
                                            <td colspan="4"
                                                class="text-center text-muted">
                                                Click a resident to load reports.
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <form
                                id="residentStatusForm"
                                method="post"
                                class="me-auto">

                                <?= csrf_field() ?>

                                <button
                                    type="submit"
                                    id="residentStatusButton"
                                    class="btn btn-secondary"
                                    disabled>

                                    <i
                                        id="residentStatusButtonIcon"
                                        class="bi bi-hourglass-split me-1">
                                    </i>

                                    <span id="residentStatusButtonText">
                                        Loading...
                                    </span>

                                </button>

                            </form>

                            <form
                                id="residentResendOtpForm"
                                method="post"
                                class="d-none me-2">

                                <?= csrf_field() ?>

                                <button
                                    type="submit"
                                    id="residentResendOtpButton"
                                    class="btn btn-warning">

                                    <i class="bi bi-envelope-arrow-up me-1"></i>

                                    <span id="residentResendOtpButtonText">
                                        Resend OTP
                                    </span>
                                </button>

                            </form>

                            <form
                                id="residentDeletePendingForm"
                                method="post"
                                class="d-none me-2">

                                <?= csrf_field() ?>

                                <button
                                    type="button"
                                    id="residentDeletePendingButton"
                                    class="btn btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteResidentModal">

                                    <i class="bi bi-trash me-1"></i>
                                    Delete Account
                                </button>

                            </form>

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

            <!-- ================= EDIT RESIDENT MODAL ================= -->

            <div class="modal fade" id="editResidentModal" tabindex="-1">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Edit Resident
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body">

                            <div class="mb-3">

                                <label class="form-label">Full Name</label>

                                <input type="text"
                                    class="form-control"
                                    value="Juan Dela Cruz">

                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email Address <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="Enter resident's active email"
                                    autocomplete="email"
                                    required>

                                <div class="form-text">
                                    A 6-digit verification code will be sent to this email before the resident can fully access the account.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">
                                    Mobile Number
                                </label>

                                <input
                                    type="tel"
                                    class="form-control"
                                    id="mobile_number"
                                    name="mobile_number"
                                    placeholder="09XXXXXXXXX"
                                    inputmode="numeric"
                                    maxlength="11"
                                    pattern="09[0-9]{9}"
                                    autocomplete="tel">

                                <div class="form-text">
                                    Enter an 11-digit Philippine mobile number, e.g. 09123456789.
                                </div>
                            </div>

                            <div class="mb-3">

                                <label class="form-label">Purok</label>

                                <select class="form-select">

                                    <option>Purok 1</option>
                                    <option>Purok 2</option>
                                    <option>Purok 3</option>
                                    <option>Purok 4</option>
                                    <option>Purok 5</option>

                                </select>

                            </div>

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

            <!-- ================= DELETE CONFIRMATION MODAL ================= -->

            <div class="modal fade" id="deleteResidentModal" tabindex="-1">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header bg-danger text-white">

                            <h5 class="modal-title">
                                Delete Resident
                            </h5>

                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body text-center">

                            <i class="bi bi-exclamation-triangle-fill text-danger"
                                style="font-size:60px;"></i>

                            <h5 class="mt-3">
                                Are you sure?
                            </h5>

                            <p>
                                This resident account will be permanently deleted.
                            </p>

                        </div>

                        <div class="modal-footer">

                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger"
                                id="confirmDeletePendingResident">
                                Delete
                            </button>
                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>
    <!-- ========================================= -->
    <!-- ADD RESIDENT MODAL -->
    <!-- ========================================= -->

    <div
        class="modal fade"
        id="addResidentModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content border-0 shadow">


                <form
                    method="post"
                    action="<?= site_url('admin/residents/create') ?>"
                    id="addResidentForm"
                    enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <!-- HEADER -->
                    <div class="modal-header bg-success text-white">

                        <div>
                            <h5 class="modal-title mb-1">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Add New Resident
                            </h5>

                            <small class="opacity-75">
                                Create a resident account for the Community Visibility System
                            </small>
                        </div>

                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body p-4">

                        <!-- ========================= -->
                        <!-- PERSONAL INFORMATION -->
                        <!-- ========================= -->

                        <div class="resident-form-section mb-4">

                            <div class="d-flex align-items-center mb-3">

                                <div class="resident-section-icon">
                                    <i class="bi bi-person-vcard"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0">Personal Information</h6>
                                    <small class="text-muted">
                                        Basic information of the resident
                                    </small>
                                </div>

                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">
                                            Profile Picture
                                        </label>

                                        <input
                                            type="file"
                                            class="form-control"
                                            id="profile_image"
                                            name="profile_image"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                                        <div class="form-text">
                                            Optional. JPG, PNG, or WebP only. Maximum 2 MB.
                                        </div>

                                        <div
                                            id="residentProfilePreviewWrap"
                                            class="mt-3 d-none text-center">
                                            <img
                                                id="residentProfilePreview"
                                                src=""
                                                alt="Profile preview"
                                                style="
                width: 110px;
                height: 110px;
                object-fit: cover;
                border-radius: 50%;
                border: 1px solid #dee2e6;
            ">
                                        </div>
                                    </div>

                                    <label class="form-label">
                                        Full Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="full_name"
                                        placeholder="e.g. Portgas D. Ace"
                                        value="<?= esc(old('full_name') ?? '') ?>"
                                        required>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Email Address
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        placeholder="resident@example.com"
                                        value="<?= esc(old('email') ?? '') ?>"
                                        required>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Mobile Number
                                    </label>

                                    <input
                                        type="tel"
                                        class="form-control"
                                        name="mobile_number"
                                        placeholder="09XXXXXXXXX"
                                        value="<?= esc(old('mobile_number') ?? '') ?>">

                                    <small class="text-muted">
                                        Optional
                                    </small>

                                </div>

                            </div>

                        </div>

                        <hr>

                        <!-- ========================= -->
                        <!-- BARANGAY ADDRESS -->
                        <!-- ========================= -->

                        <div class="resident-form-section my-4">

                            <div class="d-flex align-items-center mb-3">

                                <div class="resident-section-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0">Barangay Address</h6>
                                    <small class="text-muted">
                                        Select the resident's home Purok
                                    </small>
                                </div>

                            </div>

                            <div class="row g-3">

                                <div class="col-md-5">

                                    <label class="form-label">
                                        Purok
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="hidden"
                                        name="purok_id"
                                        id="addResidentPurok"
                                        value="">

                                    <div class="dropdown add-resident-purok-dropdown">

                                        <button
                                            class="btn dropdown-toggle add-resident-purok-btn"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">

                                            <span id="addResidentPurokLabel">
                                                Select Purok
                                            </span>

                                        </button>

                                        <ul class="dropdown-menu add-resident-purok-menu">

                                            <?php foreach (($puroks ?? []) as $purok): ?>

                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item add-resident-purok-option"
                                                        data-value="<?= (int) $purok['purok_id'] ?>">

                                                        <?= esc($purok['purok_name']) ?>

                                                    </button>
                                                </li>

                                            <?php endforeach; ?>

                                        </ul>

                                    </div>

                                    <div class="form-text">
                                        Select the resident's assigned Purok.
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        Address <span class="text-danger">*</span>
                                    </label>

                                    <textarea
                                        class="form-control"
                                        id="address"
                                        name="address"
                                        rows="3"
                                        placeholder="Enter house number, street/sitio, and other address details"
                                        required></textarea>

                                    <div class="form-text">
                                        Enter the resident's complete address within Barangay Saguing.
                                    </div>
                                </div>

                            </div>

                        </div>

                        <hr>

                        <!-- ========================= -->
                        <!-- ACCOUNT CREDENTIALS -->
                        <!-- ========================= -->

                        <div class="resident-form-section mt-4">

                            <div class="d-flex align-items-center mb-3">

                                <div class="resident-section-icon">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0">Account Credentials</h6>
                                    <small class="text-muted">
                                        Login information for the resident
                                    </small>
                                </div>

                            </div>

                            <div class="row g-3">


                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label for="password" class="form-label">
                                            Password <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="password"
                                                name="password"
                                                minlength="8"
                                                placeholder="Enter password"
                                                required>

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                id="toggleResidentPassword"
                                                aria-label="Show password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>

                                        <div class="form-text">
                                            Password must contain at least 8 characters.
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">
                                            Confirm Password <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="confirm_password"
                                                name="confirm_password"
                                                minlength="8"
                                                placeholder="Re-enter password"
                                                required>

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                id="toggleResidentConfirmPassword"
                                                aria-label="Show confirm password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>

                                        <div
                                            id="residentPasswordMatch"
                                            class="form-text"></div>
                                    </div>

                                </div>


                            </div>

                            <div class="alert alert-light border mt-3 mb-0">

                                <div class="d-flex">

                                    <i class="bi bi-info-circle-fill text-success me-2"></i>

                                    <small>
                                        A 6-digit verification code will be sent to the resident's email. The account will be activated after successful email verification.
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">

                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-success px-4"
                            id="addResidentSubmitBtn">

                            <span
                                id="addResidentSubmitSpinner"
                                class="spinner-border spinner-border-sm d-none me-2"
                                aria-hidden="true">
                            </span>

                            <span id="addResidentSubmitText">
                                <i class="bi bi-person-check-fill me-1"></i>
                                Create Resident Account
                            </span>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Residents JavaScript -->

    <script src="<?= base_url('assets/js/resident.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin-responsive.js') ?>"></script>

</body>

</html>