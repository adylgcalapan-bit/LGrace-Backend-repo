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
  const priorityFilter = document.getElementById("reportPriorityFilter");
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

  // =====================================
  // STATUS CUSTOM DROPDOWN
  // =====================================

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

  // =====================================
  // PRIORITY CUSTOM DROPDOWN
  // =====================================

  document.querySelectorAll(".report-priority-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!priorityFilter || !filtersForm) {
        return;
      }

      const value = this.dataset.value || "all";
      const label = this.textContent.trim();

      priorityFilter.value = value;

      const priorityLabel = document.getElementById("reportPriorityLabel");

      if (priorityLabel) {
        priorityLabel.textContent = label;
      }

      filtersForm.requestSubmit();
    });
  });

  // =====================================
  // CATEGORY CUSTOM DROPDOWN
  // =====================================

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

  // =====================================
  // SORT CUSTOM DROPDOWN
  // =====================================

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

  // =====================================
  // VIEW REPORT MODAL
  // =====================================

  const viewButtons = document.querySelectorAll(".view-btn");
  const reportModal = document.getElementById("reportModal");

  let reportMapInstance = null;
  let reportMapMarker = null;

  viewButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.stopPropagation();

      const row = this.closest(".report-row");

      if (!row || !reportModal) {
        return;
      }

      // =====================================
      // GET REPORT DATA
      // =====================================

      const reportNo = row.getAttribute("data-report-no") || "";

      const resident = row.getAttribute("data-resident") || "Unknown Resident";

      const residentId = row.getAttribute("data-resident-id") || "N/A";

      const title = row.getAttribute("data-title") || "Untitled Report";

      const category = row.getAttribute("data-category") || "No Category";

      const location = row.getAttribute("data-location") || "";

      const address =
        row.getAttribute("data-address") || "No address available";

      const purok = row.getAttribute("data-purok") || "Not specified";

      const latitude = parseFloat(row.getAttribute("data-latitude"));

      const longitude = parseFloat(row.getAttribute("data-longitude"));

      const date = row.getAttribute("data-date") || "";

      const status = row.getAttribute("data-status") || "";

      const description = row.getAttribute("data-description") || "";

      // =====================================
      // REPORT PHOTOS
      // Supports multiple photos and old
      // single-photo data for compatibility.
      // =====================================

      let photos = [];

      const photosRaw = row.getAttribute("data-photos") || "";

      if (photosRaw !== "") {
        try {
          const parsedPhotos = JSON.parse(photosRaw);

          if (Array.isArray(parsedPhotos)) {
            photos = parsedPhotos.filter(Boolean);
          }
        } catch (error) {
          console.error("Unable to read report photos:", error);
        }
      }

      // Backward compatibility
      if (photos.length === 0) {
        const singlePhoto = row.getAttribute("data-photo") || "";

        if (singlePhoto) {
          photos.push(singlePhoto);
        }
      }

      // =====================================
      // FILL REPORT DETAILS
      // =====================================

      const setModalText = (selector, value, fallback = "N/A") => {
        const element = reportModal.querySelector(selector);

        if (element) {
          element.textContent =
            value !== null && value !== undefined && String(value).trim() !== ""
              ? value
              : fallback;
        }
      };

      const modalTitle = reportModal.querySelector(".modal-title");

      if (modalTitle) {
        modalTitle.textContent = "Report Details";
      }

      setModalText("#reportNo", reportNo);
      setModalText("#reportResident", resident);
      setModalText("#reportResidentId", residentId);

      // Hide Resident No. when the resident account
      // connected to the historical report was deleted.
      const residentNoElement = reportModal.querySelector("#reportResidentId");

      const residentNoRow = residentNoElement
        ? residentNoElement.closest("p")
        : null;

      const isDeletedAccount = resident.includes("(Deleted Account)");

      if (residentNoRow) {
        residentNoRow.style.display = isDeletedAccount ? "none" : "";
      }

      setModalText("#reportTitle", title);
      setModalText("#reportCategory", category);
      setModalText("#reportStatus", status);
      setModalText("#reportLocation", location);
      setModalText("#reportAddress", address);
      setModalText("#reportPurok", purok);
      setModalText("#reportDate", date);

      setModalText(
        "#reportDescription",
        description,
        "No description provided.",
      );

      // =====================================
      // DISPLAY PHOTOS
      // =====================================

      const photoContainer = reportModal.querySelector("#reportPhotos");

      const noPhotoElement = reportModal.querySelector("#reportNoPhoto");

      if (photoContainer) {
        photoContainer.innerHTML = "";
      }

      if (photos.length > 0) {
        photos.slice(0, 5).forEach((photoUrl, index) => {
          const image = document.createElement("img");

          image.src = photoUrl;
          image.alt = `Report Photo ${index + 1}`;

          image.className = "img-fluid rounded";

          image.style.width = "140px";
          image.style.height = "120px";
          image.style.objectFit = "cover";
          image.style.cursor = "zoom-in";

          image.addEventListener("click", function () {
            const overlay = document.createElement("div");

            overlay.style.position = "fixed";
            overlay.style.inset = "0";
            overlay.style.background = "rgba(0, 0, 0, 0.85)";
            overlay.style.zIndex = "2000";
            overlay.style.display = "flex";
            overlay.style.alignItems = "center";
            overlay.style.justifyContent = "center";
            overlay.style.padding = "30px";
            overlay.style.cursor = "zoom-out";

            const enlargedImage = document.createElement("img");

            enlargedImage.src = photoUrl;
            enlargedImage.alt = `Report Photo ${index + 1}`;

            enlargedImage.style.maxWidth = "95%";
            enlargedImage.style.maxHeight = "95%";
            enlargedImage.style.objectFit = "contain";
            enlargedImage.style.borderRadius = "10px";

            overlay.appendChild(enlargedImage);
            document.body.appendChild(overlay);

            overlay.addEventListener("click", function () {
              overlay.remove();
            });
          });

          if (photoContainer) {
            photoContainer.appendChild(image);
          }
        });

        if (noPhotoElement) {
          noPhotoElement.style.display = "none";
        }
      } else {
        if (noPhotoElement) {
          noPhotoElement.style.display = "inline";
        }
      }

      // =====================================
      // OPEN MODAL
      // =====================================

      const modal = bootstrap.Modal.getOrCreateInstance(reportModal);

      modal.show();

      // =====================================
      // REPORT LOCATION MAP
      // =====================================

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

          if (typeof L === "undefined") {
            return;
          }

          if (!reportMapInstance) {
            reportMapInstance = L.map("reportMap");

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
              maxZoom: 19,
              attribution: "&copy; OpenStreetMap contributors",
            }).addTo(reportMapInstance);
          }

          reportMapInstance.setView([latitude, longitude], 17);

          if (reportMapMarker) {
            reportMapInstance.removeLayer(reportMapMarker);
          }

          reportMapMarker = L.marker([latitude, longitude])
            .addTo(reportMapInstance)
            .bindPopup(
              `<strong>${title || "Reported Location"}</strong><br>${
                address || "No address available"
              }`,
            )
            .openPopup();

          setTimeout(() => {
            reportMapInstance.invalidateSize();
          }, 150);
        },
        { once: true },
      );
    });
  });
});

const updateStatusButtons = document.querySelectorAll(".update-status-btn");

const statusModal = document.getElementById("statusModal");

if (statusModal && statusModal.parentElement !== document.body) {
  document.body.appendChild(statusModal);
}

const statusReportId = document.getElementById("statusReportId");

const statusSelect = document.getElementById("statusSelect");
const prioritySelect = document.getElementById("prioritySelect");

updateStatusButtons.forEach((button) => {
  button.addEventListener("click", function () {
    const reportId = this.getAttribute("data-report-id");

    const currentStatus = this.getAttribute("data-current-status");
    const currentPriority = button.getAttribute("data-current-priority") || "";

    if (statusReportId) {
      statusReportId.value = reportId;
    }
    if (prioritySelect) {
      prioritySelect.value = currentPriority;
    }

    if (statusSelect) {
      statusSelect.value = currentStatus;
    }
  });
});

tableRows.forEach((row) => {
  row.addEventListener("mouseenter", function () {
    this.style.cursor = "pointer";
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
