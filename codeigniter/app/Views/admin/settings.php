<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings | CPVS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-admin.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/settings.css') ?>">
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

        <!-- SIDEBAR -->
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

                <li class="active">
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


        <!-- MAIN CONTENT -->
        <main class="main-content">

            <?php
            $settingsAdminName = trim(
                (string) ($admin['full_name'] ?? 'System Admin')
            );

            $settingsAdminPhoto = !empty($admin['profile_image'])
                ? base_url(ltrim($admin['profile_image'], '/\\'))
                : base_url('assets/images/admin picture.jpg');
            ?>

            <!-- TOP BAR -->
            <header class="topbar settings-topbar">

                <div class="settings-page-heading">

                    <div class="settings-eyebrow">
                        <i class="bi bi-gear-fill"></i>
                        Administration
                    </div>

                    <h2>System Settings</h2>

                    <p>
                        Configure system information, notifications,
                        security, and display preferences.
                    </p>

                </div>

                <div class="settings-admin-profile">

                    <div class="settings-admin-avatar">
                        <img
                            src="<?= esc((string) $settingsAdminPhoto) ?>"
                            alt="<?= esc((string) $settingsAdminName) ?>">
                    </div>

                    <div class="settings-admin-info">

                        <strong>
                            <?= esc($settingsAdminName) ?>
                        </strong>

                        <span>
                            <i class="bi bi-shield-check"></i>
                            Administrator
                        </span>

                    </div>

                </div>

            </header>


            <!-- SUCCESS MESSAGE -->
            <?php if (session()->getFlashdata('success')): ?>

                <div class="alert alert-success alert-dismissible fade show">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <?= esc(session()->getFlashdata('success')) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>


            <!-- ERROR MESSAGE -->
            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger alert-dismissible fade show">

                    <i class="bi bi-exclamation-circle-fill me-2"></i>

                    <?= esc(session()->getFlashdata('error')) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>


            <form
                id="settingsForm"
                method="post"
                action="<?= site_url('admin/settings/save') ?>">

                <?= csrf_field() ?>

                <!-- GENERAL SETTINGS -->
                <section class="card settings-card mb-4">

                    <div class="settings-card-header">

                        <div class="settings-section-icon">
                            <i class="bi bi-sliders"></i>
                        </div>

                        <div class="settings-section-title">
                            <h4>General Settings</h4>
                            <p>
                                Manage the basic information and identity
                                displayed throughout the system.
                            </p>
                        </div>

                    </div>

                    <div class="settings-card-body">


                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="system_name"
                                    class="form-label">
                                    System Name
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="system_name"
                                    name="system_name"
                                    maxlength="100"
                                    required
                                    value="<?= esc(old(
                                                'system_name',
                                                $settings['system_name'] ?? 'CPVS'
                                            )) ?>">

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="barangay_name"
                                    class="form-label">
                                    Barangay Name
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="barangay_name"
                                    name="barangay_name"
                                    maxlength="150"
                                    required
                                    value="<?= esc(old(
                                                'barangay_name',
                                                $settings['barangay_name']
                                                    ?? 'Barangay Saguing'
                                            )) ?>">

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="contact_email"
                                    class="form-label">
                                    Contact Email
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="contact_email"
                                    name="contact_email"
                                    maxlength="150"
                                    value="<?= esc(old(
                                                'contact_email',
                                                $settings['contact_email'] ?? ''
                                            )) ?>">

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="contact_number"
                                    class="form-label">
                                    Contact Number
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="contact_number"
                                    name="contact_number"
                                    maxlength="30"
                                    value="<?= esc(old(
                                                'contact_number',
                                                $settings['contact_number'] ?? ''
                                            )) ?>">

                            </div>


                            <div class="col-12">

                                <label
                                    for="system_description"
                                    class="form-label">
                                    System Description
                                </label>

                                <textarea
                                    class="form-control"
                                    id="system_description"
                                    name="system_description"
                                    rows="3"><?= esc(old(
                                                    'system_description',
                                                    $settings['system_description'] ?? ''
                                                )) ?></textarea>

                            </div>

                        </div>

                </section>


                <!-- NOTIFICATION SETTINGS -->
                <section class="card settings-card mb-4">

                    <div class="settings-card-header">

                        <div class="settings-section-icon">
                            <i class="bi bi-bell"></i>
                        </div>

                        <div class="settings-section-title">
                            <h4>Notification Settings</h4>
                            <p>
                                Control which system activities generate
                                administrator notifications.
                            </p>
                        </div>

                    </div>

                    <div class="settings-card-body">

                        <div class="notification-settings-grid">

                            <!-- EMAIL -->
                            <div class="notification-option">

                                <div class="notification-option-info">

                                    <div class="notification-option-icon">
                                        <i class="bi bi-envelope"></i>
                                    </div>

                                    <div>
                                        <h5>Email Notifications</h5>
                                        <p>
                                            Allow the system to send notification
                                            updates through email.
                                        </p>
                                    </div>

                                </div>

                                <div class="form-check form-switch notification-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="email_notifications"
                                        name="email_notifications"
                                        value="1"
                                        <?= !empty($settings['email_notifications'])
                                            ? 'checked'
                                            : '' ?>>

                                </div>

                            </div>


                            <!-- REPORT -->
                            <div class="notification-option">

                                <div class="notification-option-info">

                                    <div class="notification-option-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>

                                    <div>
                                        <h5>Report Notifications</h5>
                                        <p>
                                            Receive alerts when new community
                                            reports are submitted.
                                        </p>
                                    </div>

                                </div>

                                <div class="form-check form-switch notification-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="report_notifications"
                                        name="report_notifications"
                                        value="1"
                                        <?= !empty($settings['report_notifications'])
                                            ? 'checked'
                                            : '' ?>>

                                </div>

                            </div>


                            <!-- REGISTRATION -->
                            <div class="notification-option">

                                <div class="notification-option-info">

                                    <div class="notification-option-icon">
                                        <i class="bi bi-person-plus"></i>
                                    </div>

                                    <div>
                                        <h5>Resident Registration</h5>
                                        <p>
                                            Receive alerts related to new
                                            resident registrations.
                                        </p>
                                    </div>

                                </div>

                                <div class="form-check form-switch notification-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="registration_notifications"
                                        name="registration_notifications"
                                        value="1"
                                        <?= !empty($settings['registration_notifications'])
                                            ? 'checked'
                                            : '' ?>>

                                </div>

                            </div>




                        </div>

                    </div>

                </section>


                <!-- SECURITY SETTINGS -->
                <section class="card settings-card mb-4">

                    <div class="settings-card-header">

                        <div class="settings-section-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>

                        <div class="settings-section-title">
                            <h4>Security Settings</h4>
                            <p>
                                Configure administrator session security
                                and automatic logout preferences.
                            </p>
                        </div>

                    </div>

                    <div class="settings-card-body">

                        <div class="security-option professional-security-option">

                            <div class="security-option-content">

                                <div class="security-option-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>

                                <div>
                                    <h5>Session Timeout</h5>
                                    <p>
                                        Automatically end an inactive administrator
                                        session after the selected period.
                                    </p>
                                </div>

                            </div>

                            <div class="security-timeout-control">

                                <label
                                    for="session_timeout"
                                    class="form-label">
                                    Timeout Duration
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="session_timeout"
                                        name="session_timeout"
                                        min="5"
                                        max="240"
                                        required
                                        value="<?= esc(old(
                                                    'session_timeout',
                                                    $settings['session_timeout'] ?? 30
                                                )) ?>">

                                    <span class="input-group-text">
                                        minutes
                                    </span>

                                </div>

                                <small>
                                    Allowed range: 5 to 240 minutes.
                                </small>

                            </div>

                        </div>

                    </div>

                </section>


                <!-- DISPLAY SETTINGS -->
                <section class="card settings-card mb-4">

                    <div class="settings-card-header">

                        <div class="settings-section-icon">
                            <i class="bi bi-display"></i>
                        </div>

                        <div class="settings-section-title">
                            <h4>Display Settings</h4>
                            <p>
                                Configure how information is presented
                                throughout the administrator interface.
                            </p>
                        </div>

                    </div>

                    <div class="settings-card-body">

                        <div class="display-settings-grid">

                            <!-- DATE FORMAT -->
                            <div class="display-option">

                                <div class="display-option-heading">

                                    <div class="display-option-icon">
                                        <i class="bi bi-calendar3"></i>
                                    </div>

                                    <div>
                                        <h5>Date Format</h5>
                                        <p>
                                            Choose the preferred date format
                                            used across the system.
                                        </p>
                                    </div>

                                </div>

                                <?php
                                $selectedDateFormat = old(
                                    'date_format',
                                    $settings['date_format'] ?? 'MM/DD/YYYY'
                                );
                                ?>

                                <select
                                    class="form-select"
                                    id="date_format"
                                    name="date_format"
                                    required>

                                    <option
                                        value="MM/DD/YYYY"
                                        <?= $selectedDateFormat === 'MM/DD/YYYY'
                                            ? 'selected'
                                            : '' ?>>
                                        MM/DD/YYYY
                                    </option>

                                    <option
                                        value="DD/MM/YYYY"
                                        <?= $selectedDateFormat === 'DD/MM/YYYY'
                                            ? 'selected'
                                            : '' ?>>
                                        DD/MM/YYYY
                                    </option>
                                    <option
                                        value="YYYY/MM/DD"
                                        <?= $selectedDateFormat === 'YYYY/MM/DD'
                                            ? 'selected'
                                            : '' ?>>
                                        YYYY/MM/DD
                                    </option>
                                </select>

                            </div>


                            <!-- ITEMS PER PAGE -->
                            <div class="display-option">

                                <div class="display-option-heading">

                                    <div class="display-option-icon">
                                        <i class="bi bi-list-ul"></i>
                                    </div>

                                    <div>
                                        <h5>Items Per Page</h5>
                                        <p>
                                            Set how many records are displayed
                                            on each page.
                                        </p>
                                    </div>

                                </div>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="items_per_page"
                                    name="items_per_page"
                                    min="5"
                                    max="100"
                                    required
                                    value="<?= esc(old(
                                                'items_per_page',
                                                $settings['items_per_page'] ?? 10
                                            )) ?>">
                                <small class="text-muted">
                                    Allowed range: 5 to 100 items per page.
                                </small>

                            </div>


                            <!-- THEME -->
                            <div class="display-option">

                                <div class="display-option-heading">

                                    <div class="display-option-icon">
                                        <i class="bi bi-circle-half"></i>
                                    </div>

                                    <div>
                                        <h5>Theme Preference</h5>
                                        <p>
                                            Select the preferred appearance
                                            for the administrator interface.
                                        </p>
                                    </div>

                                </div>

                                <?php
                                $selectedTheme = old(
                                    'theme_preference',
                                    $settings['theme_preference'] ?? 'Light'
                                );
                                ?>

                                <select
                                    class="form-select"
                                    id="theme_preference"
                                    name="theme_preference"
                                    required>

                                    <option
                                        value="Light"
                                        <?= $selectedTheme === 'Light'
                                            ? 'selected'
                                            : '' ?>>
                                        Light
                                    </option>

                                    <option
                                        value="Dark"
                                        <?= $selectedTheme === 'Dark'
                                            ? 'selected'
                                            : '' ?>>
                                        Dark
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </section>


                <!-- SETTINGS ACTIONS -->
                <div class="settings-actions">

                    <div class="settings-actions-info">

                        <div class="settings-actions-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>

                        <div>
                            <strong>Review your changes</strong>
                            <span>
                                Changes will take effect after you save the settings.
                            </span>
                        </div>

                    </div>

                    <div class="settings-actions-buttons">

                        <button
                            type="reset"
                            class="btn settings-reset-btn"
                            id="resetBtn">

                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset Changes

                        </button>

                        <button
                            type="submit"
                            class="btn settings-save-btn"
                            id="saveBtn">

                            <i class="bi bi-check-lg"></i>
                            Save Changes

                        </button>

                    </div>

                </div>

            </form>

        </main>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script src="<?= base_url('assets/js/settings.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin-responsive.js') ?>"></script>

</body>

</html>