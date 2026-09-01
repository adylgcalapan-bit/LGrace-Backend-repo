<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map View | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/map.css') ?>">
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
                <li class="active"><a href="<?= base_url('admin/map') ?>"><i class="bi bi-map"></i>Map View</a></li>
                <li><a href="<?= base_url('admin/residents') ?>"><i class="bi bi-people"></i>Residents</a></li>
                <li><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
                <?= view('admin/notification_menu') ?>
                <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
                <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account</a></li>
                <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="welcome">
                    <h2>Map View</h2>
                    <p>Frontend map view for community report monitoring.</p>
                </div>
                <div class="top-actions">
                    <div class="profile">
                        <img src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                        <div><strong>Admin</strong><br><small>Administrator</small></div>
                    </div>
                </div>
            </header>
            <!-- Purok Location Management -->
            <div class="d-flex justify-content-end mb-3">

                <a
                    href="<?= site_url('admin/purok-map-setup') ?>"
                    class="btn btn-success">

                    <i class="bi bi-geo-alt-fill me-1"></i>
                    Manage Purok Locations

                </a>

            </div>

            <section class="card p-4 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Search Location</label>

                        <div class="position-relative">
                            <input
                                type="text"
                                class="form-control"
                                id="searchLocation"
                                placeholder="Search location in Saguing..."
                                autocomplete="off">

                            <div
                                id="locationSuggestions"
                                class="location-suggestions d-none">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Category</label>

                        <input
                            type="hidden"
                            id="categoryFilter"
                            value="all">

                        <div class="dropdown report-filter-dropdown">

                            <button
                                class="btn report-filter-dropdown-btn dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <span id="mapCategoryLabel">
                                    All Categories
                                </span>

                            </button>

                            <ul class="dropdown-menu report-filter-menu">

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-category-option"
                                        data-value="all">
                                        All Categories
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="1">
                                        Waste Management Problems
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="2">
                                        Infrastructure and Public Works Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="3">
                                        Public Safety and Security Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="4">
                                        Environmental and Natural Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="5">
                                        Utilities and Public Services
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="6">
                                        Health and Sanitation Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="7">
                                        Social and Community Conflicts
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="8">
                                        Transportation and Road Safety Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="9">
                                        Public Facility Issues
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="dropdown-item map-category-option" data-value="10">
                                        Animal Control Issues
                                    </button>
                                </li>

                            </ul>

                        </div>
                    </div>
                    <div class="col-lg-4"><label class="form-label">Status</label>

                        <input
                            type="hidden"
                            id="statusFilter"
                            value="all">

                        <div class="dropdown report-filter-dropdown">

                            <button
                                class="btn report-filter-dropdown-btn dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <span id="mapStatusLabel">
                                    All Status
                                </span>

                            </button>

                            <ul class="dropdown-menu report-filter-menu">

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-status-option"
                                        data-value="all">
                                        All Status
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-status-option"
                                        data-value="Pending">
                                        Pending
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-status-option"
                                        data-value="In Progress">
                                        In Progress
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-status-option"
                                        data-value="Resolved">
                                        Resolved
                                    </button>
                                </li>

                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item map-status-option"
                                        data-value="Rejected">
                                        Rejected
                                    </button>
                                </li>

                            </ul>

                        </div>
                    </div>
            </section>

            <section class="card p-3">
                <div class="map-legend mb-3">
                    <span><i class="bi bi-circle-fill pending"></i> Pending</span>
                    <span><i class="bi bi-circle-fill progress"></i> In Progress</span>
                    <span><i class="bi bi-circle-fill resolved"></i> Resolved</span>
                    <span><i class="bi bi-circle-fill rejected"></i> Rejected</span>
                </div>
                <div id="map"></div>
            </section>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    <script>
        window.reportData = <?= json_encode(
                                $reports ?? [],
                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
                            ) ?>;
    </script>
    <script>
        window.saguingPuroks = <?= json_encode($puroks ?? []) ?>;

        window.saguingLocations = <?= json_encode($saguingLocations ?? []) ?>;
    </script>

    <script src="<?= base_url('assets/js/map.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin-responsive.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>