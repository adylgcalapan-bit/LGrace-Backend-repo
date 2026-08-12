// ==========================================
// RESIDENTS PAGE JAVASCRIPT
// File: js/residents.js
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  console.log("Residents Management Loaded");

  // ==========================
  // SEARCH RESIDENTS
  // ==========================

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

  // ==========================
  // VIEW RESIDENT OVERVIEW
  // ==========================

  const residentRows = document.querySelectorAll(".resident-row");
  const residentModal = document.getElementById("residentModal");

  function formatResidentDate(value) {
    if (!value) {
      return "N/A";
    }

    const parsedDate = new Date(String(value).replace(" ", "T"));

    if (Number.isNaN(parsedDate.getTime())) {
      return value;
    }

    return parsedDate.toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
  }

  function getStatusBadgeClass(status) {
    switch (status) {
      case "Pending":
        return "bg-warning text-dark";

      case "In Progress":
        return "bg-primary";

      case "Resolved":
        return "bg-success";

      case "Rejected":
        return "bg-danger";

      default:
        return "bg-secondary";
    }
  }

  function showNoRecentReports(tbody) {
    tbody.innerHTML = "";

    const row = document.createElement("tr");
    const cell = document.createElement("td");

    cell.colSpan = 4;
    cell.className = "text-center text-muted";
    cell.textContent = "No reports submitted yet.";

    row.appendChild(cell);
    tbody.appendChild(row);
  }

  function renderRecentReports(reports) {
    const tbody = document.getElementById("residentRecentReports");

    if (!tbody) {
      return;
    }

    tbody.innerHTML = "";

    if (!Array.isArray(reports) || reports.length === 0) {
      showNoRecentReports(tbody);
      return;
    }

    reports.forEach((report) => {
      const row = document.createElement("tr");

      // Report title
      const titleCell = document.createElement("td");
      titleCell.textContent =
        "#" + report.report_id + " - " + (report.title || "Untitled Report");

      // Category
      const categoryCell = document.createElement("td");
      categoryCell.textContent = report.category_name || "Uncategorized";

      // Date
      const dateCell = document.createElement("td");
      dateCell.textContent = formatResidentDate(report.date_reported);

      // Status
      const statusCell = document.createElement("td");

      const badge = document.createElement("span");
      badge.className = "badge " + getStatusBadgeClass(report.status);

      badge.textContent = report.status || "Unknown";

      statusCell.appendChild(badge);

      row.appendChild(titleCell);
      row.appendChild(categoryCell);
      row.appendChild(dateCell);
      row.appendChild(statusCell);

      tbody.appendChild(row);
    });
  }

  async function loadResidentDetails(row) {
    if (!residentModal || !row) {
      return;
    }

    const userId = row.dataset.userId;

    if (!userId) {
      console.error("Resident user ID is missing.");
      return;
    }

    const modal = new bootstrap.Modal(residentModal);

    modal.show();

    // Loading state
    document.getElementById("residentModalName").textContent = "Loading...";

    document.getElementById("residentModalUsername").textContent = "—";

    document.getElementById("residentModalEmail").textContent = "—";

    document.getElementById("residentModalContact").textContent = "—";

    document.getElementById("residentModalAddress").textContent = "—";

    document.getElementById("residentModalRegistered").textContent = "—";

    document.getElementById("residentModalId").textContent = "—";

    document.getElementById("residentStatTotal").textContent = "0";

    document.getElementById("residentStatPending").textContent = "0";

    document.getElementById("residentStatProgress").textContent = "0";

    document.getElementById("residentStatResolved").textContent = "0";

    const recentReportsBody = document.getElementById("residentRecentReports");

    if (recentReportsBody) {
      recentReportsBody.innerHTML =
        '<tr><td colspan="4" class="text-center text-muted">Loading reports...</td></tr>';
    }

    try {
      const response = await fetch(`/admin/residents/details/${userId}`, {
        headers: {
          Accept: "application/json",
        },
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || "Unable to load resident details.");
      }

      const resident = result.resident || {};
      const stats = result.statistics || {};

      document.getElementById("residentModalImage").src =
        resident.image_url || "";

      document.getElementById("residentModalId").textContent =
        resident.resident_id || "N/A";

      document.getElementById("residentModalName").textContent =
        resident.full_name || "N/A";

      document.getElementById("residentModalUsername").textContent =
        resident.username || "N/A";

      document.getElementById("residentModalEmail").textContent =
        resident.email || "N/A";

      document.getElementById("residentModalContact").textContent =
        resident.mobile_number || "N/A";

      document.getElementById("residentModalAddress").textContent =
        resident.address || "No address provided";

      document.getElementById("residentModalRegistered").textContent =
        formatResidentDate(resident.created_at);

      document.getElementById("residentStatTotal").textContent =
        stats.total ?? 0;

      document.getElementById("residentStatPending").textContent =
        stats.pending ?? 0;

      document.getElementById("residentStatProgress").textContent =
        stats.in_progress ?? 0;

      document.getElementById("residentStatResolved").textContent =
        stats.resolved ?? 0;

      renderRecentReports(result.recent_reports || []);
    } catch (error) {
      console.error("Resident details error:", error);

      document.getElementById("residentModalName").textContent =
        "Unable to load resident";

      if (recentReportsBody) {
        recentReportsBody.innerHTML =
          '<tr><td colspan="4" class="text-center text-danger">Unable to load resident information.</td></tr>';
      }
    }
  }

  residentRows.forEach((row) => {
    row.addEventListener("click", function () {
      loadResidentDetails(row);
    });
  });

  // ==========================
  // EDIT RESIDENT
  // ==========================

  const editButtons = document.querySelectorAll("tbody .btn-warning");

  editButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const modal = new bootstrap.Modal(
        document.getElementById("editResidentModal"),
      );

      modal.show();
    });
  });

  // ==========================
  // DELETE RESIDENT
  // ==========================

  const deleteButtons = document.querySelectorAll("tbody .btn-danger");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const modal = new bootstrap.Modal(
        document.getElementById("deleteResidentModal"),
      );

      modal.show();
    });
  });

  // ==========================
  // SAVE CHANGES
  // ==========================

  const saveButton = document.querySelector("#editResidentModal .btn-success");

  if (saveButton) {
    saveButton.addEventListener("click", () => {
      alert("Resident information updated successfully.");
    });
  }

  // ==========================
  // DELETE CONFIRMATION
  // ==========================

  const confirmDelete = document.querySelector(
    "#deleteResidentModal .btn-danger",
  );

  if (confirmDelete) {
    confirmDelete.addEventListener("click", () => {
      alert("Resident deleted successfully.");
    });
  }

  // ==========================
  // ADD RESIDENT
  // ==========================

  const addResidentBtn = document.querySelector(".top-actions .btn-success");

  if (addResidentBtn) {
    addResidentBtn.addEventListener("click", () => {
      alert("Add Resident module will be added in the backend.");
    });
  }

  // ==========================
  // PAGINATION
  // ==========================

  const pageLinks = document.querySelectorAll(".pagination .page-link");

  pageLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      pageLinks.forEach((item) => {
        item.parentElement.classList.remove("active");
      });

      if (!this.parentElement.classList.contains("disabled")) {
        this.parentElement.classList.add("active");
      }
    });
  });

  // ==========================
  // LOGOUT
  // ==========================

  const logout = document.querySelector(".logout");

  if (logout) {
    logout.addEventListener("click", function (e) {
      e.preventDefault();

      const answer = confirm("Are you sure you want to logout?");

      if (answer) {
        window.location.href = "/login";
      }
    });
  }

  // ==========================
  // AUTO REFRESH
  // ==========================

  setInterval(() => {
    console.log("Refreshing resident records...");
  }, 30000);
});
