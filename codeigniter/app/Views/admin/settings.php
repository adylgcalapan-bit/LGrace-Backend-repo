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

    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/dashboard-admin.css') ?>">
</head>

<body>

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


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOP BAR -->
        <header class="topbar">

            <div class="welcome">
                <h2>System Settings</h2>
                <p>
                    Manage general information, notifications,
                    security, and display preferences.
                </p>
            </div>

            <div class="profile">

                <img
                    src="<?= base_url('assets/images/admin picture.jpg') ?>"
                    alt="Administrator">

                <div>
                    <strong>
                        <?= esc(session()->get('full_name') ?? 'Admin') ?>
                    </strong>
                    <br>
                    <small>Administrator</small>
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
            <section class="card p-4 mb-4">

                <div class="mb-4">

                    <h4 class="mb-1">
                        <i class="bi bi-sliders me-2"></i>
                        General Settings
                    </h4>

                    <p class="text-muted mb-0">
                        Basic information displayed by the system.
                    </p>

                </div>


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
            <section class="card p-4 mb-4">

                <div class="mb-4">

                    <h4 class="mb-1">
                        <i class="bi bi-bell me-2"></i>
                        Notification Settings
                    </h4>

                    <p class="text-muted mb-0">
                        Choose which system notifications are enabled.
                    </p>

                </div>


                <div class="form-check form-switch mb-3">

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

                    <label
                        class="form-check-label"
                        for="email_notifications">
                        Enable email notifications
                    </label>

                </div>


                <div class="form-check form-switch mb-3">

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

                    <label
                        class="form-check-label"
                        for="report_notifications">
                        Enable report notifications
                    </label>

                </div>


                <div class="form-check form-switch mb-3">

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

                    <label
                        class="form-check-label"
                        for="registration_notifications">
                        Enable resident registration notifications
                    </label>

                </div>


                <div class="form-check form-switch">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="announcement_notifications"
                        name="announcement_notifications"
                        value="1"
                        <?= !empty($settings['announcement_notifications'])
                            ? 'checked'
                            : '' ?>>

                    <label
                        class="form-check-label"
                        for="announcement_notifications">
                        Enable announcement notifications
                    </label>

                </div>

            </section>


            <!-- SECURITY -->
            <section class="card p-4 mb-4">

                <div class="mb-4">

                    <h4 class="mb-1">
                        <i class="bi bi-shield-lock me-2"></i>
                        Security Settings
                    </h4>

                    <p class="text-muted mb-0">
                        Configure basic session security preferences.
                    </p>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <label
                            for="session_timeout"
                            class="form-label">
                            Session Timeout
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

                        <small class="text-muted">
                            Allowed range: 5ΓÇô240 minutes.
                        </small>

                    </div>

                </div>

            </section>


            <!-- DISPLAY SETTINGS -->
            <section class="card p-4 mb-4">

                <div class="mb-4">

                    <h4 class="mb-1">
                        <i class="bi bi-display me-2"></i>
                        Display Settings
                    </h4>

                    <p class="text-muted mb-0">
                        Configure preferred display options.
                    </p>

                </div>


                <div class="row g-3">

                    <div class="col-md-4">

                        <label
                            for="date_format"
                            class="form-label">
                            Date Format
                        </label>

                        <?php
                        $selectedDateFormat = old(
                            'date_format',
                            $settings['date_format']
                                ?? 'MM/DD/YYYY'
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

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label
                            for="items_per_page"
                            class="form-label">
                            Items Per Page
                        </label>

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

                    </div>


                    <div class="col-md-4">

                        <label
                            for="theme_preference"
                            class="form-label">
                            Theme Preference
                        </label>

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

            </section>


            <!-- ACTION BUTTONS -->
            <div
                class="d-flex flex-wrap justify-content-end gap-2 mb-4">

                <button
                    type="reset"
                    class="btn btn-outline-secondary"
                    id="resetBtn">

                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset Changes

                </button>

                <button
                    type="submit"
                    class="btn btn-success"
                    id="saveBtn">

                    <i class="bi bi-check-lg me-1"></i>
                    Save Changes

                </button>

            </div>

        </form>

    </main>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<script src="<?= base_url('assets/js/settings.js') ?>"></script>

</body>
</html>
