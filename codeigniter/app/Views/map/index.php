<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Map View | CPVS</title>


    <!-- =====================================
         Bootstrap CSS
    ====================================== -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =====================================
         Bootstrap Icons
    ====================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- =====================================
         Leaflet CSS
    ====================================== -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >


    <!-- =====================================
         Custom Map CSS
    ====================================== -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/map.css') ?>"
    >

</head>

<body>

<div class="wrapper">


    <!-- =====================================
         SIDEBAR
    ====================================== -->
    <aside class="sidebar">

        <div class="logo">

            <i class="bi bi-geo-alt-fill"></i>

            <h4>CPVS</h4>

        </div>


        <ul class="menu">

            <!-- Dashboard -->
            <li>

                <a href="<?= base_url('admin/dashboard') ?>">

                    <i class="bi bi-speedometer2"></i>

                    Dashboard

                </a>

            </li>


            <!-- Reports -->
            <li>

                <a href="<?= base_url('admin/reports') ?>">

                    <i class="bi bi-file-earmark-text"></i>

                    Reports

                </a>

            </li>


            <!-- Map -->
            <li class="active">

                <a href="<?= base_url('map') ?>">

                    <i class="bi bi-map"></i>

                    Map View

                </a>

            </li>


            <!-- Residents -->
            <li>

                <a href="<?= base_url('admin/residents') ?>">

                    <i class="bi bi-people"></i>

                    Residents

                </a>

            </li>


            <!-- Categories -->
            <li>

                <a href="<?= base_url('admin/categories') ?>">

                    <i class="bi bi-tags"></i>

                    Categories

                </a>

            </li>


            <!-- Notifications -->
            <li>

                <a href="<?= base_url('admin/notifications') ?>">

                    <i class="bi bi-bell"></i>

                    Notifications

                </a>

            </li>


            <!-- Settings -->
            <li>

                <a href="<?= base_url('admin/settings') ?>">

                    <i class="bi bi-gear"></i>

                    Settings

                </a>

            </li>


            <!-- Account -->
            <li>

                <a href="<?= base_url('admin/account') ?>">

                    <i class="bi bi-person-circle"></i>

                    Account / Profile

                </a>

            </li>


            <!-- Logout -->
            <li class="logout">

                <a href="<?= base_url('logout') ?>">

                    <i class="bi bi-box-arrow-right"></i>

                    Logout

                </a>

            </li>

        </ul>

    </aside>


    <!-- =====================================
         MAIN CONTENT
    ====================================== -->
    <main class="main-content">


        <!-- =====================================
             TOP BAR
        ====================================== -->
        <header class="topbar">

            <div class="welcome">

                <h2>Map View</h2>

                <p>
                    Monitor community reports using report markers,
                    filters, and heatmap visualization.
                </p>

            </div>


            <div class="top-actions">

                <div class="profile">

                    <img
                        src="<?= base_url('assets/images/admin picture.jpg') ?>"
                        alt="Admin Profile"
                    >

                    <div>

                        <strong>
                            <?= esc(
                                session()->get('full_name')
                                ?? session()->get('fullname')
                                ?? 'Admin'
                            ) ?>
                        </strong>

                        <br>

                        <small>
                            Administrator
                        </small>

                    </div>

                </div>

            </div>

        </header>


        <!-- =====================================
             MAP FILTERS
        ====================================== -->
        <section class="card p-4 mb-4">

            <div class="row g-3 align-items-end">


                <!-- Search Location -->
                <div class="col-lg-4">

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
                    >

                </div>


                <!-- =====================================
                     CATEGORY FILTER
                ====================================== -->
                <div class="col-lg-4">

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


                <!-- =====================================
                     STATUS FILTER
                ====================================== -->
                <div class="col-lg-4">

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


        <!-- =====================================
             MAP CARD
        ====================================== -->
        <section class="card p-3">


            <!-- =====================================
                 MAP LEGEND
            ====================================== -->
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


            <!-- =====================================
                 HEATMAP INFORMATION
            ====================================== -->
            <div class="mb-3">

                <small class="text-muted">

                    <i class="bi bi-fire"></i>

                    Heatmap intensity shows areas where community
                    reports are concentrated.

                </small>

            </div>


            <!-- =====================================
                 LEAFLET MAP
            ====================================== -->
            <div id="map"></div>

        </section>

    </main>

</div>


<!-- =====================================
     LEAFLET JAVASCRIPT
====================================== -->

<!-- Leaflet must load first -->
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>


<!-- =====================================
     LEAFLET HEAT PLUGIN
====================================== -->

<!--
    Required for:
    L.heatLayer(...)
-->
<script
    src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js">
</script>


<!-- =====================================
     BOOTSTRAP JAVASCRIPT
====================================== -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- =====================================
     CUSTOM MAP JAVASCRIPT
====================================== -->

<!--
    IMPORTANT:
    map.js must load AFTER Leaflet
    and AFTER Leaflet.heat.
-->
<script
    src="<?= base_url('assets/js/map.js') ?>">
</script>


</body>

</html>