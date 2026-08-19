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
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-admin.css') ?>">

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

                        <i class="bi bi-person-plus-fill me-1"></i>
                        Add Resident
                    </button>

                </div>

            </header>

            <!-- SUCCESS / ERROR NOTIFICATION -->

            <?php
            $successMessage = session()->getFlashdata('success');
            $errorMessage   = session()->getFlashdata('error');
            ?>

            <?php if ($successMessage): ?>

                <div
                    class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow"
                    style="z-index: 2000; min-width: 340px;"
                    role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <strong>Success!</strong>
                    <?= esc($successMessage) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>


            <?php if ($errorMessage): ?>

                <div
                    class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow"
                    style="z-index: 2000; min-width: 340px;"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <strong>Error!</strong>
                    <?= esc($errorMessage) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>

                <div
                    class="alert alert-danger alert-dismissible fade show shadow-sm"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <?= esc(session()->getFlashdata('error')) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                    </button>

                </div>

            <?php endif; ?>

            <!-- SEARCH & FILTERS -->

            <div class="card border-0 shadow-sm p-4 mb-4">

                <div class="row g-3">

                    <div class="col-lg-4">

                        <input
                            type="text"
                            class="form-control"
                            id="residentSearch"
                            placeholder="Search resident...">

                    </div>

                    <div class="col-lg-3">

                        <select class="form-select" id="statusFilter">

                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <select
                            class="form-select"
                            id="purokFilter">

                            <option value="">
                                All Puroks
                            </option>

                            <?php foreach (($puroks ?? []) as $purok): ?>

                                <option value="<?= (int) ($purok['purok_id'] ?? 0) ?>">
                                    <?= esc($purok['purok_name'] ?? 'Unnamed Purok') ?>
                                </option>

                            <?php endforeach; ?>

                            <option value="unassigned">
                                Unassigned
                            </option>

                        </select>

                    </div>





                </div>

            </div>

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
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($residents)): ?>

                                <?php foreach ($residents as $resident): ?>

                                    <?php
                                    $residentImage = !empty($resident['profile_image'])
                                        ? base_url(ltrim($resident['profile_image'], '/\\'))
                                        : base_url('assets/images/resident picture.jpg');

                                    $residentId = 'R-' . str_pad(
                                        (string) $resident['user_id'],
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    );

                                    $isActive = (int) ($resident['is_active'] ?? 1) === 1;
                                    ?>

                                    <tr class="resident-row"
                                        data-user-id="<?= (int) $resident['user_id'] ?>"
                                        data-id="<?= esc($residentId) ?>"
                                        data-name="<?= esc($resident['full_name'] ?? '') ?>"
                                        data-email="<?= esc($resident['email'] ?? '') ?>"
                                        data-contact="<?= esc($resident['mobile_number'] ?? '') ?>"
                                        data-address="<?= esc($resident['address'] ?? '') ?>"
                                        data-purok-id="<?= !empty($resident['purok_id']) ? (int) $resident['purok_id'] : 'unassigned' ?>"
                                        data-status="<?= $isActive ? 'active' : 'inactive' ?>"
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
                                            <?php if (!empty($resident['profile_image'])): ?>

                                                <img
                                                    src="<?= base_url(ltrim($resident['profile_image'], '/\\')) ?>"
                                                    alt="<?= esc($resident['full_name']) ?>"
                                                    class="rounded-circle"
                                                    width="45"
                                                    height="45"
                                                    style="object-fit: cover;">

                                            <?php else: ?>

                                                <div
                                                    class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                                    style="width:45px;height:45px;">
                                                    <i class="bi bi-person-fill text-secondary"></i>
                                                </div>

                                            <?php endif; ?>
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
                                            <?php if ($isActive): ?>

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
                                                ? date('F d, Y', strtotime($resident['created_at']))
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
                                        src="<?= base_url('assets/images/resident picture.jpg') ?>"
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
                                                —
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Username</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalUsername">
                                                —
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Email Address</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalEmail">
                                                —
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Contact Number</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalContact">
                                                —
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Address</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalAddress">
                                                —
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Date Registered</small>
                                            <p class="fw-semibold mb-0"
                                                id="residentModalRegistered">
                                                —
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

                                <label class="form-label">Email</label>

                                <input type="email"
                                    class="form-control"
                                    value="juan@email.com">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Contact Number</label>

                                <input type="text"
                                    class="form-control"
                                    value="09123456789">

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

                            <button class="btn btn-danger">
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

                                    <div class="col-12">

                                        <label class="form-label">
                                            Profile Picture
                                        </label>

                                        <div class="d-flex align-items-center gap-3 flex-wrap">

                                            <div
                                                class="resident-photo-preview d-flex align-items-center justify-content-center"
                                                id="residentPhotoPreview">

                                                <i class="bi bi-person-fill"></i>

                                            </div>

                                            <div class="flex-grow-1">

                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    id="residentProfileImage"
                                                    name="profile_image"
                                                    accept="image/jpeg,image/png,image/webp">

                                                <small class="text-muted">
                                                    Optional. JPG, PNG, or WebP. Maximum 2 MB.
                                                </small>

                                            </div>

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

                                    <select
                                        class="form-select"
                                        name="purok_id"
                                        required>

                                        <option value="" selected disabled>
                                            Select Purok
                                        </option>

                                        <?php foreach (($puroks ?? []) as $purok): ?>

                                            <option
                                                value="<?= (int) $purok['purok_id'] ?>">

                                                <?= esc($purok['purok_name']) ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-md-7">

                                    <label class="form-label">
                                        Additional Address Details
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="address"
                                        placeholder="e.g. Near barangay hall, beside sari-sari store"
                                        value="<?= esc(old('address') ?? '') ?>"
                                        required>

                                    <small class="text-muted">
                                        Landmark, street, house location, or other helpful details
                                    </small>

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

                                <div class="col-md-12">

                                    <label class="form-label">
                                        Username
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="username"
                                        placeholder="Choose a unique username"
                                        value="<?= esc(old('username') ?? '') ?>"
                                        autocomplete="off"
                                        required>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Password
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">

                                        <input
                                            type="password"
                                            class="form-control resident-password"
                                            id="residentPassword"
                                            name="password"
                                            placeholder="Minimum 8 characters"
                                            minlength="8"
                                            autocomplete="new-password"
                                            required>

                                        <button
                                            class="btn btn-outline-secondary toggle-resident-password"
                                            type="button"
                                            data-target="residentPassword"
                                            title="Show Password">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Confirm Password
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">

                                        <input
                                            type="password"
                                            class="form-control resident-password"
                                            id="residentConfirmPassword"
                                            name="confirm_password"
                                            placeholder="Re-enter password"
                                            minlength="8"
                                            autocomplete="new-password"
                                            required>

                                        <button
                                            class="btn btn-outline-secondary toggle-resident-password"
                                            type="button"
                                            data-target="residentConfirmPassword"
                                            title="Show Password">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                            <div class="alert alert-light border mt-3 mb-0">

                                <div class="d-flex">

                                    <i class="bi bi-info-circle-fill text-success me-2"></i>

                                    <small>
                                        The resident account will be
                                        <strong>Active</strong> immediately after creation
                                        and can use these credentials to log in.
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
                            class="btn btn-success px-4">

                            <i class="bi bi-person-check-fill me-1"></i>
                            Create Resident Account

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

</body>

</html>