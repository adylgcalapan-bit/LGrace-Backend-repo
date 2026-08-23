<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Account & Settings |
        <?= esc($settings['system_name'] ?? 'CPVS') ?>
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Resident Profile CSS -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/profile R.css') ?>"
    >
</head>

<body>

<?php

$profileImage = !empty($resident['profile_image'])
    ? base_url(
        ltrim(
            $resident['profile_image'],
            '/\\'
        )
    )
    : base_url(
        'assets/images/resident picture.jpg'
    );

$fullName =
    $resident['full_name']
    ?? 'Resident';

$username =
    $resident['username']
    ?? '';

$email =
    $resident['email']
    ?? '';

$mobileNumber =
    $resident['mobile_number']
    ?? '';

$address =
    $resident['address']
    ?? '';

$currentPurokId = old(
    'purok_id',
    $resident['purok_id'] ?? ''
);

$barangayName =
    $settings['barangay_name']
    ?? 'Barangay Saguing';

$systemName =
    $settings['system_name']
    ?? 'Community Problems Visibility System';

$isActive =
    (int) (
        $resident['is_active']
        ?? 0
    ) === 1;

?>


<div class="profile-layout">

    <!-- =========================================
         RESIDENT SIDEBAR
         ========================================= -->
    <aside
        class="resident-sidebar"
        id="residentSidebar"
    >

        <div class="sidebar-brand">

            <i class="bi bi-geo-alt-fill"></i>

            <span>CPVS</span>

        </div>


        <nav class="resident-menu">

            <a
                href="<?= base_url('resident/dashboard') ?>"
            >
                <i class="bi bi-house-door-fill"></i>

                <span>
                    Dashboard
                </span>
            </a>


            <a
                href="<?= base_url('resident/report') ?>"
            >
                <i class="bi bi-pencil-square"></i>

                <span>
                    Report a Problem
                </span>
            </a>


            <a
                href="<?= base_url('resident/my-reports') ?>"
            >
                <i class="bi bi-file-earmark-text"></i>

                <span>
                    My Reports
                </span>
            </a>


            <a
                href="<?= base_url('resident/notifications') ?>"
            >
                <i class="bi bi-bell-fill"></i>

                <span>
                    Notifications
                </span>
            </a>


            <a
                href="<?= base_url('resident/profile') ?>"
                class="active"
            >
                <i class="bi bi-person-circle"></i>

                <span>
                    My Profile
                </span>
            </a>


            <a
                href="<?= base_url('logout') ?>"
                class="sidebar-logout"
            >
                <i class="bi bi-box-arrow-right"></i>

                <span>
                    Logout
                </span>
            </a>

        </nav>

    </aside>


    <!-- =========================================
         MOBILE SIDEBAR OVERLAY
         ========================================= -->
    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <!-- =========================================
         MAIN CONTENT
         ========================================= -->
    <main class="profile-main">


        <!-- =====================================
             FLASH MESSAGES
             ===================================== -->
        <?php if (session()->getFlashdata('success')): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >

                <i class="bi bi-check-circle-fill me-2"></i>

                <?= esc(
                    session()->getFlashdata('success')
                ) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>

            </div>

        <?php endif; ?>


        <?php if (session()->getFlashdata('error')): ?>

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                <?= esc(
                    session()->getFlashdata('error')
                ) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>

            </div>

        <?php endif; ?>


        <!-- =====================================
             PAGE HEADER
             ===================================== -->
        <header class="profile-page-header">

            <!-- MOBILE MENU -->
            <button
                type="button"
                class="mobile-sidebar-button"
                id="mobileSidebarButton"
                aria-label="Open resident navigation"
                aria-expanded="false"
            >

                <i class="bi bi-list"></i>

            </button>


            <!-- TITLE -->
            <div class="page-heading">

                <span class="portal-label">
                    Resident Portal
                </span>

                <h1>
                    Account &amp; Settings
                </h1>

                <p>
                    Manage your personal information,
                    profile photo, password, and account access.
                </p>

            </div>


            <!-- BACK BUTTON -->
            <a
                href="<?= base_url('resident/dashboard') ?>"
                class="dashboard-back-button"
            >

                <i class="bi bi-grid-fill"></i>

                <span>
                    Back to Dashboard
                </span>

            </a>

        </header>


        <!-- =====================================
             PROFILE SUMMARY CARD
             ===================================== -->
        <section class="profile-summary-card">


            <!-- LEFT SUMMARY -->
            <div class="summary-user">

                <div class="summary-avatar-wrap">

                    <img
                        src="<?= esc($profileImage) ?>"
                        alt="Resident profile picture"
                        class="summary-avatar"
                    >

                    <?php if ($isActive): ?>

                        <span
                            class="online-indicator"
                            title="Active account"
                        ></span>

                    <?php endif; ?>

                </div>


                <div class="summary-user-details">

                    <span class="resident-label">
                        Resident Account
                    </span>

                    <h2>
                        <?= esc($fullName) ?>
                    </h2>


                    <div class="summary-username">

                        <i class="bi bi-at"></i>

                        <span>
                            <?= esc(
                                $username !== ''
                                    ? $username
                                    : 'No username'
                            ) ?>
                        </span>

                    </div>


                    <div class="summary-badges">

                        <span class="account-badge">

                            <i class="bi bi-person-check-fill"></i>

                            Resident

                        </span>


                        <span
                            class="
                                account-badge
                                <?= $isActive
                                    ? 'active-status'
                                    : 'inactive-status'
                                ?>
                            "
                        >

                            <i
                                class="
                                    bi
                                    <?= $isActive
                                        ? 'bi-check-circle-fill'
                                        : 'bi-x-circle-fill'
                                    ?>
                                "
                            ></i>

                            <?= $isActive
                                ? 'Active Account'
                                : 'Inactive Account'
                            ?>

                        </span>

                    </div>

                </div>

            </div>


            <!-- RIGHT SUMMARY -->
            <div class="summary-contact-grid">


                <!-- EMAIL -->
                <div class="summary-contact-card">

                    <div class="summary-contact-icon">

                        <i class="bi bi-envelope-fill"></i>

                    </div>


                    <div class="summary-contact-text">

                        <span>
                            Email
                        </span>

                        <strong>
                            <?= esc(
                                $email !== ''
                                    ? $email
                                    : 'Not provided'
                            ) ?>
                        </strong>

                    </div>

                </div>


                <!-- CONTACT -->
                <div class="summary-contact-card">

                    <div class="summary-contact-icon">

                        <i class="bi bi-telephone-fill"></i>

                    </div>


                    <div class="summary-contact-text">

                        <span>
                            Contact
                        </span>

                        <strong>
                            <?= esc(
                                $mobileNumber !== ''
                                    ? $mobileNumber
                                    : 'Not provided'
                            ) ?>
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================
             PROFILE UPDATE FORM
             ===================================== -->
        <form
            id="profileForm"
            action="<?= site_url('resident/profile/update') ?>"
            method="POST"
            enctype="multipart/form-data"
        >

            <?= csrf_field() ?>


            <div class="profile-content-grid">


                <!-- =================================
                     LEFT / MAIN COLUMN
                     ================================= -->
                <div class="profile-primary-column">


                    <!-- =============================
                         PERSONAL INFORMATION
                         ============================= -->
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="section-icon green-icon">

                                <i class="bi bi-person-vcard-fill"></i>

                            </div>


                            <div>

                                <h3>
                                    Personal Information
                                </h3>

                                <p>
                                    Keep your account information
                                    accurate and up to date.
                                </p>

                            </div>

                        </div>


                        <div class="settings-card-body">

                            <div class="form-grid">


                                <!-- FULL NAME -->
                                <div class="form-field">

                                    <label for="full_name">

                                        Full Name

                                        <span>*</span>

                                    </label>


                                    <div class="input-with-icon">

                                        <i class="bi bi-person"></i>

                                        <input
                                            type="text"
                                            id="full_name"
                                            name="full_name"
                                            maxlength="150"
                                            required
                                            value="<?= esc(
                                                old(
                                                    'full_name',
                                                    $fullName
                                                )
                                            ) ?>"
                                        >

                                    </div>

                                </div>


                                <!-- USERNAME -->
                                <div class="form-field">

                                    <label for="username">
                                        Username
                                    </label>


                                    <div
                                        class="
                                            input-with-icon
                                            readonly-field
                                        "
                                    >

                                        <i class="bi bi-at"></i>

                                        <input
                                            type="text"
                                            id="username"
                                            value="<?= esc($username) ?>"
                                            readonly
                                        >

                                    </div>


                                    <small>
                                        Username cannot be changed here.
                                    </small>

                                </div>


                                <!-- EMAIL -->
                                <div class="form-field">

                                    <label for="email">

                                        Email Address

                                        <span>*</span>

                                    </label>


                                    <div class="input-with-icon">

                                        <i class="bi bi-envelope"></i>

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            maxlength="150"
                                            required
                                            value="<?= esc(
                                                old(
                                                    'email',
                                                    $email
                                                )
                                            ) ?>"
                                        >

                                    </div>

                                </div>


                                <!-- MOBILE NUMBER -->
                                <div class="form-field">

                                    <label for="mobile_number">
                                        Mobile Number
                                    </label>


                                    <div class="input-with-icon">

                                        <i class="bi bi-phone"></i>

                                        <input
                                            type="text"
                                            id="mobile_number"
                                            name="mobile_number"
                                            maxlength="30"
                                            value="<?= esc(
                                                old(
                                                    'mobile_number',
                                                    $mobileNumber
                                                )
                                            ) ?>"
                                        >

                                    </div>

                                </div>


                                <!-- PUROK -->
                                <div class="form-field">

                                    <label for="purok_id">
                                        Purok
                                    </label>


                                    <div
                                        class="
                                            input-with-icon
                                            select-field
                                        "
                                    >

                                        <i class="bi bi-geo-alt"></i>

                                        <select
                                            id="purok_id"
                                            name="purok_id"
                                        >

                                            <option value="">
                                                Select your Purok
                                            </option>


                                            <?php foreach (
                                                $puroks ?? []
                                                as $purok
                                            ): ?>

                                                <option
                                                    value="<?= esc(
                                                        $purok['purok_id']
                                                    ) ?>"
                                                    <?= (string) $currentPurokId
                                                        ===
                                                        (string) $purok['purok_id']
                                                        ? 'selected'
                                                        : ''
                                                    ?>
                                                >

                                                    <?= esc(
                                                        $purok['purok_name']
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>

                                </div>


                                <!-- BARANGAY -->
                                <div class="form-field">

                                    <label for="barangay">
                                        Barangay
                                    </label>


                                    <div
                                        class="
                                            input-with-icon
                                            readonly-field
                                        "
                                    >

                                        <i class="bi bi-building"></i>

                                        <input
                                            type="text"
                                            id="barangay"
                                            value="<?= esc(
                                                $barangayName
                                            ) ?>"
                                            readonly
                                        >

                                    </div>

                                </div>


                                <!-- COMPLETE ADDRESS -->
                                <div
                                    class="
                                        form-field
                                        full-width-field
                                    "
                                >

                                    <label for="address">
                                        Complete Address
                                    </label>


                                    <div class="input-with-icon">

                                        <i class="bi bi-signpost-2"></i>

                                        <input
                                            type="text"
                                            id="address"
                                            name="address"
                                            maxlength="255"
                                            placeholder="Enter complete address"
                                            value="<?= esc(
                                                old(
                                                    'address',
                                                    $address
                                                )
                                            ) ?>"
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>


                    <!-- =============================
                         SECURITY AND PASSWORD
                         ============================= -->
                    <section
                        class="
                            settings-card
                            security-card
                        "
                    >

                        <div class="settings-card-header">

                            <div class="section-icon blue-icon">

                                <i class="bi bi-shield-lock-fill"></i>

                            </div>


                            <div>

                                <h3>
                                    Security &amp; Password
                                </h3>

                                <p>
                                    Use a strong password to protect
                                    your resident account.
                                </p>

                            </div>

                        </div>


                        <div class="settings-card-body">


                            <div class="password-note">

                                <i class="bi bi-info-circle-fill"></i>

                                <span>
                                    Leave all password fields blank
                                    if you only want to update
                                    your profile information.
                                </span>

                            </div>


                            <div class="password-grid">


                                <!-- CURRENT PASSWORD -->
                                <div class="form-field">

                                    <label for="currentPassword">
                                        Current Password
                                    </label>


                                    <div class="password-input">

                                        <input
                                            type="password"
                                            id="currentPassword"
                                            name="current_password"
                                            autocomplete="current-password"
                                            placeholder="Current password"
                                        >


                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="currentPassword"
                                            aria-label="Show current password"
                                        >

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>


                                <!-- NEW PASSWORD -->
                                <div class="form-field">

                                    <label for="newPassword">
                                        New Password
                                    </label>


                                    <div class="password-input">

                                        <input
                                            type="password"
                                            id="newPassword"
                                            name="new_password"
                                            minlength="8"
                                            autocomplete="new-password"
                                            placeholder="Minimum 8 characters"
                                        >


                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="newPassword"
                                            aria-label="Show new password"
                                        >

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>


                                <!-- CONFIRM PASSWORD -->
                                <div class="form-field">

                                    <label for="confirmPassword">
                                        Confirm Password
                                    </label>


                                    <div class="password-input">

                                        <input
                                            type="password"
                                            id="confirmPassword"
                                            name="confirm_password"
                                            minlength="8"
                                            autocomplete="new-password"
                                            placeholder="Repeat new password"
                                        >


                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-target="confirmPassword"
                                            aria-label="Show confirmed password"
                                        >

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>


                    <!-- =============================
                         SAVE SETTINGS CARD
                         ============================= -->
                    <section class="save-settings-card">


                        <div class="save-settings-text">

                            <strong>
                                Save your account changes
                            </strong>

                            <p>
                                Review your information before saving.
                            </p>

                        </div>


                        <div class="save-actions">


                            <button
                                type="reset"
                                class="reset-profile-button"
                            >

                                <i class="bi bi-arrow-counterclockwise"></i>

                                Reset

                            </button>


                            <button
                                type="submit"
                                class="save-profile-button"
                            >

                                <i class="bi bi-check-circle-fill"></i>

                                Save Changes

                            </button>

                        </div>

                    </section>

                </div>


                <!-- =================================
                     RIGHT COLUMN
                     ================================= -->
                <aside class="profile-secondary-column">


                    <!-- =============================
                         PROFILE PHOTO
                         ============================= -->
                    <section class="side-settings-card">

                        <div class="side-card-header">

                            <div class="section-icon amber-icon">

                                <i class="bi bi-camera-fill"></i>

                            </div>


                            <div>

                                <h3>
                                    Profile Photo
                                </h3>

                                <p>
                                    Personalize your resident account.
                                </p>

                            </div>

                        </div>


                        <div class="profile-photo-content">

                            <img
                                src="<?= esc($profileImage) ?>"
                                alt="Current resident profile picture"
                                class="profile-photo-preview"
                                id="profilePhotoPreview"
                            >


                            <input
                                type="file"
                                id="profileImage"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp"
                                hidden
                            >


                            <label
                                for="profileImage"
                                class="choose-photo-button"
                            >

                                <i class="bi bi-upload"></i>

                                Choose New Photo

                            </label>


                            <small>
                                JPG, PNG, or WebP only.
                                Maximum file size: 5 MB.
                            </small>

                        </div>

                    </section>


                    <!-- =============================
                         NOTIFICATIONS
                         ============================= -->
                    <section class="side-settings-card">

                        <div class="side-card-header">

                            <div class="section-icon purple-icon">

                                <i class="bi bi-bell-fill"></i>

                            </div>


                            <div>

                                <h3>
                                    Notifications
                                </h3>

                                <p>
                                    Stay updated about your reports.
                                </p>

                            </div>

                        </div>


                        <div class="side-card-content">

                            <div class="notification-row">

                                <div class="mini-purple-icon">

                                    <i class="bi bi-megaphone-fill"></i>

                                </div>


                                <div>

                                    <strong>
                                        Notification Center
                                    </strong>

                                    <p>
                                        View report status updates
                                        and system notifications.
                                    </p>

                                </div>

                            </div>


                            <a
                                href="<?= base_url(
                                    'resident/notifications'
                                ) ?>"
                                class="open-notification-button"
                            >

                                <i class="bi bi-bell"></i>

                                Open Notifications

                            </a>

                        </div>

                    </section>


                    <!-- =============================
                         SESSION
                         ============================= -->
                    <section class="side-settings-card">

                        <div class="side-card-header">

                            <div class="section-icon gray-icon">

                                <i class="bi bi-key-fill"></i>

                            </div>


                            <div>

                                <h3>
                                    Session
                                </h3>

                                <p>
                                    Manage your current login session.
                                </p>

                            </div>

                        </div>


                        <div class="side-card-content">

                            <a
                                href="<?= base_url('logout') ?>"
                                class="logout-account-button"
                            >

                                <i class="bi bi-box-arrow-right"></i>

                                Log Out of Account

                            </a>

                        </div>

                    </section>

                </aside>

            </div>

        </form>


        <!-- =====================================
             DANGER ZONE
             ===================================== -->
        <section class="danger-zone-card">

            <div class="danger-zone-content">

                <div class="danger-icon">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                </div>


                <div>

                    <span>
                        Danger Zone
                    </span>

                    <strong>
                        Delete Resident Account
                    </strong>

                    <p>
                        Permanently remove your resident account.
                        This action cannot be undone.
                    </p>

                </div>

            </div>


            <button
                type="button"
                class="delete-account-button"
                data-bs-toggle="modal"
                data-bs-target="#deleteAccountModal"
            >

                <i class="bi bi-trash3"></i>

                Delete Account

            </button>

        </section>


        <!-- =====================================
             FOOTER
             ===================================== -->
        <footer class="profile-footer">

            <span>
                &copy; 2026
                <?= esc($systemName) ?>
            </span>

            <span class="footer-dot">
                •
            </span>

            <span>
                <?= esc($barangayName) ?>
            </span>

        </footer>

    </main>

</div>


<!-- =========================================
     DELETE ACCOUNT MODAL
     ========================================= -->
<div
    class="modal fade"
    id="deleteAccountModal"
    tabindex="-1"
    aria-labelledby="deleteAccountModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title text-danger"
                    id="deleteAccountModalLabel"
                >

                    <i class="bi bi-exclamation-octagon-fill me-2"></i>

                    Permanently Delete Account

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <form
                action="<?= site_url(
                    'resident/account/delete'
                ) ?>"
                method="POST"
            >

                <?= csrf_field() ?>


                <div class="modal-body">

                    <div class="alert alert-danger">

                        <strong>
                            This action cannot be undone.
                        </strong>

                        Your resident account and related
                        system records will be permanently removed.

                    </div>


                    <div class="mb-3">

                        <label
                            for="deleteCurrentPassword"
                            class="form-label fw-semibold"
                        >
                            Current Password
                        </label>


                        <input
                            type="password"
                            class="form-control"
                            id="deleteCurrentPassword"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                    <div class="mb-2">

                        <label
                            for="deleteConfirmation"
                            class="form-label fw-semibold"
                        >

                            Type

                            <strong>
                                DELETE
                            </strong>

                            to confirm

                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="deleteConfirmation"
                            name="delete_confirmation"
                            placeholder="DELETE"
                            autocomplete="off"
                            required
                        >

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-trash3-fill me-1"></i>

                        Permanently Delete

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<!-- Resident Profile JS -->
<script
    defer
    src="<?= base_url('assets/js/profile R.js') ?>"
></script>

</body>

</html>