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

        <!-- PAGE HEADER -->

        <header class="topbar">

            <div class="welcome">
                <h2>Residents Management</h2>
                <p>View and manage all registered residents.</p>
            </div>

            <div class="top-actions">

                <button class="btn btn-success">
                    <i class="bi bi-person-plus-fill"></i>
                    Add Resident
                </button>

            </div>

        </header>

        <!-- SEARCH & FILTERS -->

        <div class="card border-0 shadow-sm p-4 mb-4">

            <div class="row g-3">

                <div class="col-lg-4">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search resident...">

                </div>

                <div class="col-lg-3">

                    <select class="form-select">

                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>

                    </select>

                </div>

                <div class="col-lg-3">

                    <select class="form-select">

                        <option>All Puroks</option>
                        <option>Purok 1</option>
                        <option>Purok 2</option>
                        <option>Purok 3</option>
                        <option>Purok 4</option>
                        <option>Purok 5</option>

                    </select>

                </div>

                <div class="col-lg-2 d-grid">

                    <button class="btn btn-success">

                        <i class="bi bi-funnel-fill"></i>
                        Filter

                    </button>

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

                            <th>ID</th>

                            <th>Photo</th>

                            <th>Full Name</th>

                            <th>Email</th>

                            <th>Contact</th>

                            <th>Purok</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr class="resident-row"
                            data-id="R-001"
                            data-name="Juan Dela Cruz"
                            data-email="juan@email.com"
                            data-contact="09123456789"
                            data-address="Purok 1, Barangay Saguing"
                            data-status="Active"
                            data-image="https://i.pravatar.cc/150?img=12">

                            <td>R-001</td>

                            <td>
                                <img src="https://i.pravatar.cc/45?img=12"
                                     class="rounded-circle"
                                     width="45"
                                     height="45">
                            </td>

                            <td>Juan Dela Cruz</td>

                            <td>juan@email.com</td>

                            <td>09123456789</td>

                            <td>Purok 1</td>

                            <td>

                                <span class="badge bg-success">

                                    Active

                                </span>

                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary">

                                    <i class="bi bi-eye"></i>

                                </button>

                                <button class="btn btn-sm btn-warning text-white">

                                    <i class="bi bi-pencil-square"></i>

                                </button>

                                <button class="btn btn-sm btn-danger">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                        <tr class="resident-row"
                            data-id="R-002"
                            data-name="Maria Santos"
                            data-email="maria@email.com"
                            data-contact="09987654321"
                            data-address="Purok 3, Barangay Saguing"
                            data-status="Active"
                            data-image="https://i.pravatar.cc/150?img=32">

                            <td>R-002</td>

                            <td>
                                <img src="https://i.pravatar.cc/45?img=32"
                                     class="rounded-circle"
                                     width="45"
                                     height="45">
                            </td>

                            <td>Maria Santos</td>

                            <td>maria@email.com</td>

                            <td>09987654321</td>

                            <td>Purok 3</td>

                            <td>

                                <span class="badge bg-success">

                                    Active

                                </span>

                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>

                        </tr>
                                                <tr class="resident-row"
                            data-id="R-003"
                            data-name="Pedro Ramos"
                            data-email="pedro@email.com"
                            data-contact="09112223344"
                            data-address="Purok 2, Barangay Saguing"
                            data-status="Inactive"
                            data-image="https://i.pravatar.cc/150?img=45">

                            <td>R-003</td>

                            <td>
                                <img src="https://i.pravatar.cc/45?img=45"
                                     class="rounded-circle"
                                     width="45"
                                     height="45">
                            </td>

                            <td>Pedro Ramos</td>

                            <td>pedro@email.com</td>

                            <td>09112223344</td>

                            <td>Purok 2</td>

                            <td>
                                <span class="badge bg-secondary">
                                    Inactive
                                </span>
                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>

                        </tr>

                        <tr class="resident-row"
                            data-id="R-004"
                            data-name="Ana Lopez"
                            data-email="ana@email.com"
                            data-contact="09175556677"
                            data-address="Purok 5, Barangay Saguing"
                            data-status="Active"
                            data-image="https://i.pravatar.cc/150?img=18">

                            <td>R-004</td>

                            <td>
                                <img src="https://i.pravatar.cc/45?img=18"
                                     class="rounded-circle"
                                     width="45"
                                     height="45">
                            </td>

                            <td>Ana Lopez</td>

                            <td>ana@email.com</td>

                            <td>09175556677</td>

                            <td>Purok 5</td>

                            <td>
                                <span class="badge bg-success">
                                    Active
                                </span>
                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>

                        </tr>

                        <tr class="resident-row"
                            data-id="R-005"
                            data-name="Carlos Reyes"
                            data-email="carlos@email.com"
                            data-contact="09998887766"
                            data-address="Purok 4, Barangay Saguing"
                            data-status="Active"
                            data-image="https://i.pravatar.cc/150?img=60">

                            <td>R-005</td>

                            <td>
                                <img src="https://i.pravatar.cc/45?img=60"
                                     class="rounded-circle"
                                     width="45"
                                     height="45">
                            </td>

                            <td>Carlos Reyes</td>

                            <td>carlos@email.com</td>

                            <td>09998887766</td>

                            <td>Purok 4</td>

                            <td>
                                <span class="badge bg-success">
                                    Active
                                </span>
                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>

                        </tr>

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
                <!-- ================= RESIDENT DETAILS MODAL ================= -->

        <div class="modal fade" id="residentModal" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Resident Details
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-4 text-center">

                                <img id="residentModalImage" src="https://i.pravatar.cc/150?img=12"
                                     class="rounded-circle img-fluid mb-3"
                                     alt="Resident">

                            </div>

                            <div class="col-md-8">

                                <p><strong>Resident ID:</strong> <span id="residentModalId">R-001</span></p>
                                <p><strong>Full Name:</strong> <span id="residentModalName">Juan Dela Cruz</span></p>
                                <p><strong>Email:</strong> <span id="residentModalEmail">juan@email.com</span></p>
                                <p><strong>Contact:</strong> <span id="residentModalContact">09123456789</span></p>
                                <p><strong>Address:</strong> <span id="residentModalAddress">Purok 1, Barangay Saguing</span></p>
                                <p><strong>Status:</strong> <span id="residentModalStatus">Active</span></p>

                            </div>

                        </div>

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

<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Residents JavaScript -->

<script src="<?= base_url('assets/js/resident.js') ?>"></script>

</body>
</html>