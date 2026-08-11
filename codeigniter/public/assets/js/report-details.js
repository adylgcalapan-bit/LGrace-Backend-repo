document.addEventListener("DOMContentLoaded", function () {
  const mapContainer = document.getElementById("report-map");

  if (!mapContainer || typeof L === "undefined") {
    return;
  }

  const latitude = parseFloat(mapContainer.getAttribute("data-latitude"));

  const longitude = parseFloat(mapContainer.getAttribute("data-longitude"));

  if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
    mapContainer.innerHTML = '<p class="text-muted">Location unavailable.</p>';

    return;
  }

  const map = L.map("report-map").setView([latitude, longitude], 17);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  L.marker([latitude, longitude])
    .addTo(map)
    .bindPopup("Report Location")
    .openPopup();
});
