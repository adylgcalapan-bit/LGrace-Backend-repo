// ==========================================
// REPORTS PAGE JAVASCRIPT
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  console.log("Reports Page Loaded");

  const searchInput = document.querySelector("input[type='text']");
  const tableRows = document.querySelectorAll("tbody tr");

  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const value = this.value.toLowerCase();

      tableRows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
      });
    });
  }

  const filterButton = document.querySelector(".filter-btn");
  if (filterButton) {
    filterButton.addEventListener("click", function () {
      alert("Filter feature will be connected to the database later.");
    });
  }

  const viewButtons = document.querySelectorAll(".view-btn");
  const reportModal = document.getElementById("reportModal");

  viewButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest(".report-row");

      if (!row || !reportModal) {
        return;
      }

      const reportId = row.getAttribute("data-id") || "";
      const resident = row.getAttribute("data-resident") || "";
      const title = row.getAttribute("data-title") || "";
      const category = row.getAttribute("data-category") || "";
      const location = row.getAttribute("data-location") || "";
      const address = row.getAttribute("data-address") || "";
      const date = row.getAttribute("data-date") || "";
      const status = row.getAttribute("data-status") || "";
      const description = row.getAttribute("data-description") || "";
      const photo = row.getAttribute("data-photo") || "";

      reportModal.querySelector(".modal-title").textContent =
        `Report Details - ${reportId}`;

      reportModal.querySelector("#reportId").textContent = reportId;
      reportModal.querySelector("#reportResident").textContent = resident;
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

  if (tableBody) {
    tableBody.addEventListener("click", function (event) {
      const button = event.target.closest("button");
      if (!button) return;

      const row = button.closest("tr");
      const statusBadge = row.querySelector(".status-badge");
      const actionCell = row.querySelector(".action-cell");

      if (button.classList.contains("accept-btn")) {
        if (statusBadge) {
          statusBadge.className = "badge bg-primary status-badge";
          statusBadge.textContent = "In Progress";
        }
        row.setAttribute("data-state", "in-progress");
        const prioritySelect = row.querySelector(".priority-select");
        if (prioritySelect) {
          prioritySelect.disabled = false;
        }
        if (actionCell) {
          actionCell.innerHTML =
            '<div class="action-buttons"><button class="btn btn-sm btn-success resolve-btn">Resolve</button></div>';
        }
      }

      if (button.classList.contains("decline-btn")) {
        if (statusBadge) {
          statusBadge.className = "badge bg-danger status-badge";
          statusBadge.textContent = "Rejected";
        }
        row.setAttribute("data-state", "rejected");
        const prioritySelect = row.querySelector(".priority-select");
        if (prioritySelect) {
          prioritySelect.disabled = true;
        }
        if (actionCell) {
          actionCell.innerHTML =
            '<span class="text-muted small">Declined</span>';
        }
      }

      if (button.classList.contains("resolve-btn")) {
        if (statusBadge) {
          statusBadge.className = "badge bg-success status-badge";
          statusBadge.textContent = "Resolved";
        }
        row.setAttribute("data-state", "resolved");
        const prioritySelect = row.querySelector(".priority-select");
        if (prioritySelect) {
          prioritySelect.disabled = false;
        }
        if (actionCell) {
          actionCell.innerHTML =
            '<span class="text-success small">Completed</span>';
        }
      }
    });
  }

  tableRows.forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.cursor = "pointer";
    });
  });

  const exportButton = document.querySelector(".export-btn");
  if (exportButton) {
    exportButton.addEventListener("click", function () {
      alert("Export feature will be available soon.");
    });
  }

  const pages = document.querySelectorAll(".pagination .page-link");
  pages.forEach((page) => {
    page.addEventListener("click", function (e) {
      e.preventDefault();

      pages.forEach((item) => {
        item.parentElement.classList.remove("active");
      });

      if (!this.parentElement.classList.contains("disabled")) {
        this.parentElement.classList.add("active");
      }
    });
  });

  setInterval(() => {
    console.log("Checking for new reports...");
  }, 30000);
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
