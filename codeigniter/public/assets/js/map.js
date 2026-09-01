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
    1: "Waste Management Problems",
    2: "Infrastructure and Public Works Issues",
    3: "Public Safety and Security Issues",
    4: "Environmental and Natural Issues",
    5: "Utilities and Public Services",
    6: "Health and Sanitation Issues",
    7: "Social and Community Conflicts",
    8: "Transportation and Road Safety Issues",
    9: "Public Facility Issues",
    10: "Animal Control Issues",
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
      radius: 45,
      blur: 30,
      maxZoom: 17,
      minOpacity: 0.45,
      gradient: {
        0.2: "#b7e4c7",
        0.5: "#52b788",
        0.8: "#2d6a4f",
        1.0: "#1b4332",
      },
    }).addTo(map);
  }

  // =====================================
  // UI ELEMENTS
  // =====================================

  const searchLocation = document.getElementById("searchLocation");

  const categoryFilter = document.getElementById("categoryFilter");

  const statusFilter = document.getElementById("statusFilter");

  // =====================================
  // CUSTOM CATEGORY DROPDOWN
  // =====================================

  document.querySelectorAll(".map-category-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!categoryFilter) {
        return;
      }

      categoryFilter.value = this.dataset.value || "all";

      const label = document.getElementById("mapCategoryLabel");

      if (label) {
        label.textContent = this.textContent.trim();
      }

      applyFilters();
    });
  });

  // =====================================
  // CUSTOM STATUS DROPDOWN
  // =====================================

  document.querySelectorAll(".map-status-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!statusFilter) {
        return;
      }

      statusFilter.value = this.dataset.value || "all";

      const label = document.getElementById("mapStatusLabel");

      if (label) {
        label.textContent = this.textContent.trim();
      }

      applyFilters();
    });
  });

  // =====================================
  // SAFE POPUP TEXT
  // =====================================

  function escapeHtml(value) {
    const div = document.createElement("div");

    div.textContent = value ?? "";

    return div.innerHTML;
  }

  // =====================================
  //  DISPLAY REPORT MARKERS
  // =====================================

  function displayReports(reports) {
    markerLayer.clearLayers();

    const visibleMarkers = [];
    const heatPoints = [];

    reports.forEach(function (report) {
      const latitude = parseFloat(report.latitude);

      const longitude = parseFloat(report.longitude);

      if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
        console.warn("Skipping report with invalid coordinates:", report);

        return;
      }

      const status = String(report.status ?? "").trim();

      if (status !== "Resolved" && status !== "Rejected") {
        heatPoints.push([latitude, longitude, 1]);
      }

      const categoryId = String(report.category_id ?? "");

      const categoryName = categoryNames[categoryId] || "Unknown Category";

      const markerColor = statusColors[status] || "#6c757d";

      // =====================================
      // REPORT MARKER
      // =====================================

      const marker = L.circleMarker([latitude, longitude], {
        radius: 8,
        color: markerColor,
        fillColor: markerColor,
        fillOpacity: 0.9,
        weight: 2,
      });

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

    if (heatLayer) {
      heatLayer.setLatLngs(heatPoints);
    }

    console.log("Visible reports:", reports);

    // =====================================
    // AUTOMATIC MAP ZOOM
    // =====================================

    if (visibleMarkers.length > 0) {
      const markerGroup = L.featureGroup(visibleMarkers);

      map.fitBounds(markerGroup.getBounds().pad(0.2), {
        maxZoom: 16,
      });
    } else {
      map.setView([7.0083, 125.0894], 13);
    }
  }

  // =====================================
  // CATEGORY + STATUS FILTER
  // =====================================

  function applyFilters() {
    const selectedCategory = categoryFilter ? categoryFilter.value : "all";

    const selectedStatus = statusFilter ? statusFilter.value : "all";

    const filteredReports = allReports.filter(function (report) {
      const reportCategory = String(report.category_id ?? "");

      const reportStatus = String(report.status ?? "").trim();

      const categoryMatches =
        selectedCategory === "all" || reportCategory === selectedCategory;

      const statusMatches =
        selectedStatus === "all" || reportStatus === selectedStatus;

      // Hide resolved reports older than 7 days from the default map.
      // If "Resolved" is explicitly selected, show all resolved reports.
      let resolvedVisibilityMatches = true;

      if (
        selectedStatus === "all" &&
        reportStatus === "Resolved" &&
        report.resolved_at
      ) {
        const resolvedAt = new Date(
          String(report.resolved_at).replace(" ", "T"),
        );

        if (!Number.isNaN(resolvedAt.getTime())) {
          const now = new Date();

          const sevenDaysInMs = 7 * 24 * 60 * 60 * 1000;

          resolvedVisibilityMatches =
            now.getTime() - resolvedAt.getTime() <= sevenDaysInMs;
        }
      }

      return categoryMatches && statusMatches && resolvedVisibilityMatches;
    });

    console.log("Selected category:", selectedCategory);

    console.log("Selected status:", selectedStatus);

    console.log("Filtered reports:", filteredReports);

    displayReports(filteredReports);
  }

  // =====================================
  // LOAD REAL REPORTS FROM API
  // =====================================

  // =====================================
  // LOAD REPORTS
  // Use server-provided data first.
  // Fall back to API when not available.
  // =====================================

  if (Array.isArray(window.reportData) && window.reportData.length > 0) {
    console.log("Using reports provided by Map View:", window.reportData);

    allReports = window.reportData;

    applyFilters();
  } else {
    fetch("/api/locations", {
      method: "GET",

      headers: {
        Accept: "application/json",
      },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Unable to load reports from API.");
        }

        return response.json();
      })

      .then(function (result) {
        console.log("Reports received from API:", result);

        if (!result.success || !Array.isArray(result.data)) {
          throw new Error("Invalid report data received.");
        }

        allReports = result.data.map(function (report) {
          return {
            ...report,

            // Compatibility with existing DB/API spelling.
            longitude: report.longitude ?? report.longtitude,
          };
        });

        applyFilters();
      })

      .catch(function (error) {
        console.error("Map report loading error:", error);
      });
  }

  // =====================================
  // CATEGORY FILTER EVENT
  // =====================================

  if (categoryFilter) {
    categoryFilter.addEventListener("change", function () {
      applyFilters();
    });
  }

  // =====================================
  // STATUS FILTER EVENT
  // =====================================

  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      applyFilters();
    });
  }

  // =====================================
  // SAGUING LOCATION AUTOCOMPLETE
  // =====================================

  const locationSuggestions = document.getElementById("locationSuggestions");

  const saguingPuroks = Array.isArray(window.saguingPuroks)
    ? window.saguingPuroks
    : [];

  const saguingLocations = Array.isArray(window.saguingLocations)
    ? window.saguingLocations
    : [];

  let searchedLocationMarker = null;

  // =====================================
  // HIDE AUTOCOMPLETE
  // =====================================

  function hideLocationSuggestions() {
    if (!locationSuggestions) return;

    locationSuggestions.innerHTML = "";
    locationSuggestions.classList.add("d-none");
  }

  // =====================================
  // MOVE MAP TO STORED COORDINATES
  // =====================================

  function moveMapToLocation(location) {
    const latitude = parseFloat(location.latitude);
    const longitude = parseFloat(location.longitude);

    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
      alert("This location does not have valid map coordinates.");
      return;
    }

    // Fly / zoom to selected place
    map.flyTo([latitude, longitude], 17, {
      animate: true,
      duration: 1.5,
    });

    // Remove previous search marker
    if (searchedLocationMarker) {
      map.removeLayer(searchedLocationMarker);
    }

    // Add temporary searched-location marker
    searchedLocationMarker = L.marker([latitude, longitude])
      .addTo(map)
      .bindPopup(
        `
      <div>
        <strong>
          ${escapeHtml(location.location_name)}
        </strong>

        <br>

        <span>
          ${escapeHtml(location.location_type || "Location")}
        </span>

        <br>

        <small>
          ${escapeHtml(
            location.address || "Barangay Saguing, Makilala, Cotabato",
          )}
        </small>
      </div>
    `,
      )
      .openPopup();

    console.log(
      "Map moved to stored location:",
      location.location_name,
      latitude,
      longitude,
    );
  }

  // =====================================
  // TRY TO LOCATE PUROK USING OSM
  // =====================================

  // =====================================
  // GO TO VERIFIED SAGUING PUROK
  // =====================================

  function goToSaguingPurok(purok) {
    const purokName = String(purok.purok_name || "").trim();

    const latitude = parseFloat(purok.latitude);

    const longitude = parseFloat(purok.longitude);

    // No verified coordinates yet
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
      alert(
        "Exact map coordinates for " + purokName + " are not yet verified.",
      );

      return;
    }

    // Move map
    map.flyTo([latitude, longitude], 17, {
      animate: true,
      duration: 1.5,
    });

    // Remove previous searched marker
    if (searchedLocationMarker) {
      map.removeLayer(searchedLocationMarker);
    }

    // Add marker
    searchedLocationMarker = L.marker([latitude, longitude])
      .addTo(map)
      .bindPopup(
        `
      <div>
        <strong>
          ${escapeHtml(purokName)}
        </strong>

        <br>

        <small>
          Barangay Saguing, Makilala, Cotabato
        </small>
      </div>
    `,
      )
      .openPopup();

    console.log("Map moved to verified Purok:", purokName, latitude, longitude);
  }

  // =====================================
  // SHOW SEARCH SUGGESTIONS
  // =====================================

  function showLocationSuggestions(searchText) {
    if (!locationSuggestions) return;

    const query = String(searchText || "")
      .trim()
      .toLowerCase();

    if (query === "") {
      hideLocationSuggestions();
      return;
    }

    // =====================================
    // VERIFIED LOCATIONS
    // =====================================

    const matchedLocations = saguingLocations
      .filter(function (location) {
        const searchableText = [
          location.location_name,
          location.location_type,
          location.address,
          location.search_keywords,
        ]
          .join(" ")
          .toLowerCase();

        return searchableText.includes(query);
      })
      .map(function (location) {
        return {
          source: "location",
          name: location.location_name,
          type: location.location_type,
          address: location.address,
          data: location,
        };
      });

    // =====================================
    // PUROKS
    // =====================================

    const matchedPuroks = saguingPuroks
      .filter(function (purok) {
        const purokName = String(purok.purok_name || "").toLowerCase();

        return purokName.includes(query);
      })
      .map(function (purok) {
        return {
          source: "purok",
          name: purok.purok_name,
          type: "Purok",
          address: "Barangay Saguing, Makilala, Cotabato",
          data: purok,
        };
      });

    // Combine both lists
    const matches = [...matchedLocations, ...matchedPuroks];

    locationSuggestions.innerHTML = "";

    // =====================================
    // NO RESULTS
    // =====================================

    if (matches.length === 0) {
      const emptyItem = document.createElement("div");

      emptyItem.className = "location-suggestion-empty";

      emptyItem.textContent = "No matching location found in Barangay Saguing.";

      locationSuggestions.appendChild(emptyItem);

      locationSuggestions.classList.remove("d-none");

      return;
    }

    // =====================================
    // CREATE SUGGESTION BUTTONS
    // =====================================

    matches.forEach(function (result) {
      const item = document.createElement("button");

      item.type = "button";

      item.className = "location-suggestion-item";

      item.innerHTML = `
      <strong>
        ${escapeHtml(result.name)}
      </strong>

      <small>
        ${escapeHtml(result.type)}
        ·
        ${escapeHtml(result.address)}
      </small>
    `;

      // =====================================
      // CLICK RESULT
      // =====================================

      item.addEventListener("click", async function () {
        searchLocation.value =
          result.name + ", Barangay Saguing, Makilala, Cotabato";

        hideLocationSuggestions();

        // Verified database place
        if (result.source === "location") {
          moveMapToLocation(result.data);

          return;
        }

        // Purok
        if (result.source === "purok") {
          goToSaguingPurok(result.data);
        }
      });

      locationSuggestions.appendChild(item);
    });

    locationSuggestions.classList.remove("d-none");
  }

  // =====================================
  // SEARCH EVENTS
  // =====================================

  if (searchLocation) {
    searchLocation.addEventListener("input", function () {
      showLocationSuggestions(this.value);
    });

    searchLocation.addEventListener("focus", function () {
      if (this.value.trim() !== "") {
        showLocationSuggestions(this.value);
      }
    });
  }

  // =====================================
  // CLICK OUTSIDE = CLOSE AUTOCOMPLETE
  // =====================================

  document.addEventListener("click", function (event) {
    if (
      searchLocation &&
      locationSuggestions &&
      !searchLocation.contains(event.target) &&
      !locationSuggestions.contains(event.target)
    ) {
      hideLocationSuggestions();
    }
  });
});
