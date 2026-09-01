<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Purok Map Setup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-responsive.css') ?>">

    <style>
        body {
            background: #f5f7f6;
        }

        .setup-container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 24px;
        }

        .setup-header {
            margin-bottom: 20px;
        }

        .setup-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .purok-list {
            max-height: 620px;
            overflow-y: auto;
        }

        .purok-item {
            width: 100%;
            border: none;
            background: transparent;
            padding: 12px 14px;
            text-align: left;
            border-radius: 10px;
            transition: 0.2s ease;
            margin-bottom: 5px;
        }

        .purok-item:hover {
            background: #f1f8f3;
        }

        .purok-item.active {
            background: #e8f5ec;
            box-shadow: inset 3px 0 0 #198754;
        }

        .purok-name {
            font-weight: 600;
            color: #27352e;
        }

        .purok-status {
            font-size: 12px;
        }

        .status-mapped {
            color: #198754;
        }

        .status-unmapped {
            color: #b7791f;
        }

        #purokSetupMap {
            width: 100%;
            height: 600px;
            border-radius: 14px;
        }

        .coordinate-box {
            background: #f8faf9;
            border: 1px solid #e3e9e5;
            border-radius: 12px;
            padding: 14px;
        }

        .progress {
            height: 10px;
            border-radius: 20px;
        }

        .map-instruction {
            font-size: 14px;
            color: #68756e;
        }

        @media (max-width: 991px) {
            #purokSetupMap {
                height: 500px;
            }

            .purok-list {
                max-height: 300px;
            }
        }

        @media (max-width: 576px) {
            .setup-container {
                padding: 14px;
            }

            #purokSetupMap {
                height: 400px;
            }
        }
    </style>
</head>

<body class="<?= esc(system_theme_class()) ?>">
    <button
        type="button"
        class="admin-mobile-toggle"
        aria-label="Open admin menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="admin-sidebar-overlay"></div>

    <div class="setup-container">

        <!-- HEADER -->
        <div class="setup-header">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-geo-alt-fill text-success"></i>
                        Purok Map Setup
                    </h2>

                    <p class="text-muted mb-0">
                        Select a Purok, click its location on the map,
                        then save.
                    </p>
                </div>

                <a
                    href="<?= site_url('admin/map') ?>"
                    class="btn btn-outline-success">

                    <i class="bi bi-map"></i>
                    Back to Map

                </a>

            </div>

        </div>


        <!-- PROGRESS -->

        <?php
        $completed = isset($completed)
            ? (is_array($completed) ? (int) ($completed['completed'] ?? $completed['count'] ?? 0) : (int) $completed)
            : 0;

        $total = isset($total)
            ? (is_array($total) ? (int) ($total['total'] ?? $total['count'] ?? 0) : (int) $total)
            : 0;

        $percent = $total > 0
            ? round(($completed / $total) * 100)
            : 0;
        ?>

        <div class="setup-card p-3 mb-4">

            <div class="d-flex justify-content-between mb-2">

                <strong>
                    Mapping Progress
                </strong>

                <span>
                    <strong id="completedCount">
                        <?= esc((string)$completed) ?>
                    </strong>
                    /
                    <?= esc((string)$total) ?>
                    mapped
                </span>

            </div>

            <div class="progress">

                <div
                    id="mappingProgress"
                    class="progress-bar bg-success"
                    style="width: <?= $percent ?>%;">
                </div>

            </div>

        </div>


        <div class="row g-4">

            <!-- LEFT SIDE -->
            <div class="col-lg-4">

                <div class="setup-card p-3">

                    <div class="mb-3">

                        <label
                            for="purokSearch"
                            class="form-label fw-semibold">

                            Search Purok

                        </label>

                        <input
                            type="text"
                            id="purokSearch"
                            class="form-control"
                            placeholder="Search Purok...">

                    </div>


                    <div
                        id="purokList"
                        class="purok-list">

                        <?php foreach ($puroks ?? [] as $purok): ?>

                            <?php
                            $isMapped =
                                ($purok['latitude'] ?? null) !== null &&
                                ($purok['latitude'] ?? '') !== '' &&
                                ($purok['longitude'] ?? null) !== null &&
                                ($purok['longitude'] ?? '') !== '';
                            ?>

                            <button
                                type="button"
                                class="purok-item"
                                data-purok-id="<?= esc($purok['purok_id']) ?>"
                                data-purok-name="<?= esc($purok['purok_name']) ?>"
                                data-latitude="<?= esc($purok['latitude'] ?? '') ?>"
                                data-longitude="<?= esc($purok['longitude'] ?? '') ?>"
                                data-mapped="<?= $isMapped ? '1' : '0' ?>">

                                <div class="d-flex justify-content-between align-items-center gap-2">

                                    <span class="purok-name">
                                        <?= esc($purok['purok_name']) ?>
                                    </span>

                                    <?php if ($isMapped): ?>

                                        <span class="purok-status status-mapped">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Mapped
                                        </span>

                                    <?php else: ?>

                                        <span class="purok-status status-unmapped">
                                            <i class="bi bi-exclamation-circle"></i>
                                            Needs Location
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="col-lg-8">

                <div class="setup-card p-3">

                    <div class="mb-3">

                        <h5
                            id="selectedPurokName"
                            class="fw-bold mb-1">

                            Select a Purok

                        </h5>

                        <p class="map-instruction mb-0">
                            Choose a Purok from the list,
                            then click its location inside Barangay Saguing.
                        </p>

                    </div>


                    <div id="purokSetupMap"></div>


                    <div class="coordinate-box mt-3">

                        <input
                            type="hidden"
                            id="selectedPurokId">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Latitude
                                </label>

                                <input
                                    type="text"
                                    id="selectedLatitude"
                                    class="form-control"
                                    readonly>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Longitude
                                </label>

                                <input
                                    type="text"
                                    id="selectedLongitude"
                                    class="form-control"
                                    readonly>

                            </div>

                        </div>


                        <div class="d-flex flex-wrap gap-2 justify-content-end mt-3">

                            <button
                                type="button"
                                id="saveCoordinatesBtn"
                                class="btn btn-success"
                                disabled>

                                <i class="bi bi-check-lg"></i>
                                Save & Next

                            </button>

                        </div>

                    </div>


                    <div
                        id="saveMessage"
                        class="mt-3">
                    </div>

                </div>

            </div>

        </div>

    </div>


    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const map = L.map("purokSetupMap").setView(
                [6.9780, 125.0810],
                15
            );

            L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "&copy; OpenStreetMap contributors"
                }
            ).addTo(map);


            let selectedMarker = null;
            let selectedPurokButton = null;


            const purokItems =
                Array.from(
                    document.querySelectorAll(".purok-item")
                );

            const selectedPurokId =
                document.getElementById("selectedPurokId");

            const selectedPurokName =
                document.getElementById("selectedPurokName");

            const selectedLatitude =
                document.getElementById("selectedLatitude");

            const selectedLongitude =
                document.getElementById("selectedLongitude");

            const saveButton =
                document.getElementById("saveCoordinatesBtn");

            const saveMessage =
                document.getElementById("saveMessage");

            const searchInput =
                document.getElementById("purokSearch");


            function placeMarker(lat, lng, purokName) {

                if (selectedMarker) {
                    map.removeLayer(selectedMarker);
                }

                selectedMarker = L.marker([
                        lat,
                        lng
                    ])
                    .addTo(map)
                    .bindPopup(
                        "<strong>" +
                        purokName +
                        "</strong>"
                    )
                    .openPopup();
            }


            function selectPurok(button) {

                purokItems.forEach(function(item) {
                    item.classList.remove("active");
                });

                button.classList.add("active");

                selectedPurokButton = button;

                const id =
                    button.dataset.purokId;

                const name =
                    button.dataset.purokName;

                const latitude =
                    button.dataset.latitude;

                const longitude =
                    button.dataset.longitude;


                selectedPurokId.value = id;
                selectedPurokName.textContent = name;

                selectedLatitude.value =
                    latitude || "";

                selectedLongitude.value =
                    longitude || "";

                saveMessage.innerHTML = "";


                if (latitude && longitude) {

                    const lat =
                        parseFloat(latitude);

                    const lng =
                        parseFloat(longitude);

                    if (
                        Number.isFinite(lat) &&
                        Number.isFinite(lng)
                    ) {

                        map.flyTo(
                            [lat, lng],
                            17
                        );

                        placeMarker(
                            lat,
                            lng,
                            name
                        );

                        saveButton.disabled = false;
                    }

                } else {

                    saveButton.disabled = true;

                    if (selectedMarker) {
                        map.removeLayer(selectedMarker);
                        selectedMarker = null;
                    }

                }
            }


            map.on("click", function(event) {

                if (!selectedPurokButton) {

                    alert(
                        "Select a Purok first."
                    );

                    return;
                }

                const latitude =
                    event.latlng.lat.toFixed(8);

                const longitude =
                    event.latlng.lng.toFixed(8);


                selectedLatitude.value =
                    latitude;

                selectedLongitude.value =
                    longitude;


                placeMarker(
                    event.latlng.lat,
                    event.latlng.lng,
                    selectedPurokButton.dataset.purokName
                );


                saveButton.disabled = false;
            });


            purokItems.forEach(function(button) {

                button.addEventListener(
                    "click",
                    function() {

                        selectPurok(this);

                    }
                );

            });


            searchInput.addEventListener(
                "input",
                function() {

                    const query =
                        this.value
                        .trim()
                        .toLowerCase();

                    purokItems.forEach(
                        function(button) {

                            const name =
                                button.dataset.purokName
                                .toLowerCase();

                            button.style.display =
                                name.includes(query) ?
                                "" :
                                "none";

                        }
                    );

                }
            );


            function selectNextUnmapped() {

                const next =
                    purokItems.find(function(button) {

                        return (
                            button.dataset.mapped !== "1"
                        );

                    });


                if (next) {

                    selectPurok(next);

                    next.scrollIntoView({
                        behavior: "smooth",
                        block: "nearest"
                    });

                } else {

                    selectedPurokName.textContent =
                        "All Puroks are mapped";

                    selectedPurokId.value = "";
                    selectedLatitude.value = "";
                    selectedLongitude.value = "";

                    saveButton.disabled = true;

                }

            }


            saveButton.addEventListener(
                "click",
                async function() {

                    const purokId =
                        selectedPurokId.value;

                    const latitude =
                        selectedLatitude.value;

                    const longitude =
                        selectedLongitude.value;


                    if (
                        !purokId ||
                        !latitude ||
                        !longitude
                    ) {

                        alert(
                            "Select a Purok and click its location on the map."
                        );

                        return;
                    }


                    saveButton.disabled = true;

                    saveButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Saving...';


                    const formData =
                        new FormData();

                    formData.append(
                        "purok_id",
                        purokId
                    );

                    formData.append(
                        "latitude",
                        latitude
                    );

                    formData.append(
                        "longitude",
                        longitude
                    );

                    formData.append(
                        "<?= csrf_token() ?>",
                        "<?= csrf_hash() ?>"
                    );


                    try {

                        const response =
                            await fetch(
                                "<?= site_url('admin/purok-map-setup/save') ?>", {
                                    method: "POST",
                                    body: formData,
                                    headers: {
                                        "X-Requested-With": "XMLHttpRequest"
                                    }
                                }
                            );


                        const result =
                            await response.json();


                        if (!response.ok || !result.success) {

                            throw new Error(
                                result.message ||
                                "Unable to save coordinates."
                            );

                        }


                        selectedPurokButton.dataset.latitude =
                            latitude;

                        selectedPurokButton.dataset.longitude =
                            longitude;

                        selectedPurokButton.dataset.mapped =
                            "1";


                        const status =
                            selectedPurokButton.querySelector(
                                ".purok-status"
                            );

                        if (status) {

                            status.className =
                                "purok-status status-mapped";

                            status.innerHTML =
                                '<i class="bi bi-check-circle-fill"></i> Mapped';

                        }


                        saveMessage.innerHTML =
                            '<div class="alert alert-success py-2">' +
                            result.message +
                            "</div>";


                        updateProgress();


                        setTimeout(function() {

                            saveMessage.innerHTML = "";

                            selectNextUnmapped();

                        }, 500);


                    } catch (error) {

                        console.error(error);

                        saveMessage.innerHTML =
                            '<div class="alert alert-danger py-2">' +
                            error.message +
                            "</div>";

                    } finally {

                        saveButton.innerHTML =
                            '<i class="bi bi-check-lg"></i> Save & Next';

                        if (selectedPurokId.value) {
                            saveButton.disabled = false;
                        }

                    }

                }
            );


            function updateProgress() {

                const completed =
                    purokItems.filter(
                        function(button) {
                            return button.dataset.mapped === "1";
                        }
                    ).length;

                const total =
                    purokItems.length;

                const percentage =
                    total > 0 ?
                    Math.round(
                        (completed / total) * 100
                    ) :
                    0;


                document.getElementById(
                        "completedCount"
                    ).textContent =
                    completed;


                document.getElementById(
                        "mappingProgress"
                    ).style.width =
                    percentage + "%";

            }


            selectNextUnmapped();

        });
    </script>
    <script src="<?= base_url('assets/js/admin-responsive.js') ?>"></script>

</body>

</html>