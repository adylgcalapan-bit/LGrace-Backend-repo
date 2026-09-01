// ==========================================
// REPORTS PAGE JAVASCRIPT
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  console.log("Reports Page Loaded");

  const tableRows = document.querySelectorAll("tbody tr");

  const filtersForm = document.getElementById("reportFiltersForm");
  const searchInput = document.getElementById("reportSearch");
  const categoryFilter = document.getElementById("reportCategory");
  const statusFilter = document.getElementById("reportStatusFilter");
  const fromDate = document.getElementById("reportFromDate");
  const toDate = document.getElementById("reportToDate");
  const sortFilter = document.getElementById("reportSort");

  let searchTimer;

  if (searchInput && filtersForm) {
    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimer);

      searchTimer = setTimeout(() => {
        filtersForm.requestSubmit();
      }, 500);
    });
  }

  [categoryFilter, statusFilter, fromDate, toDate, sortFilter].forEach(
    (filter) => {
      if (filter && filtersForm) {
        filter.addEventListener("change", () => {
          filtersForm.requestSubmit();
        });
      }
    },
  );

  document.querySelectorAll(".report-status-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!statusFilter || !filtersForm) {
        return;
      }

      const value = this.dataset.value || "all";
      const label = this.textContent.trim();

      statusFilter.value = value;

      const statusLabel = document.getElementById("reportStatusLabel");

      if (statusLabel) {
        statusLabel.textContent = label;
      }

      filtersForm.requestSubmit();
    });
  });

  // SORT CUSTOM DROPDOWN
  document.querySelectorAll(".report-sort-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!sortFilter || !filtersForm) {
        return;
      }

      const value = this.dataset.value || "newest";
      const label = this.textContent.trim();

      sortFilter.value = value;

      const sortLabel = document.getElementById("reportSortLabel");

      if (sortLabel) {
        sortLabel.textContent = label;
      }

      filtersForm.requestSubmit();
    });
  });

  document.querySelectorAll(".report-category-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!categoryFilter || !filtersForm) {
        return;
      }

      const value = this.dataset.value || "0";
      const label = this.textContent.trim();

      categoryFilter.value = value;

      const categoryLabel = document.getElementById("reportCategoryLabel");

      if (categoryLabel) {
        categoryLabel.textContent = label;
      }

      filtersForm.requestSubmit();
    });
  });

  const viewButtons = document.querySelectorAll(".view-btn");
  const reportModal = document.getElementById("reportModal");
  let reportMapInstance = null;
  let reportMapMarker = null;

  viewButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest(".report-row");

      if (!row || !reportModal) {
        return;
      }

      const reportId = row.getAttribute("data-id") || "";
      const resident = row.getAttribute("data-resident") || "";
      const residentId = row.getAttribute("data-resident-id") || "";
      const title = row.getAttribute("data-title") || "";
      const category = row.getAttribute("data-category") || "";
      const location = row.getAttribute("data-location") || "";
      const address = row.getAttribute("data-address") || "";
      const latitude = parseFloat(row.getAttribute("data-latitude"));

      const longitude = parseFloat(row.getAttribute("data-longitude"));
      const date = row.getAttribute("data-date") || "";
      const status = row.getAttribute("data-status") || "";
      const description = row.getAttribute("data-description") || "";
      const photo = row.getAttribute("data-photo") || "";

      reportModal.querySelector(".modal-title").textContent =
        `Report Details - ${reportId}`;

      reportModal.querySelector("#reportId").textContent = reportId;
      reportModal.querySelector("#reportResident").textContent = resident;
      reportModal.querySelector("#reportResidentId").textContent = residentId;
      reportModal.querySelector("#reportTitle").textContent = title;
      reportModal.querySelector("#reportCategory").textContent = category;
      reportModal.querySelector("#reportStatus").textContent = status;
      reportModal.querySelector("#reportLocation").textContent = location;
      reportModal.querySelector("#reportAddress").textContent = address;
      reportModal.querySelector("#reportDate").textContent = date;
      reportModal.querySelector("#reportDescription").textContent =
        description || "No description provided.";

      const photoElement = reportModal.querySelector("#reportPhoto");

      const noPhotoElement = reportModal.querySelector("#reportNoPhoto");

      if (photo) {
        photoElement.src = photo;
        photoElement.style.display = "block";
        noPhotoElement.style.display = "none";
      } else {
        photoElement.src = "";
        photoElement.style.display = "none";
        noPhotoElement.style.display = "inline";
      }

      const modal = new bootstrap.Modal(reportModal);
      modal.show();

      reportModal.addEventListener(
        "shown.bs.modal",
        function () {
          const mapElement = document.getElementById("reportMap");

          const mapMessage = document.getElementById("reportMapMessage");

          const hasValidCoordinates =
            Number.isFinite(latitude) &&
            Number.isFinite(longitude) &&
            !(latitude === 0 && longitude === 0);

          if (!mapElement) {
            return;
          }

          // No valid coordinates
          if (!hasValidCoordinates) {
            mapElement.style.display = "none";

            if (mapMessage) {
              mapMessage.classList.remove("d-none");
            }

            return;
          }

          mapElement.style.display = "block";

          if (mapMessage) {
            mapMessage.classList.add("d-none");
          }

          // Create map once
          if (!reportMapInstance) {
            reportMapInstance = L.map("reportMap");

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
              maxZoom: 19,
              attribution: "&copy; OpenStreetMap contributors",
            }).addTo(reportMapInstance);
          }

          // Move map to selected report
          reportMapInstance.setView([latitude, longitude], 17);

          // Remove previous report marker
          if (reportMapMarker) {
            reportMapInstance.removeLayer(reportMapMarker);
          }

          // Add marker for current report
          reportMapMarker = L.marker([latitude, longitude])
            .addTo(reportMapInstance)
            .bindPopup(
              `<strong>${title || "Reported Location"}</strong><br>${address || "No address available"}`,
            )
            .openPopup();

          // Important because map is inside Bootstrap modal
          setTimeout(() => {
            reportMapInstance.invalidateSize();
          }, 150);
        },
        { once: true },
      );
    });
  });

  const updateStatusButtons = document.querySelectorAll(".update-status-btn");

  const statusModal = document.getElementById("statusModal");

  const statusReportId = document.getElementById("statusReportId");

  const statusSelect = document.getElementById("statusSelect");
  const prioritySelect = document.getElementById("prioritySelect");

  updateStatusButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const reportId = this.getAttribute("data-report-id");

      const currentStatus = this.getAttribute("data-current-status");
      const currentPriority =
        button.getAttribute("data-current-priority") || "";

      if (statusReportId) {
        statusReportId.value = reportId;
      }
      if (prioritySelect) {
        prioritySelect.value = currentPriority;
      }

      if (statusSelect) {
        statusSelect.value = currentStatus;
      }

      if (statusModal) {
        const modal = bootstrap.Modal.getOrCreateInstance(statusModal);

        modal.show();
      }
    });
  });

  tableRows.forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.cursor = "pointer";
    });
  });
});

// =====================================
// Auto-open report from notification
// =====================================
document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const reportId = params.get("report_id");

  if (!reportId) {
    return;
  }

  const reportRows = document.querySelectorAll(".report-row");

  let targetRow = null;

  reportRows.forEach(function (row) {
    const rowId = (row.getAttribute("data-id") || "").replace("#", "");

    if (rowId === reportId) {
      targetRow = row;
    }
  });

  if (!targetRow) {
    console.error("Report not found:", reportId);
    return;
  }

  const viewButton = targetRow.querySelector(".view-btn");

  if (viewButton) {
    viewButton.click();
  }
});
