document.addEventListener("DOMContentLoaded", function () {
  // =====================================
  // MAP INITIALIZATION
  // =====================================

  const mapElement = document.getElementById("map");

  if (!mapElement || typeof L === "undefined") {
    console.error("Map or Leaflet library not found.");
    return;
  }

  const map = L.map("map").setView([7.0083, 125.0894], 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);


  // =====================================
  // CATEGORY NAMES
  // Actual category_id from database
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
    "10": "Animal Control Issues",
  };


  // =====================================
  // STATUS COLORS
  // =====================================

  const statusColors = {
    Pending: "#ff9800",
    "In Progress": "#2196f3",
    Resolved: "#28a745",
    Rejected: "#dc3545",
  };


  // =====================================
  // DATA STORAGE
  // =====================================

  let allReports = [];


  // =====================================
  // MAP LAYERS
  // =====================================

  const markerLayer = L.layerGroup().addTo(map);

  let heatLayer = null;

  if (typeof L.heatLayer === "function") {
    heatLayer = L.heatLayer([], {
      radius: 35,
      blur: 25,
      maxZoom: 17,
      minOpacity: 0.35,
    }).addTo(map);
  } else {
    console.error("Leaflet Heat plugin was not loaded.");
  }


  // =====================================
  // UI ELEMENTS
  // =====================================

  const searchLocation =
    document.getElementById("searchLocation");

  const categoryFilter =
    document.getElementById("categoryFilter");

  const statusFilter =
    document.getElementById("statusFilter");


  // =====================================
  // SAFE POPUP TEXT
  // =====================================

  function escapeHtml(value) {
    const div = document.createElement("div");

    div.textContent = value ?? "";

    return div.innerHTML;
  }


  // =====================================
  // DISPLAY MARKERS + HEATMAP
  // =====================================

  function displayReports(reports) {
    markerLayer.clearLayers();

    const visibleMarkers = [];
    const heatPoints = [];


    reports.forEach(function (report) {
      const latitude =
        parseFloat(report.latitude);

      const longitude =
        parseFloat(report.longitude);


      if (
        !Number.isFinite(latitude) ||
        !Number.isFinite(longitude)
      ) {
        console.warn(
          "Skipping report with invalid coordinates:",
          report,
        );

        return;
      }


      const status =
        String(report.status ?? "").trim();

      const categoryId =
        String(report.category_id ?? "");

      const categoryName =
        categoryNames[categoryId] ||
        "Unknown Category";

      const markerColor =
        statusColors[status] ||
        "#6c757d";


      // =====================================
      // HEATMAP POINT
      // =====================================

      heatPoints.push([
        latitude,
        longitude,
        1,
      ]);


      // =====================================
      // REPORT MARKER
      // =====================================

      const marker = L.circleMarker(
        [latitude, longitude],
        {
          radius: 8,
          color: markerColor,
          fillColor: markerColor,
          fillOpacity: 0.9,
          weight: 2,
        },
      );


      marker.bindPopup(`
        <div>
          <strong>
            ${escapeHtml(report.title || "No title")}
          </strong>

          <br>

          <strong>Category:</strong>
          ${escapeHtml(categoryName)}

          <br>

          <strong>Status:</strong>
          ${escapeHtml(status || "Unknown")}

          <br>

          <strong>Report ID:</strong>
          ${escapeHtml(report.report_id)}
        </div>
      `);


      marker.addTo(markerLayer);

      visibleMarkers.push(marker);
    });


    // =====================================
    // UPDATE HEATMAP
    // =====================================

    if (heatLayer) {
      heatLayer.setLatLngs(heatPoints);
    }


    console.log("Visible reports:", reports);
    console.log("Heatmap points:", heatPoints);


    // =====================================
    // AUTOMATIC MAP ZOOM
    // =====================================

    if (visibleMarkers.length > 0) {
      const markerGroup =
        L.featureGroup(visibleMarkers);

      map.fitBounds(
        markerGroup.getBounds().pad(0.2),
        {
          maxZoom: 16,
        },
      );
    } else {
      map.setView(
        [7.0083, 125.0894],
        13,
      );
    }
  }


  // =====================================
  // CATEGORY + STATUS FILTER
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
      allReports.filter(function (report) {
        const reportCategory =
          String(report.category_id ?? "");

        const reportStatus =
          String(report.status ?? "").trim();


        const categoryMatches =
          selectedCategory === "all" ||
          reportCategory === selectedCategory;


        const statusMatches =
          selectedStatus === "all" ||
          reportStatus === selectedStatus;


        return (
          categoryMatches &&
          statusMatches
        );
      });


    console.log(
      "Selected category:",
      selectedCategory,
    );

    console.log(
      "Selected status:",
      selectedStatus,
    );

    console.log(
      "Filtered reports:",
      filteredReports,
    );


    displayReports(filteredReports);
  }


  // =====================================
  // LOAD REAL REPORTS FROM API
  // =====================================

  fetch("/api/locations", {
    method: "GET",

    headers: {
      Accept: "application/json",
    },
  })

    .then(function (response) {
      if (!response.ok) {
        throw new Error(
          "Unable to load reports from API.",
        );
      }

      return response.json();
    })

    .then(function (result) {
      console.log(
        "Reports received from API:",
        result,
      );


      if (
        !result.success ||
        !Array.isArray(result.data)
      ) {
        throw new Error(
          "Invalid report data received.",
        );
      }


      allReports = result.data;


      // Show all reports initially
      applyFilters();
    })

    .catch(function (error) {
      console.error(
        "Map report loading error:",
        error,
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
      },
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
      },
    );
  }


  // =====================================
  // SEARCH LOCATION
  // =====================================

  if (searchLocation) {
    searchLocation.addEventListener(
      "input",
      function (event) {
        console.log(
          "Search location:",
          event.target.value,
        );
      },
    );
  }
});