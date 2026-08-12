document.addEventListener("DOMContentLoaded", function () {

    // =====================================
    // MAP INITIALIZATION
    // =====================================

    const mapElement = document.getElementById("map");

    if (!mapElement) {
        console.error("Map element not found.");
        return;
    }

    if (typeof L === "undefined") {
        console.error("Leaflet library not loaded.");
        return;
    }

    const map = L.map("map").setView(
        [7.0083, 125.0894],
        13
    );


    // =====================================
    // OPENSTREETMAP BASE LAYER
    // =====================================

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap contributors"
        }
    ).addTo(map);


    // =====================================
    // FILTER ELEMENTS
    // =====================================

    const categoryFilter =
        document.getElementById("categoryFilter");

    const statusFilter =
        document.getElementById("statusFilter");

    const searchLocation =
        document.getElementById("searchLocation");


    // =====================================
    // CATEGORY NAMES
    // =====================================

    const categoryNames = {
        "1": "Waste Management Problems",
        "2": "Infrastructure and Public Works Issues",
        "3": "Public Safety and Security Issues",
        "4": "Environmental and Natural Issues",
        "5": "Utilities and Public Services",
        "6": "Health and Sanitation Issues",
        "7": "Social and Community Conflicts",
        "8": "Transportation and Road Safety Issues",
        "9": "Public Facility Issues",
        "10": "Animal Control Issues"
    };


    // =====================================
    // STATUS COLORS
    // =====================================

    const statusColors = {
        "Pending": "#ff9800",
        "In Progress": "#2196f3",
        "Resolved": "#28a745",
        "Rejected": "#dc3545"
    };


    // =====================================
    // REPORT STORAGE
    // =====================================

    let allReports = [];


    // =====================================
    // MARKER LAYER
    // =====================================

    const markerLayer =
        L.layerGroup().addTo(map);


    // =====================================
    // HEATMAP LAYER
    // =====================================

    let heatLayer = null;

    if (typeof L.heatLayer === "function") {

        heatLayer = L.heatLayer(
            [],
            {
                radius: 35,
                blur: 25,
                maxZoom: 17,
                minOpacity: 0.35
            }
        ).addTo(map);

    } else {

        console.error(
            "Leaflet Heat plugin was not loaded."
        );
    }


    // =====================================
    // SAFE HTML FOR POPUPS
    // =====================================

    function escapeHtml(value) {

        return String(value ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }


    // =====================================
    // DISPLAY REPORTS
    // MARKERS + HEATMAP
    // =====================================

    function displayReports(reports) {

        // Remove existing markers
        markerLayer.clearLayers();

        const visibleMarkers = [];

        const heatPoints = [];


        reports.forEach(function (report) {

            const latitude =
                parseFloat(report.latitude);

            const longitude =
                parseFloat(report.longitude);


            // ---------------------------------
            // VALIDATE COORDINATES
            // ---------------------------------

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude)
            ) {

                console.warn(
                    "Skipping report with invalid coordinates:",
                    report
                );

                return;
            }


            // Prevent invalid world coordinates
            if (
                latitude < -90 ||
                latitude > 90 ||
                longitude < -180 ||
                longitude > 180
            ) {

                console.warn(
                    "Skipping report outside valid coordinate range:",
                    report
                );

                return;
            }


            const status =
                String(
                    report.status ?? ""
                ).trim();


            const categoryId =
                String(
                    report.category_id ?? ""
                );


            const categoryName =
                categoryNames[categoryId]
                || "Unknown Category";


            const markerColor =
                statusColors[status]
                || "#6c757d";


            // =====================================
            // ADD POINT TO HEATMAP
            // =====================================

            /*
             * Format:
             *
             * [latitude, longitude, intensity]
             *
             * Each report has intensity = 1.
             * If reports are close together,
             * the heat becomes stronger.
             */

            heatPoints.push([
                latitude,
                longitude,
                1
            ]);


            // =====================================
            // CREATE REPORT MARKER
            // =====================================

            const marker =
                L.circleMarker(
                    [
                        latitude,
                        longitude
                    ],
                    {
                        radius: 7,
                        color: markerColor,
                        fillColor: markerColor,
                        fillOpacity: 0.85,
                        weight: 2
                    }
                );


            // =====================================
            // MARKER POPUP
            // =====================================

            marker.bindPopup(`
                <div>

                    <strong>
                        Report #${escapeHtml(report.report_id)}
                    </strong>

                    <br>

                    <strong>Title:</strong>
                    ${escapeHtml(report.title || "No title")}

                    <br>

                    <strong>Category:</strong>
                    ${escapeHtml(categoryName)}

                    <br>

                    <strong>Status:</strong>
                    ${escapeHtml(status || "Unknown")}

                    <br>

                    <strong>Latitude:</strong>
                    ${escapeHtml(report.latitude)}

                    <br>

                    <strong>Longitude:</strong>
                    ${escapeHtml(report.longitude)}

                </div>
            `);


            marker.addTo(markerLayer);

            visibleMarkers.push(marker);
        });


        // =====================================
        // UPDATE HEATMAP
        // =====================================

        if (heatLayer) {

            heatLayer.setLatLngs(
                heatPoints
            );
        }


        console.log(
            "Heatmap points:",
            heatPoints
        );


        // =====================================
        // ADJUST MAP VIEW
        // =====================================

        if (visibleMarkers.length > 0) {

            const markerGroup =
                L.featureGroup(
                    visibleMarkers
                );


            map.fitBounds(
                markerGroup
                    .getBounds()
                    .pad(0.20),
                {
                    maxZoom: 16
                }
            );

        } else {

            // No matching reports
            map.setView(
                [7.0083, 125.0894],
                13
            );
        }
    }


    // =====================================
    // APPLY CATEGORY + STATUS FILTER
    // =====================================

    function applyFilters() {

        const selectedCategory =
            categoryFilter
                ? categoryFilter.value
                : "all";


        const selectedStatus =
            statusFilter
                ? statusFilter.value
                : "all";


        const filteredReports =
            allReports.filter(
                function (report) {

                    // -----------------------------
                    // CATEGORY FILTER
                    // -----------------------------

                    const reportCategory =
                        String(
                            report.category_id ?? ""
                        );


                    const categoryMatches =
                        selectedCategory === "all"
                        ||
                        reportCategory ===
                        String(selectedCategory);


                    // -----------------------------
                    // STATUS FILTER
                    // -----------------------------

                    const reportStatus =
                        String(
                            report.status ?? ""
                        ).trim();


                    const statusMatches =
                        selectedStatus === "all"
                        ||
                        reportStatus ===
                        selectedStatus;


                    // Report must match BOTH
                    return (
                        categoryMatches &&
                        statusMatches
                    );
                }
            );


        console.log(
            "Category:",
            selectedCategory
        );


        console.log(
            "Status:",
            selectedStatus
        );


        console.log(
            "Filtered reports:",
            filteredReports
        );


        // Update markers AND heatmap
        displayReports(
            filteredReports
        );
    }


    // =====================================
    // LOAD REPORTS FROM BACKEND API
    // =====================================

    fetch(
        "/api/locations",
        {
            method: "GET",

            headers: {
                "Accept": "application/json"
            },

            credentials: "same-origin"
        }
    )

        .then(function (response) {

            if (!response.ok) {

                throw new Error(
                    "Unable to load reports from the server."
                );
            }


            return response.json();
        })


        .then(function (result) {

            console.log(
                "Reports from backend:",
                result
            );


            if (
                !result.success ||
                !Array.isArray(result.data)
            ) {

                throw new Error(
                    "Invalid report data received from backend."
                );
            }


            // Store all reports
            allReports =
                result.data;


            console.log(
                "Total reports loaded:",
                allReports.length
            );


            // Initially show everything
            applyFilters();
        })


        .catch(function (error) {

            console.error(
                "Error loading map reports:",
                error
            );
        });


    // =====================================
    // CATEGORY FILTER EVENT
    // =====================================

    if (categoryFilter) {

        categoryFilter.addEventListener(
            "change",
            function () {

                applyFilters();

            }
        );
    }


    // =====================================
    // STATUS FILTER EVENT
    // =====================================

    if (statusFilter) {

        statusFilter.addEventListener(
            "change",
            function () {

                applyFilters();

            }
        );
    }


    // =====================================
    // SEARCH LOCATION
    // =====================================

    /*
     * Search is currently separate.
     * Sprint 5 requirement focuses on
     * heatmap implementation.
     */

    if (searchLocation) {

        searchLocation.addEventListener(
            "input",
            function (event) {

                console.log(
                    "Search location:",
                    event.target.value
                );

            }
        );
    }

});