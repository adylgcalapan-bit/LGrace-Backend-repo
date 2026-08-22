<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Map View | CPVS</title>

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

    <!-- Leaflet -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <!-- Map CSS -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/map.css') ?>"
    >
</head>

<body>

<div class="wrapper">

    <!-- ==========================================
         SIDEBAR
         ========================================== -->
    <aside
        class="sidebar"
        id="adminSidebar"
    >

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

            <li class="active">
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
                <a href="<?= base_url('login') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>

        </ul>
    </aside>


    <!-- ==========================================
         MOBILE SIDEBAR BACKDROP
         ========================================== -->
    <div
        class="sidebar-backdrop"
        id="sidebarBackdrop"
    ></div>


    <!-- ==========================================
         MAIN CONTENT
         ========================================== -->
    <main class="main-content">

        <!-- ======================================
             TOP BAR
             ====================================== -->
        <header class="topbar">

            <!-- Mobile Menu Button -->
            <button
                type="button"
                class="mobile-menu-btn"
                id="mobileMenuButton"
                aria-label="Open navigation menu"
                aria-expanded="false"
            >
                <i class="bi bi-list"></i>
            </button>


            <!-- Page Title -->
            <div class="welcome">
                <h2>Map View</h2>

                <p>
                    Community report monitoring with location
                    and heatmap visualization.
                </p>
            </div>


            <!-- Admin Profile -->
            <div class="top-actions">

                <div class="profile">

                    <img
                        src="<?= base_url('assets/images/admin picture.jpg') ?>"
                        alt="Administrator profile"
                    >

                    <div>
                        <strong>Admin</strong>
                        <br>
                        <small>Administrator</small>
                    </div>

                </div>

            </div>

        </header>


        <!-- ======================================
             FILTER CARD
             ====================================== -->
        <section class="card map-filter-card p-4 mb-4">

            <div class="row g-3 align-items-end">

                <!-- Search Location
                     Mobile: full width
                     Tablet: full width
                     Desktop: 1/3
                -->
                <div class="col-12 col-lg-4">

                    <label
                        for="searchLocation"
                        class="form-label"
                    >
                        Search Location
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="searchLocation"
                        placeholder="Search location..."
                        autocomplete="off"
                    >

                </div>


                <!-- Category
                     Mobile: full width
                     Tablet: half width
                     Desktop: 1/3
                -->
                <div class="col-12 col-md-6 col-lg-4">

                    <label
                        for="categoryFilter"
                        class="form-label"
                    >
                        Category
                    </label>

                    <select
                        class="form-select"
                        id="categoryFilter"
                    >

                        <option value="all">
                            All Categories
                        </option>

                        <option value="1">
                            Waste Management Problems
                        </option>

                        <option value="2">
                            Infrastructure and Public Works Issues
                        </option>

                        <option value="3">
                            Public Safety and Security Issues
                        </option>

                        <option value="4">
                            Environmental and Natural Issues
                        </option>

                        <option value="5">
                            Utilities and Public Services
                        </option>

                        <option value="6">
                            Health and Sanitation Issues
                        </option>

                        <option value="7">
                            Social and Community Conflicts
                        </option>

                        <option value="8">
                            Transportation and Road Safety Issues
                        </option>

                        <option value="9">
                            Public Facility Issues
                        </option>

                        <option value="10">
                            Animal Control Issues
                        </option>

                    </select>

                </div>


                <!-- Status
                     Mobile: full width
                     Tablet: half width
                     Desktop: 1/3
                -->
                <div class="col-12 col-md-6 col-lg-4">

                    <label
                        for="statusFilter"
                        class="form-label"
                    >
                        Status
                    </label>

                    <select
                        class="form-select"
                        id="statusFilter"
                    >

                        <option value="all">
                            All Status
                        </option>

                        <option value="Pending">
                            Pending
                        </option>

                        <option value="In Progress">
                            In Progress
                        </option>

                        <option value="Resolved">
                            Resolved
                        </option>

                        <option value="Rejected">
                            Rejected
                        </option>

                    </select>

                </div>

            </div>

        </section>


        <!-- ======================================
             MAP CARD
             ====================================== -->
        <section class="card map-container-card p-3">

            <!-- Map Legend -->
            <div class="map-legend mb-3">

                <span>
                    <i class="bi bi-circle-fill pending"></i>
                    Pending
                </span>

                <span>
                    <i class="bi bi-circle-fill progress"></i>
                    In Progress
                </span>

                <span>
                    <i class="bi bi-circle-fill resolved"></i>
                    Resolved
                </span>

                <span>
                    <i class="bi bi-circle-fill rejected"></i>
                    Rejected
                </span>

            </div>


            <!-- Leaflet Map -->
            <div
                id="map"
                role="region"
                aria-label="Community report location map"
            ></div>

        </section>

    </main>

</div>


<!-- ==========================================
     LEAFLET
     ========================================== -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<!-- ==========================================
     LEAFLET HEAT
     ========================================== -->
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>


<!-- ==========================================
     REPORT DATA
     ========================================== -->
<script>
window.reportData = <?= json_encode(
    $reports ?? [],
    JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
) ?>;
</script>


<!-- ==========================================
     MAP FUNCTIONALITY
     ========================================== -->
<script src="<?= base_url('assets/js/map.js') ?>"></script>


<!-- ==========================================
     RESPONSIVE SIDEBAR
     ========================================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const sidebar =
        document.getElementById("adminSidebar");

    const menuButton =
        document.getElementById("mobileMenuButton");

    const backdrop =
        document.getElementById("sidebarBackdrop");


    function openSidebar() {

        if (!sidebar) {
            return;
        }

        sidebar.classList.add("show");

        if (backdrop) {
            backdrop.classList.add("show");
        }

        if (menuButton) {
            menuButton.setAttribute(
                "aria-expanded",
                "true"
            );
        }

        document.body.style.overflow = "hidden";
    }


    function closeSidebar() {

        if (!sidebar) {
            return;
        }

        sidebar.classList.remove("show");

        if (backdrop) {
            backdrop.classList.remove("show");
        }

        if (menuButton) {
            menuButton.setAttribute(
                "aria-expanded",
                "false"
            );
        }

        document.body.style.overflow = "";
    }


    /*
     * Open / close sidebar using hamburger
     */
    if (menuButton) {

        menuButton.addEventListener(
            "click",
            function () {

                if (
                    sidebar &&
                    sidebar.classList.contains("show")
                ) {
                    closeSidebar();
                } else {
                    openSidebar();
                }

            }
        );

    }


    /*
     * Close sidebar when backdrop is clicked
     */
    if (backdrop) {

        backdrop.addEventListener(
            "click",
            closeSidebar
        );

    }


    /*
     * Close sidebar after selecting menu item
     * on tablet/mobile
     */
    if (sidebar) {

        sidebar
            .querySelectorAll("a")
            .forEach(function (link) {

                link.addEventListener(
                    "click",
                    function () {

                        if (window.innerWidth < 992) {
                            closeSidebar();
                        }

                    }
                );

            });

    }


    /*
     * Escape key closes sidebar
     */
    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Escape") {
                closeSidebar();
            }

        }
    );


    /*
     * Reset sidebar state when changing
     * from mobile/tablet back to desktop
     */
    window.addEventListener(
        "resize",
        function () {

            if (window.innerWidth >= 992) {
                closeSidebar();
            }

        }
    );

});
</script>

</body>
</html>