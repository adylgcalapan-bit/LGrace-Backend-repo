<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-admin.css') ?>">

<body>
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
                <li class="active"><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
                <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
                <li class="logout"><a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="welcome">
                    <h2>Settings</h2>
                    <p>Manage the system behavior and notifications.</p>
                </div>
                <div class="top-actions">
                    <button class="btn btn-outline-secondary" id="resetBtn">Reset Changes</button>
                    <button class="btn btn-success" id="saveBtn">Save Changes</button>
                    <div class="profile">
                        <img src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                        <div><strong>Admin</strong><br><small>Administrator</small></div>
                    </div>
                </div>
            </header>

            <section class="card p-4 mb-4">
                <h4 class="mb-4">General Settings</h4>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">System Name</label><input type="text" class="form-control" value="CPVS"></div>
                    <div class="col-md-6"><label class="form-label">Barangay Name</label><input type="text" class="form-control" value="Barangay Saguing"></div>
                    <div class="col-md-6"><label class="form-label">Contact Email</label><input type="email" class="form-control" value="admin@cpvs.com"></div>
                    <div class="col-md-6"><label class="form-label">Contact Number</label><input type="text" class="form-control" value="09123456789"></div>
                    <div class="col-12"><label class="form-label">System Description</label><textarea class="form-control" rows="3">Community Problems Visibility System for barangay reporting and monitoring.</textarea></div>
                </div>
            </section>

            <section class="card p-4 mb-4">
                <h4 class="mb-4">Notification Settings</h4>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Enable email notifications</label></div>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Enable report notifications</label></div>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Enable resident registration notifications</label></div>
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Enable announcement notifications</label></div>
            </section>

            <section class="card p-4 mb-4 security-settings-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="mb-2"><i class="bi bi-shield-lock me-2"></i>Security Settings</h4>
                        <p class="text-muted mb-0">Control account protection and login safeguards for the system.</p>
                    </div>
                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Protected</span>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="security-option">
                            <label class="form-label">Session Timeout</label>
                            <input type="text" class="form-control" value="30 minutes">
                            <small class="text-muted">Auto-logout after inactivity.</small>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="security-option">
                            <label class="form-label">Two-Factor Authentication</label>
                            <select class="form-select">
                                <option>Enabled</option>
                                <option>Disabled</option>
                            </select>
                            <small class="text-muted">Add extra protection to admin logins.</small>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card p-4">
                <h4 class="mb-4">Display Settings</h4>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Date Format</label><select class="form-select">
                            <option>MM/DD/YYYY</option>
                            <option>DD/MM/YYYY</option>
                        </select></div>
                    <div class="col-md-4"><label class="form-label">Items Per Page</label><input type="number" class="form-control" value="10"></div>
                    <div class="col-md-4"><label class="form-label">Theme Preference</label><select class="form-select">
                            <option>Light</option>
                            <option>Dark</option>
                        </select></div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/settings.js') ?>"></script>
</body>

</html>