<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Account & Settings |
        <?= esc($settings['system_name'] ?? 'CPVS') ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/profile R.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/resident-sidebar.css') ?>">
</head>

<body>

    <div class="wrapper">

        <!-- =========================================
         SIDEBAR
    ========================================== -->
        <aside class="sidebar">

            <div class="logo">
                <i class="bi bi-geo-alt-fill"></i>

                <h4>
                    <?= esc(
                        $settings['system_name']
                            ?? 'Community Visibility System'
                    ) ?>
                </h4>
            </div>

            <ul class="menu">

                <li>
                    <a href="<?= base_url('resident/dashboard') ?>">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('resident/report') ?>">
                        <i class="bi bi-pencil-square"></i>
                        <span>Report a Problem</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url('resident/my-reports') ?>">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>My Reports</span>
                    </a>
                </li>

                <?= view('resident/notification_menu') ?>

                <li class="active">
                    <a href="<?= base_url('resident/profile') ?>">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li class="logout">
                    <a href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>

        </aside>


        <!-- =========================================
         MAIN CONTENT
    ========================================== -->
        <main class="main-content">

            <!-- FLASH MESSAGES -->
            <div id="profile-flash-messages">

                <?php if (session()->getFlashdata('success')): ?>

                    <div
                        class="alert alert-success alert-dismissible fade show account-alert"
                        role="alert">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        <?= esc(session()->getFlashdata('success')) ?>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                        </button>

                    </div>

                <?php endif; ?>


                <?php if (session()->getFlashdata('error')): ?>

                    <div
                        class="alert alert-danger alert-dismissible fade show account-alert"
                        role="alert">

                        <i class="bi bi-exclamation-circle-fill me-2"></i>

                        <?= esc(session()->getFlashdata('error')) ?>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                        </button>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =========================================
             PAGE HEADER
        ========================================== -->
            <section class="page-header">

                <div>
                    <span class="page-eyebrow">
                        Resident Portal
                    </span>

                    <h1>Account & Settings</h1>

                    <p>
                        Manage your personal information, profile photo,
                        password, and account access.
                    </p>
                </div>

                <a
                    href="<?= base_url('resident/dashboard') ?>"
                    class="btn btn-light dashboard-button">

                    <i class="bi bi-grid-1x2-fill"></i>
                    Back to Dashboard
                </a>

            </section>


            <?php

            $profileImage = !empty($resident['profile_image'])
                ? base_url(
                    ltrim(
                        $resident['profile_image'],
                        '/\\'
                    )
                )
                : base_url(
                    'assets/images/resident picture.png'
                );

            $residentName =
                $resident['full_name']
                ?? 'Resident';

            $residentUsername =
                $resident['username']
                ?? '';

            $residentEmail =
                $resident['email']
                ?? '';

            $isActive =
                (int) ($resident['is_active'] ?? 1) === 1;

            ?>


            <!-- =========================================
             PROFILE SUMMARY
        ========================================== -->
            <section class="profile-summary-card">

                <div class="profile-summary-left">

                    <div class="profile-avatar-wrapper">

                        <img
                            id="profilePreview"
                            src="<?= esc($profileImage) ?>"
                            data-original-src="<?= esc($profileImage) ?>"
                            alt="Resident profile picture"
                            class="profile-avatar">

                        <span class="profile-status-dot"></span>

                    </div>

                    <div class="profile-summary-info">

                        <span class="profile-label">
                            Resident Account
                        </span>

                        <h2>
                            <?= esc($residentName) ?>
                        </h2>

                        <p>
                            <i class="bi bi-at"></i>
                            <?= esc($residentUsername ?: 'No username') ?>
                        </p>

                        <div class="profile-badges">

                            <span class="account-badge resident-badge">
                                <i class="bi bi-person-check-fill"></i>
                                Resident
                            </span>

                            <span class="account-badge <?= $isActive
                                                            ? 'active-badge'
                                                            : 'inactive-badge' ?>">

                                <i class="bi <?= $isActive
                                                    ? 'bi-check-circle-fill'
                                                    : 'bi-x-circle-fill' ?>"></i>

                                <?= $isActive
                                    ? 'Active Account'
                                    : 'Inactive Account' ?>

                            </span>

                        </div>

                    </div>

                </div>

                <div class="profile-summary-meta">

                    <div class="summary-meta-item">
                        <i class="bi bi-envelope-fill"></i>

                        <div>
                            <span>Email</span>
                            <strong>
                                <?= esc(
                                    $residentEmail
                                        ?: 'Not provided'
                                ) ?>
                            </strong>
                        </div>
                    </div>

                    <div class="summary-meta-item">
                        <i class="bi bi-telephone-fill"></i>

                        <div>
                            <span>Contact</span>
                            <strong>
                                <?= esc(
                                    $resident['mobile_number']
                                        ?? 'Not provided'
                                ) ?>
                            </strong>
                        </div>
                    </div>

                </div>

            </section>


            <!-- =========================================
             ACCOUNT FORM
        ========================================== -->
            <form
                id="profileForm"
                action="<?= site_url('resident/profile/update') ?>"
                method="POST"
                enctype="multipart/form-data">

                <?= csrf_field() ?>

                <div class="settings-layout">

                    <!-- LEFT COLUMN -->
                    <div class="settings-main-column">


                        <!-- =========================================
                         PERSONAL INFORMATION
                    ========================================== -->
                        <section class="settings-card">

                            <div class="settings-card-header">

                                <div class="settings-icon">
                                    <i class="bi bi-person-vcard-fill"></i>
                                </div>

                                <div>
                                    <h3>Personal Information</h3>

                                    <p>
                                        Keep your account information accurate
                                        and up to date.
                                    </p>
                                </div>

                            </div>


                            <div class="settings-card-body">

                                <div class="row g-4">

                                    <div class="col-md-6">

                                        <label
                                            for="full_name"
                                            class="form-label">

                                            Full Name
                                            <span class="required">*</span>
                                        </label>

                                        <div class="input-with-icon">

                                            <i class="bi bi-person"></i>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="full_name"
                                                name="full_name"
                                                maxlength="100"
                                                required
                                                value="<?= esc(
                                                            old(
                                                                'full_name',
                                                                $resident['full_name'] ?? ''
                                                            )
                                                        ) ?>">

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <label
                                            for="username"
                                            class="form-label">

                                            Username
                                        </label>

                                        <div class="input-with-icon readonly-field">

                                            <i class="bi bi-at"></i>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="username"
                                                name="username"
                                                value="<?= esc(
                                                            old(
                                                                'username',
                                                                $resident['username'] ?? ''
                                                            )
                                                        ) ?>"
                                                placeholder="Create your username"
                                                minlength="4"
                                                maxlength="30">

                                        </div>

                                        <small class="field-help">
                                            Choose a unique username with 4 to 30 characters and no spaces.
                                        </small>

                                    </div>


                                    <div class="col-md-6">

                                        <label
                                            for="email"
                                            class="form-label">

                                            Email Address
                                            <span class="required">*</span>
                                        </label>

                                        <div class="input-with-icon">

                                            <i class="bi bi-envelope"></i>

                                            <input
                                                type="email"
                                                class="form-control"
                                                id="email"
                                                name="email"
                                                maxlength="150"
                                                required
                                                value="<?= esc(
                                                            old(
                                                                'email',
                                                                $resident['email']
                                                                    ?? ''
                                                            )
                                                        ) ?>">

                                        </div>

                                        <!-- EMAIL CHANGE VERIFICATION -->
                                        <div class="d-flex align-items-center gap-3 mt-2">

                                            <button
                                                type="button"
                                                id="sendProfileEmailCodeBtn"
                                                class="btn btn-outline-success btn-sm"
                                                style="white-space: nowrap;">
                                                Send Code
                                            </button>

                                            <span
                                                id="profileEmailVerificationStatus"
                                                class="small text-muted text-nowrap">
                                                Current email is verified
                                            </span>

                                        </div>

                                        <div
                                            id="profileEmailCodeSection"
                                            class="mt-3"
                                            style="display: none;">

                                            <label
                                                for="profileEmailCode"
                                                class="form-label">
                                                Verification Code
                                            </label>

                                            <div class="d-flex gap-2">

                                                <input
                                                    type="text"
                                                    id="profileEmailCode"
                                                    class="form-control"
                                                    maxlength="6"
                                                    inputmode="numeric"
                                                    autocomplete="one-time-code"
                                                    placeholder="Enter 6-digit code">

                                                <button
                                                    type="button"
                                                    id="verifyProfileEmailCodeBtn"
                                                    class="btn btn-success btn-sm">
                                                    Verify
                                                </button>

                                            </div>

                                            <div
                                                id="profileEmailCodeMessage"
                                                class="small mt-2">
                                            </div>

                                        </div>
                                        <!-- END EMAIL CHANGE VERIFICATION -->


                                    </div>


                                    <div class="col-md-6">

                                        <label
                                            for="mobile_number"
                                            class="form-label">

                                            Mobile Number
                                        </label>

                                        <div class="input-with-icon">

                                            <i class="bi bi-phone"></i>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="mobile_number"
                                                name="mobile_number"
                                                maxlength="20"
                                                placeholder="09XXXXXXXXX"
                                                value="<?= esc(
                                                            old(
                                                                'mobile_number',
                                                                $resident['mobile_number']
                                                                    ?? ''
                                                            )
                                                        ) ?>">

                                        </div>

                                    </div>


                                    <!-- PUROK -->
                                    <div class="col-md-6">

                                        <label
                                            for="purok_id"
                                            class="form-label">

                                            Purok
                                        </label>

                                        <div class="input-with-icon">

                                            <i class="bi bi-geo-alt"></i>

                                            <select
                                                class="form-select"
                                                id="purok_id"
                                                name="purok_id">

                                                <option value="">
                                                    Select your Purok
                                                </option>

                                                <?php
                                                $selectedPurok = (string) old(
                                                    'purok_id',
                                                    $resident['purok_id']
                                                        ?? ''
                                                );
                                                ?>

                                                <?php foreach (($puroks ?? []) as $purok): ?>

                                                    <option
                                                        value="<?= esc(
                                                                    $purok['purok_id']
                                                                ) ?>"
                                                        <?= $selectedPurok ===
                                                            (string) $purok['purok_id']
                                                            ? 'selected'
                                                            : '' ?>>

                                                        <?= esc(
                                                            $purok['purok_name']
                                                        ) ?>

                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <label
                                            for="barangay"
                                            class="form-label">

                                            Barangay
                                        </label>

                                        <div class="input-with-icon readonly-field">

                                            <i class="bi bi-building"></i>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="barangay"
                                                value="<?= esc(
                                                            $settings['barangay_name']
                                                                ?? 'Barangay Saguing'
                                                        ) ?>"
                                                readonly>

                                        </div>

                                    </div>


                                    <div class="col-12">

                                        <label
                                            for="address"
                                            class="form-label">

                                            Complete Address
                                        </label>

                                        <div class="input-with-icon">

                                            <i class="bi bi-signpost-2"></i>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="address"
                                                name="address"
                                                maxlength="255"
                                                autocomplete="off"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                placeholder="House no., street, landmark, etc."
                                                value="<?= esc(
                                                            old(
                                                                'address',
                                                                $resident['address'] ?? ''
                                                            )
                                                        ) ?>">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </section>


                        <!-- =========================================
                         SECURITY
                    ========================================== -->
                        <section class="settings-card">

                            <div class="settings-card-header">

                                <div class="settings-icon security-icon">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>

                                <div>
                                    <h3>Security & Password</h3>

                                    <p>
                                        Use a strong password to protect
                                        your resident account.
                                    </p>
                                </div>

                            </div>


                            <div class="settings-card-body">

                                <div class="security-note">

                                    <i class="bi bi-info-circle-fill"></i>

                                    <span>
                                        Leave all password fields blank if
                                        you only want to update your profile.
                                    </span>

                                </div>


                                <div class="row g-4">

                                    <div class="col-lg-4">

                                        <label
                                            for="currentPassword"
                                            class="form-label">

                                            Current Password
                                        </label>

                                        <div class="password-field">

                                            <input
                                                type="password"
                                                class="form-control"
                                                id="currentPassword"
                                                name="current_password"
                                                autocomplete="current-password"
                                                placeholder="Current password">

                                            <button
                                                type="button"
                                                class="password-toggle"
                                                data-password-target="currentPassword"
                                                aria-label="Show current password">

                                                <i class="bi bi-eye"></i>

                                            </button>

                                        </div>

                                    </div>


                                    <div class="col-lg-4">

                                        <label
                                            for="newPassword"
                                            class="form-label">

                                            New Password
                                        </label>

                                        <div class="password-field">

                                            <input
                                                type="password"
                                                class="form-control"
                                                id="newPassword"
                                                name="new_password"
                                                minlength="8"
                                                autocomplete="new-password"
                                                placeholder="Minimum 8 characters">

                                            <button
                                                type="button"
                                                class="password-toggle"
                                                data-password-target="newPassword"
                                                aria-label="Show new password">

                                                <i class="bi bi-eye"></i>

                                            </button>

                                        </div>

                                    </div>


                                    <div class="col-lg-4">

                                        <label
                                            for="confirmPassword"
                                            class="form-label">

                                            Confirm Password
                                        </label>

                                        <div class="password-field">

                                            <input
                                                type="password"
                                                class="form-control"
                                                id="confirmPassword"
                                                name="confirm_password"
                                                minlength="8"
                                                autocomplete="new-password"
                                                placeholder="Repeat new password">

                                            <button
                                                type="button"
                                                class="password-toggle"
                                                data-password-target="confirmPassword"
                                                aria-label="Show confirmed password">

                                                <i class="bi bi-eye"></i>

                                            </button>

                                        </div>

                                    </div>

                                </div>


                                <div
                                    id="passwordMatchMessage"
                                    class="password-message"
                                    aria-live="polite">
                                </div>

                            </div>

                        </section>


                        <!-- =========================================
                         FORM ACTIONS
                    ========================================== -->
                        <section class="settings-action-bar">

                            <div>
                                <strong>Save your account changes</strong>

                                <p>
                                    Review your information before saving.
                                </p>
                            </div>

                            <div class="settings-action-buttons">

                                <button
                                    type="reset"
                                    class="btn btn-outline-secondary"
                                    id="resetProfileButton">

                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                    id="saveProfileButton">

                                    <i class="bi bi-check-circle-fill"></i>
                                    Save Changes
                                </button>

                            </div>

                        </section>

                    </div>


                    <!-- =========================================
                     RIGHT SIDEBAR SETTINGS
                ========================================== -->
                    <div class="settings-side-column">


                        <!-- PROFILE PHOTO -->
                        <section class="settings-card compact-card">

                            <div class="settings-card-header">

                                <div class="settings-icon photo-icon">
                                    <i class="bi bi-camera-fill"></i>
                                </div>

                                <div>
                                    <h3>Profile Photo</h3>

                                    <p>
                                        Personalize your resident account.
                                    </p>
                                </div>

                            </div>


                            <div class="settings-card-body">

                                <div class="photo-settings">

                                    <img
                                        id="sideProfilePreview"
                                        src="<?= esc($profileImage) ?>"
                                        data-original-src="<?= esc($profileImage) ?>"
                                        alt="Profile preview"
                                        class="photo-settings-preview">

                                    <label
                                        for="profileImage"
                                        class="btn btn-outline-success upload-photo-button">

                                        <i class="bi bi-upload"></i>
                                        Choose New Photo
                                    </label>

                                    <input
                                        type="file"
                                        id="profileImage"
                                        name="profile_image"
                                        accept="image/jpeg,image/png,image/webp"
                                        hidden>

                                    <small>
                                        JPG, PNG, or WebP.
                                        Maximum size: 5 MB.
                                    </small>

                                    <div
                                        id="selectedFileName"
                                        class="selected-file-name">
                                    </div>

                                </div>

                            </div>

                        </section>


                        <!-- NOTIFICATION CENTER -->
                        <section class="settings-card compact-card">

                            <div class="settings-card-header">

                                <div class="settings-icon notification-icon">
                                    <i class="bi bi-bell-fill"></i>
                                </div>

                                <div>
                                    <h3>Notifications</h3>

                                    <p>
                                        Stay updated about your reports.
                                    </p>
                                </div>

                            </div>


                            <div class="settings-card-body">

                                <div class="quick-setting-item">

                                    <div class="quick-setting-icon">
                                        <i class="bi bi-megaphone-fill"></i>
                                    </div>

                                    <div>
                                        <strong>Notification Center</strong>

                                        <span>
                                            View report status updates and
                                            system notifications.
                                        </span>
                                    </div>

                                </div>

                                <a
                                    href="<?= base_url('resident/notifications') ?>"
                                    class="btn btn-outline-success w-100 mt-3">

                                    <i class="bi bi-bell"></i>
                                    Open Notifications
                                </a>

                            </div>

                        </section>

                        <!-- MOBILE-ONLY FORM ACTIONS -->
                        <section class="settings-action-bar mobile-profile-actions">

                            <div>
                                <strong>Save your account changes</strong>

                                <p>
                                    Review your information before saving.
                                </p>
                            </div>

                            <div class="settings-action-buttons">

                                <button
                                    type="reset"
                                    class="btn btn-outline-secondary">

                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-success">

                                    <i class="bi bi-check-circle-fill"></i>
                                    Save Changes
                                </button>

                            </div>

                        </section>


                        <!-- SESSION -->
                        <section class="settings-card compact-card">

                            <div class="settings-card-header">

                                <div class="settings-icon session-icon">
                                    <i class="bi bi-key-fill"></i>
                                </div>

                                <div>
                                    <h3>Session</h3>

                                    <p>
                                        Manage your current login session.
                                    </p>
                                </div>

                            </div>


                            <div class="settings-card-body">

                                <a
                                    href="<?= base_url('logout') ?>"
                                    class="btn btn-outline-dark w-100">

                                    <i class="bi bi-box-arrow-right"></i>
                                    Log Out of Account
                                </a>

                            </div>

                        </section>

                    </div>

                </div>

            </form>


            <!-- =========================================
             DANGER ZONE
        ========================================== -->
            <section class="danger-zone-card">

                <div class="danger-zone-content">

                    <div class="danger-zone-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <div>
                        <span class="danger-label">
                            Danger Zone
                        </span>

                        <h3>Delete Resident Account</h3>

                        <p>
                            Permanently remove your account.
                            This action cannot be undone.
                        </p>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteAccountModal">

                    <i class="bi bi-trash3-fill"></i>
                    Delete Account
                </button>

            </section>


            <!-- =========================================
             DELETE ACCOUNT MODAL
        ========================================== -->
            <div
                class="modal fade"
                id="deleteAccountModal"
                tabindex="-1"
                aria-labelledby="deleteAccountModalLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content delete-modal">

                        <div class="modal-header">

                            <div>

                                <span class="danger-label">
                                    Permanent Action
                                </span>

                                <h5
                                    class="modal-title"
                                    id="deleteAccountModalLabel">

                                    Delete Resident Account
                                </h5>

                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                            </button>

                        </div>


                        <form
                            action="<?= site_url('resident/account/delete') ?>"
                            method="POST">

                            <?= csrf_field() ?>


                            <div class="modal-body">

                                <div class="delete-warning">

                                    <i class="bi bi-exclamation-octagon-fill"></i>

                                    <div>
                                        <strong>
                                            This cannot be undone.
                                        </strong>

                                        <p>
                                            Your resident account will be
                                            permanently deleted.
                                        </p>
                                    </div>

                                </div>


                                <div class="mb-3">

                                    <label
                                        for="deleteCurrentPassword"
                                        class="form-label">

                                        Current Password
                                    </label>

                                    <input
                                        type="password"
                                        class="form-control"
                                        id="deleteCurrentPassword"
                                        name="current_password"
                                        autocomplete="current-password"
                                        required>

                                </div>


                                <div class="mb-2">

                                    <label
                                        for="deleteConfirmation"
                                        class="form-label">

                                        Type
                                        <strong>DELETE</strong>
                                        to confirm
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="deleteConfirmation"
                                        name="delete_confirmation"
                                        placeholder="DELETE"
                                        autocomplete="off"
                                        required>

                                </div>

                            </div>


                            <div class="modal-footer">

                                <button
                                    type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal">

                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    id="deleteAccountButton">

                                    <i class="bi bi-trash3-fill"></i>
                                    Permanently Delete
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <footer class="footer">

                <p>
                    &copy; 2026
                    <?= esc(
                        $settings['system_name']
                            ?? 'Community Problems Visibility System'
                    ) ?>

                    <span>•</span>

                    <?= esc(
                        $settings['barangay_name']
                            ?? 'Barangay Saguing'
                    ) ?>
                </p>

            </footer>

        </main>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script
        defer
        src="<?= base_url('assets/js/profile R.js') ?>">
    </script>

</body>

</html>