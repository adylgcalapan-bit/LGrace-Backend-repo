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


    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
                    <a href="<?= base_url('logout') ?>">
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
                        <h3 id="totalReports"><?= (int) ($totalReports ?? 0) ?></h3>
                        <span>Total Reports</span>
                    </div>

                </div>

                <div class="card dashboard-card">

                    <div class="icon orange">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div>
                        <h3 id="pendingReports"><?= (int) ($pendingReports ?? 0) ?></h3>
                        <span>Pending</span>
                    </div>

                </div>

                <div class="card dashboard-card">

                    <div class="icon blue">
                        <i class="bi bi-tools"></i>
                    </div>

                    <div>
                        <h3 id="progressReports"><?= (int) ($progressReports ?? 0) ?></h3>
                        <span>In Progress</span>
                    </div>

                </div>

                <div class="card dashboard-card">

                    <div class="icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>
                        <h3 id="resolvedReports"><?= (int) ($resolvedReports ?? 0) ?></h3>
                        <span>Resolved</span>
                    </div>

                </div>

            </section>

            <!-- REPORT TABLE -->

            <section class="table-section">

                <div class="table-header">

                    <h4>Recent Community Reports</h4>

                    <a href="<?= base_url('admin/reports') ?>"
                        class="btn btn-outline-success">
                        View All
                    </a>

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

                            <?php if (!empty($recentReports)): ?>

                                <?php foreach ($recentReports as $report): ?>

                                    <?php
                                    $status = $report['status'] ?? 'Pending';

                                    $badgeClass = match ($status) {
                                        'Pending'     => 'bg-warning text-dark',
                                        'In Progress' => 'bg-primary',
                                        'Resolved'    => 'bg-success',
                                        'Rejected'    => 'bg-danger',
                                        default       => 'bg-secondary'
                                    };
                                    ?>

                                    <tr>

                                        <td>
                                            #<?= esc($report['report_id']) ?>
                                        </td>

                                        <td>
                                            <?= esc($report['display_resident_name'] ?? 'Unknown Resident') ?>
                                        </td>

                                        <td>
                                            <?= esc($report['category_name'] ?? 'Uncategorized') ?>
                                        </td>

                                        <td>
                                            <?= esc($report['address'] ?? 'No location provided') ?>
                                        </td>

                                        <td>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= esc($status) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= !empty($report['date_reported'])
                                                ? date('F d, Y', strtotime($report['date_reported']))
                                                : 'N/A' ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No reports found.
                                    </td>
                                </tr>

                            <?php endif; ?>

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

                    <?php if (!empty($dashboardAnnouncements)): ?>

                        <?php foreach ($dashboardAnnouncements as $announcement): ?>

                            <div class="announcement-item">

                                <strong>
                                    <?= esc($announcement['title']) ?>
                                </strong>

                                <p>
                                    <?= esc($announcement['content']) ?>
                                </p>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="text-center text-muted py-3">
                            No published announcements yet.
                        </div>

                    <?php endif; ?>

                </div>



            </section>

            <!-- MAP -->

            <section class="map-card">

                <div class="section-title">
                    <h4>Community Map</h4>
                </div>

                <div id="map" style="height: 350px; width: 100%;"></div>



            </section>

            <!-- ACTIVITY -->

            <section class="activity-card">

                <div class="section-title">
                    <h4>Recent Activity</h4>
                </div>

                <ul>

                    <?php if (!empty($recentReports)): ?>

                        <?php foreach ($recentReports as $report): ?>

                            <li>
                                ✔ Report
                                <strong>#<?= esc($report['report_id']) ?></strong>
                                by
                                <strong><?= esc($report['display_resident_name'] ?? 'Unknown Resident') ?></strong>
                                is currently
                                <strong><?= esc($report['status'] ?? 'Pending') ?></strong>.
                            </li>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <li>No recent activity yet.</li>

                    <?php endif; ?>

                </ul>

            </section>

        </main>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const mapElement = document.getElementById("map");

            if (!mapElement || typeof L === "undefined") {
                return;
            }

            const reports = <?= json_encode(
                                $mapReports ?? [],
                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
                            ) ?>;

            const statusColors = {
                Pending: "#ffc107",
                "In Progress": "#0d6efd",
                Resolved: "#198754",
                Rejected: "#dc3545"
            };

            const map = L.map("map").setView([7.0083, 125.0894], 13);

            L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "&copy; OpenStreetMap contributors"
                }
            ).addTo(map);

            const markerBounds = [];

            reports.forEach(function(report) {

                const lat = parseFloat(report.latitude);
                const lng = parseFloat(report.longtitude);

                if (
                    !Number.isFinite(lat) ||
                    !Number.isFinite(lng) ||
                    (lat === 0 && lng === 0)
                ) {
                    return;
                }

                const color = statusColors[report.status] || "#6c757d";

                const marker = L.circleMarker([lat, lng], {
                    radius: 8,
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.9
                }).addTo(map);


                const popup = document.createElement("div");

                const title = document.createElement("strong");
                title.textContent =
                    "#" + report.report_id + " - " +
                    (report.title || "Untitled Report");

                const category = document.createElement("p");
                category.style.margin = "6px 0 0";
                category.textContent =
                    "Category: " + (report.category_name || "Uncategorized");

                const status = document.createElement("p");
                status.style.margin = "3px 0 0";
                status.textContent =
                    "Status: " + (report.status || "Pending");

                const address = document.createElement("p");
                address.style.margin = "3px 0 0";
                address.textContent =
                    "Location: " + (report.address || "No address available");

                popup.appendChild(title);
                popup.appendChild(category);
                popup.appendChild(status);
                popup.appendChild(address);

                marker.bindPopup(popup);

                markerBounds.push([lat, lng]);
            });

            if (markerBounds.length > 0) {
                map.fitBounds(markerBounds, {
                    padding: [30, 30],
                    maxZoom: 16
                });
            }
        });
    </script>

    <script src="<?= base_url('assets/js/dashboard-admin.js') ?>"></script>

</body>

</html>