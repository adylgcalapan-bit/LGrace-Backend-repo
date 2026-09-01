<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
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
        <aside class="sidebar">
            <div class="logo">
                <i class="bi bi-geo-alt-fill"></i>
                <h4>CPVS</h4>
            </div>
            <ul class="menu">
                <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
                <li><a href="<?= base_url('admin/reports') ?>"><i class="bi bi-file-earmark-text"></i>Reports</a></li>
                <li><a href="<?= base_url('admin/map') ?>"><i class="bi bi-map"></i>Map View</a></li>
                <li><a href="<?= base_url('admin/residents') ?>"><i class="bi bi-people"></i>Residents</a></li>
                <li><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
                <?= view('admin/notification_menu') ?>
                <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
                <li class="active"><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account</a></li>
                <li class="logout"><a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <!-- ACCOUNT PAGE HEADER -->
            <?php
            $topAdminName = trim((string) ($admin['full_name'] ?? 'System Admin'));

            $topAdminPhoto = !empty($admin['profile_image'])
                ? base_url(ltrim($admin['profile_image'], '/\\'))
                : base_url('assets/images/admin picture.jpg');
            ?>

            <header class="topbar account-topbar">

                <div class="account-page-heading">

                    <div class="account-eyebrow">
                        <i class="bi bi-person-badge-fill"></i>
                        Administrator Account
                    </div>

                    <h2>Account</h2>

                    <p>
                        Manage your administrator profile,
                        account information, and security credentials.
                    </p>

                </div>

                <div class="account-admin-profile">

                    <div class="account-admin-avatar">

                        <img
                            id="topProfileImage"
                            src="<?= esc($topAdminPhoto) ?>"
                            alt="<?= esc($topAdminName) ?>">

                    </div>

                    <div class="account-admin-info">

                        <strong>
                            <?= esc($topAdminName) ?>
                        </strong>

                        <span>
                            <i class="bi bi-shield-check"></i>
                            Administrator
                        </span>

                    </div>

                </div>

            </header>

            <?php
            $adminName = trim((string) ($admin['full_name'] ?? 'System Admin'));

            $adminPhoto = ! empty($admin['profile_image'])
                ? base_url(ltrim($admin['profile_image'], '/'))
                : base_url('assets/images/admin picture.jpg');

            $accountStatus = ! empty($admin['is_active'])
                ? 'Active'
                : 'Inactive';

            $memberSince = ! empty($admin['created_at'])
                ? format_system_date($admin['created_at'])
                : 'Not available';
            ?>



            <!-- PROFILE & ACCOUNT INFORMATION -->
            <section class="account-section-card mb-4">

                <div class="account-section-header">

                    <div class="account-section-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>

                    <div>
                        <h4>Account Information</h4>
                        <p>
                            Manage the administrator profile photo,
                            identity, and contact information.
                        </p>
                    </div>

                </div>

                <div class="account-section-body">

                    <div class="profile-account-layout">

                        <!-- PROFILE PHOTO -->
                        <aside class="profile-account-photo">

                            <div class="profile-photo-preview-wrap">

                                <img
                                    id="profilePreview"
                                    class="profile-photo-preview"
                                    src="<?= esc($adminPhoto) ?>"
                                    alt="Administrator Profile">

                                <span class="profile-photo-status">
                                    <i class="bi bi-check-lg"></i>
                                </span>

                            </div>

                            <div class="profile-photo-identity">

                                <strong><?= esc($adminName) ?></strong>

                                <span>
                                    <i class="bi bi-shield-check"></i>
                                    Administrator
                                </span>

                            </div>
                            <div class="admin-photo-file-picker">
                                <label for="profileImageInput" class="form-label">
                                    Choose Profile Photo
                                </label>

                                <input
                                    type="file"
                                    id="profileImageInput"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            </div>

                            <span
                                id="selectedProfileFile"
                                class="profile-file-name">
                                JPG, PNG, or WebP · Maximum 2 MB
                            </span>

                            <div class="profile-photo-actions">

                                <button
                                    type="button"
                                    class="btn upload-photo-btn"
                                    id="uploadPhotoBtn"
                                    data-photo-url="<?= site_url('admin/account/photo') ?>"
                                    data-csrf-name="<?= esc(csrf_token()) ?>"
                                    data-csrf-hash="<?= esc(csrf_hash()) ?>">

                                    <i class="bi bi-upload"></i>
                                    Upload Photo

                                </button>

                                <button
                                    type="button"
                                    class="btn profile-cancel-btn"
                                    id="cancelPhotoBtn">

                                    Cancel

                                </button>

                            </div>

                            <small class="profile-photo-note">
                                JPG, PNG or WEBP. Use a clear square image.
                            </small>

                        </aside>


                        <!-- ACCOUNT INFORMATION -->
                        <div class="profile-account-fields">

                            <div class="account-form-grid">

                                <div class="account-form-group">

                                    <label
                                        for="admin_full_name"
                                        class="form-label">
                                        Full Name
                                    </label>

                                    <div class="account-input-wrap">

                                        <i class="bi bi-person"></i>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="admin_full_name"
                                            name="full_name"
                                            value="<?= esc($admin['full_name'] ?? '') ?>">

                                    </div>

                                </div>


                                <div class="account-form-group">

                                    <label
                                        for="admin_username"
                                        class="form-label">
                                        Username
                                    </label>

                                    <div class="account-input-wrap">

                                        <i class="bi bi-at"></i>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="admin_username"
                                            name="username"
                                            value="<?= esc($admin['username'] ?? '') ?>">

                                    </div>

                                </div>


                                <div class="account-form-group">

                                    <label
                                        for="admin_email"
                                        class="form-label">
                                        Email Address
                                    </label>

                                    <div class="account-input-wrap">

                                        <i class="bi bi-envelope"></i>

                                        <input
                                            type="email"
                                            class="form-control"
                                            id="admin_email"
                                            name="email"
                                            value="<?= esc($admin['email'] ?? '') ?>">


                                    </div>

                                    <!-- ADMIN EMAIL CHANGE VERIFICATION -->
                                    <div class="mt-2">

                                        <div class="d-flex gap-2 align-items-center">

                                            <button
                                                type="button"
                                                id="sendAdminEmailCodeBtn"
                                                class="btn btn-outline-success btn-sm"
                                                data-send-url="<?= site_url('admin/account/send-email-code') ?>">
                                                Send Code
                                            </button>

                                            <span
                                                id="adminEmailVerificationStatus"
                                                class="small text-muted">
                                                Current email is verified
                                            </span>

                                        </div>

                                    </div>

                                    <div
                                        id="adminEmailCodeSection"
                                        class="mt-3"
                                        style="display: none;">

                                        <label
                                            for="adminEmailCode"
                                            class="form-label">
                                            Verification Code
                                        </label>

                                        <div class="d-flex gap-2">

                                            <input
                                                type="text"
                                                id="adminEmailCode"
                                                class="form-control"
                                                maxlength="6"
                                                inputmode="numeric"
                                                autocomplete="one-time-code"
                                                placeholder="Enter 6-digit code">

                                            <button
                                                type="button"
                                                id="verifyAdminEmailCodeBtn"
                                                class="btn btn-success btn-sm"
                                                data-verify-url="<?= site_url('admin/account/verify-email-code') ?>">
                                                Verify
                                            </button>

                                        </div>

                                        <div
                                            id="adminEmailCodeMessage"
                                            class="small mt-2">
                                        </div>

                                    </div>
                                    <!-- END ADMIN EMAIL CHANGE VERIFICATION -->

                                </div>


                                <div class="account-form-group">

                                    <label
                                        for="admin_mobile_number"
                                        class="form-label">
                                        Contact Number
                                    </label>

                                    <div class="account-input-wrap">

                                        <i class="bi bi-telephone"></i>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="admin_mobile_number"
                                            name="mobile_number"
                                            value="<?= esc($admin['mobile_number'] ?? '') ?>">

                                    </div>

                                </div>


                                <div class="account-form-group account-form-full">

                                    <label
                                        for="admin_address"
                                        class="form-label">
                                        Address
                                    </label>

                                    <div class="account-input-wrap">

                                        <i class="bi bi-geo-alt"></i>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="admin_address"
                                            name="address"
                                            value="<?= esc($admin['address'] ?? '') ?>">

                                    </div>

                                </div>

                            </div>

                            <div class="account-information-note">

                                <i class="bi bi-info-circle"></i>

                                <span>
                                    Keep administrator information accurate
                                    for account and system communication.
                                </span>

                            </div>

                            <div class="profile-account-actions">

                                <div class="profile-save-note">
                                    <i class="bi bi-shield-check"></i>
                                    <span>
                                        Changes will update your administrator account information.
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    class="btn save-profile-btn"
                                    id="saveChangesBtn"
                                    data-update-url="<?= site_url('admin/account/update') ?>"
                                    data-csrf-name="<?= esc(csrf_token()) ?>"
                                    data-csrf-hash="<?= esc(csrf_hash()) ?>">

                                    <i class="bi bi-check-lg"></i>
                                    Save Profile Changes

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

            <!-- SECURITY & ACCESS -->
            <section class="account-section-card mb-4">

                <div class="account-section-header">

                    <div class="account-section-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>

                    <div>
                        <h4>Security & Access</h4>
                        <p>
                            Review administrator access information
                            and manage account credentials.
                        </p>
                    </div>

                </div>


                <div class="account-section-body">

                    <!-- ACCOUNT ACCESS -->
                    <div class="security-access-summary">

                        <div class="security-access-item">

                            <div class="security-access-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div>
                                <span>Account Role</span>
                                <strong>Administrator</strong>
                            </div>

                        </div>


                        <div class="security-access-item">

                            <div class="security-access-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>

                            <div>
                                <span>Account Status</span>

                                <strong class="<?= !empty($admin['is_active'])
                                                    ? 'access-status-active'
                                                    : 'access-status-inactive' ?>">

                                    <?= !empty($admin['is_active'])
                                        ? 'Active'
                                        : 'Inactive' ?>

                                </strong>

                            </div>

                        </div>


                        <div class="security-access-item">

                            <div class="security-access-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>

                            <div>
                                <span>Member Since</span>
                                <strong><?= esc($memberSince) ?></strong>
                            </div>

                        </div>

                    </div>


                    <div class="security-section-divider"></div>


                    <!-- PASSWORD SECURITY -->
                    <div class="password-security-header">

                        <div>
                            <h5>Password Security</h5>

                            <p>
                                Use a strong password to protect
                                administrator access to the system.
                            </p>
                        </div>

                        <span class="security-protected-badge">
                            <i class="bi bi-shield-check"></i>
                            Protected Account
                        </span>

                    </div>


                    <div class="password-fields-grid">

                        <!-- CURRENT PASSWORD -->
                        <div class="password-field-group">

                            <label
                                for="currentPassword"
                                class="form-label">
                                Current Password
                            </label>

                            <div class="password-input-wrap">

                                <i class="bi bi-lock"></i>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="currentPassword"
                                    autocomplete="current-password"
                                    placeholder="Enter current password">

                                <button
                                    type="button"
                                    class="password-toggle-btn"
                                    data-target="currentPassword"
                                    aria-label="Show current password">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>


                        <!-- NEW PASSWORD -->
                        <div class="password-field-group">

                            <label
                                for="newPassword"
                                class="form-label">
                                New Password
                            </label>

                            <div class="password-input-wrap">

                                <i class="bi bi-key"></i>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="newPassword"
                                    autocomplete="new-password"
                                    placeholder="Enter new password">

                                <button
                                    type="button"
                                    class="password-toggle-btn"
                                    data-target="newPassword"
                                    aria-label="Show new password">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>


                        <!-- CONFIRM PASSWORD -->
                        <div class="password-field-group">

                            <label
                                for="confirmPassword"
                                class="form-label">
                                Confirm New Password
                            </label>

                            <div class="password-input-wrap">

                                <i class="bi bi-shield-check"></i>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirmPassword"
                                    autocomplete="new-password"
                                    placeholder="Confirm new password">

                                <button
                                    type="button"
                                    class="password-toggle-btn"
                                    data-target="confirmPassword"
                                    aria-label="Show confirmed password">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>


                    <div class="password-security-footer">

                        <div class="password-requirements">

                            <i class="bi bi-info-circle"></i>

                            <span>
                                Use at least 8 characters with a combination
                                of letters, numbers, and symbols.
                            </span>

                        </div>

                        <button
                            type="button"
                            class="btn change-password-btn"
                            id="changePasswordBtn"
                            data-password-url="<?= site_url('admin/account/password') ?>"
                            data-csrf-name="<?= esc(csrf_token()) ?>"
                            data-csrf-hash="<?= esc(csrf_hash()) ?>">

                            <i class="bi bi-key"></i>
                            Change Password

                        </button>

                    </div>

                </div>

            </section>
        </main>
    </div>

    <script>
        document.querySelectorAll('.password-toggle-btn').forEach(function(button) {
            button.onclick = function(event) {
                event.preventDefault();

                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';

                    if (icon) {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                } else {
                    input.type = 'password';

                    if (icon) {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                }
            };
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/account.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin-responsive.js') ?>"></script>
</body>

</html>