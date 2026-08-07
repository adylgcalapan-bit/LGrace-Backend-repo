<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Profile | Community Problems Visibility System</title>

    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->

    <link rel="stylesheet"
          href="<?= base_url('assets/css/porile R.css') ?>">

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

            <li>

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

            <li class="active">

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

            <div>

                <h2>My Profile</h2>

                <p>View and update your account information.</p>

            </div>

        </div>

        <div class="card profile-card">

            <div class="card-header">

                <h4>

                    <i class="bi bi-person-circle"></i>

                    Resident Profile

                </h4>

            </div>

            <div class="card-body">

                <div class="text-center mb-4">
                    <img src="<?= base_url('assets/images/resident picture.jpg') ?>"
                         alt="Resident profile picture"
                         class="rounded-circle"
                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #198754;">
                    <h5 class="mt-3 mb-0">Juan Dela Cruz</h5>
                    <p class="text-muted">Resident</p>
                </div>

                <form id="profileForm">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Full Name

                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="Juan Dela Cruz">

                        </div>
<div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Address

                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="Makilala">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Email Address

                            </label>

                            <input type="email"
                                   class="form-control"
                                   value="juan@email.com">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Contact Number

                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="09123456789">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Barangay

                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="Barangay Saguing"
                                   readonly>
                                                           <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Current Password

                            </label>

                            <input type="password"
                                   class="form-control"
                                   id="currentPassword"
                                   placeholder="Enter current password">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                New Password

                            </label>

                            <input type="password"
                                   class="form-control"
                                   id="newPassword"
                                   placeholder="Enter new password">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Confirm New Password

                            </label>

                            <input type="password"
                                   class="form-control"
                                   id="confirmPassword"
                                   placeholder="Confirm new password">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Profile Picture

                            </label>

                            <input type="file"
                                   class="form-control"
                                   id="profileImage"
                                   accept="image/*">

                        </div>

                        <div class="col-12 mt-4 text-end">

                            <button type="reset"
                                    class="btn btn-secondary">

                                <i class="bi bi-arrow-counterclockwise"></i>

                                Cancel

                            </button>

                            <button type="submit"
                                    class="btn btn-success">

                                <i class="bi bi-check-circle-fill"></i>

                                Save Changes

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

<!-- Custom JavaScript -->

<script defer src="<?= base_url('assets/js/profile R.js') ?>"></script>

</body>

</html>
