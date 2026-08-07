<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Community Problems Visibility System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
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

            <li class="active">
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
                <a href="href="<?= base_url('admin/map') ?>>
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

    <!-- ================= MAIN ================= -->

    <main class="main-content">

        <!-- HEADER -->

        <header class="topbar">

            <div class="welcome">
                <h2>Admin Dashboard</h2>
                <p>Welcome back, Administrator</p>
            </div>

            <div class="top-actions">

                <a id="newAnnouncementBtn" class="btn btn-success" href="<?= base_url('admin/announcements') ?>?open=add">
                    <i class="bi bi-plus-circle"></i>
                    New Announcement
                </a>

                <div class="profile">

                    <img src="<?= base_url('assets/images/admin picture.jpg') ?>">

                    <div>
                        <strong>Admin</strong>
                    </div>

                </div>

            </div>

        </header>

        <!-- DASHBOARD CARDS -->

        <section class="cards">

            <div class="card dashboard-card">

                <div class="icon green">
                    <i class="bi bi-file-earmark-text"></i>
                </div>

                <div>
                    <h3 id="totalReports">245</h3>
                    <span>Total Reports</span>
                </div>

            </div>

            <div class="card dashboard-card">

                <div class="icon orange">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <div>
                    <h3 id="pendingReports">52</h3>
                    <span>Pending</span>
                </div>

            </div>

            <div class="card dashboard-card">

                <div class="icon blue">
                    <i class="bi bi-tools"></i>
                </div>

                <div>
                    <h3 id="progressReports">76</h3>
                    <span>In Progress</span>
                </div>

            </div>

            <div class="card dashboard-card">

                <div class="icon success">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <h3 id="resolvedReports">117</h3>
                    <span>Resolved</span>
                </div>

            </div>

        </section>

        <!-- REPORT TABLE -->

        <section class="table-section">

            <div class="table-header">

                <h4>Recent Community Reports</h4>

                <button class="btn btn-outline-success">
                    View All
                </button>

            </div>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                    <tr>

                        <th>ID</th>
                        <th>Resident</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date</th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <td>#1001</td>
                        <td>Juan Dela Cruz</td>
                        <td>Road Damage</td>
                        <td>Purok 1</td>

                        <td>
                            <span class="badge bg-warning">
                                Pending
                            </span>
                        </td>

                        <td>July 26, 2026</td>

                    </tr>

                    <tr>

                        <td>#1002</td>
                        <td>Maria Santos</td>
                        <td>Garbage</td>

                        <td>Purok 2</td>

                        <td>
                            <span class="badge bg-primary">
                                In Progress
                            </span>
                        </td>

                        <td>July 26, 2026</td>

                    </tr>

                    <tr>

                        <td>#1003</td>
                        <td>Pedro Ramos</td>
                        <td>Flood</td>

                        <td>Purok 5</td>

                        <td>
                            <span class="badge bg-success">
                                Resolved
                            </span>
                        </td>

                        <td>July 25, 2026</td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </section>

        <section class="map-card announcement-dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <h4>Announcements</h4>
                <a href="<?= base_url('admin/announcements') ?>" class="btn btn-sm btn-outline-success">Manage</a>
            </div>
            <div class="announcement-list">
                <div class="announcement-item">
                    <strong>Barangay Cleanup Drive</strong>
                    <p>Community cleanup scheduled this weekend.</p>
                </div>
                <div class="announcement-item">
                    <strong>Utility Maintenance Notice</strong>
                    <p>Water service interruption for Purok 4.</p>
                </div>
            </div>
        </section>

        <!-- MAP -->

        <section class="map-card">

            <div class="section-title">
                <h4>Community Map</h4>
            </div>

            <div id="map">

                <div class="map-placeholder">

                    <i class="bi bi-geo-alt-fill"></i>

                    <h5>Leaflet Map</h5>

                    <p>
                        Community reports with location will appear here.
                    </p>

                </div>

            </div>

        </section>

        <!-- ACTIVITY -->

        <section class="activity-card">

            <div class="section-title">
                <h4>Recent Activity</h4>
            </div>

            <ul>

                <li>✔ Road damage report was submitted.</li>

                <li>✔ Flood report updated to In Progress.</li>

                <li>✔ Garbage complaint has been resolved.</li>

                <li>✔ New resident account registered.</li>

            </ul>

        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= base_url('assets/js/dashboard-admin.js') ?>"></script>

</body>
</html>
