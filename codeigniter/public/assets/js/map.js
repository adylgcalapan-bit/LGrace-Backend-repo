document.addEventListener("DOMContentLoaded", function () {
  // Default location sa inyong project area
  const map = L.map("map").setView([7.0083, 125.0894], 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  // Real reports gikan sa PHP/database
  const reports = Array.isArray(window.reportData) ? window.reportData : [];

  const statusColors = {
    Pending: "#ff9800",
    "In Progress": "#2196f3",
    Resolved: "#28a745",
    Rejected: "#dc3545",
  };

  const markers = [];

  function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value ?? "";
    return div.innerHTML;
  }

  reports.forEach(function (report) {
    const latitude = parseFloat(report.latitude);
    const longitude = parseFloat(report.longtitude);

    if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
      return;
    }

    const color = statusColors[report.status] || "#6c757d";

    const marker = L.circleMarker([latitude, longitude], {
      radius: 8,
      color: color,
      fillColor: color,
      fillOpacity: 0.9,
    }).addTo(map);

    const photoHtml = report.image_url
      ? `<img src="${escapeHtml(report.image_url)}"
             alt="Report Photo"
             style="width:180px;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;"><br>`
      : `<small>No photo available</small><br>`;

    marker.bindPopup(`
    ${photoHtml}
    <strong>${escapeHtml(report.title)}</strong><br>
    Category: ${escapeHtml(report.category_name ?? "No Category")}<br>
    Status: ${escapeHtml(report.status)}<br>
    ${report.address ? `Location: ${escapeHtml(report.address)}<br>` : ""}
    <small>Report ID: ${escapeHtml(report.report_id)}</small>
`);

    markers.push(marker);
  });

  // Automatic zoom para makita tanan real markers
  if (markers.length > 0) {
    const markerGroup = L.featureGroup(markers);

    map.fitBounds(markerGroup.getBounds().pad(0.2));
  }

  // =====================================
  // Existing Search / Filter Controls
  // =====================================

  const searchLocation = document.getElementById("searchLocation");

  const categoryFilter = document.getElementById("categoryFilter");

  const statusFilter = document.getElementById("statusFilter");

  if (searchLocation) {
    searchLocation.addEventListener("input", function (event) {
      console.log("Search location:", event.target.value);
    });
  }

  if (categoryFilter) {
    categoryFilter.addEventListener("change", function () {
      console.log("Category filter:", categoryFilter.value);
    });
  }

  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      console.log("Status filter:", statusFilter.value);
    });
  }
});
