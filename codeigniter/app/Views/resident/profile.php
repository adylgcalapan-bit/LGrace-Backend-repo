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

                <?= view('resident/notification_menu') ?>

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

                        <?php
                        $profileImage = !empty($resident['profile_image'])
                            ? base_url(ltrim($resident['profile_image'], '/\\'))
                            : base_url('assets/images/resident picture.jpg');
                        ?>

                        <img src="<?= esc($profileImage) ?>"
                            alt="Resident profile picture"
                            class="rounded-circle"
                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #198754;">

                        <h5 class="mt-3 mb-0">
                            <?= esc($resident['full_name'] ?? 'Resident') ?>
                        </h5>

                        <p class="text-muted">Resident</p>

                    </div>

                    <form id="profileForm"
                        action="<?= site_url('resident/profile/update') ?>"
                        method="POST"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">





                                    Full Name

                                </label>

                                <!-- Full Name -->
                                <input type="text"
                                    class="form-control"
                                    name="full_name"
                                    value="<?= esc($resident['full_name'] ?? '') ?>">

                            </div>
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Address

                                    </label>

                                    <!-- Address -->
                                    <input type="text"
                                        class="form-control"
                                        name="address"
                                        value="<?= esc($resident['address'] ?? '') ?>">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Email Address

                                    </label>

                                    <!-- Email -->
                                    <input type="email"
                                        class="form-control"
                                        name="email"
                                        value="<?= esc($resident['email'] ?? '') ?>">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Contact Number

                                    </label>

                                    <!-- Contact Number -->
                                    <input type="text"
                                        class="form-control"
                                        name="mobile_number"
                                        value="<?= esc($resident['mobile_number'] ?? '') ?>">

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
                                            name="current_password"
                                            placeholder="Enter current password">

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            New Password

                                        </label>

                                        <input type="password"
                                            class="form-control"
                                            id="newPassword"
                                            name="new_password"
                                            placeholder="Enter new password">

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Confirm New Password

                                        </label>

                                        <input type="password"
                                            class="form-control"
                                            id="confirmPassword"
                                            name="confirm_password"
                                            placeholder="Confirm new password">

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Profile Picture

                                        </label>

                                        <input type="file"
                                            class="form-control"
                                            id="profileImage"
                                            name="profile_image"
                                            accept="image/jpeg,image/png,image/webp">

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