// ==========================================
// RESIDENTS PAGE JAVASCRIPT
// File: js/residents.js
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  console.log("Residents Management Loaded");

  // ==========================
  // SEARCH + PUROK FILTER
  // ==========================

  const searchInput = document.getElementById("residentSearch");
  const statusFilter = document.getElementById("statusFilter");
  const purokFilter = document.getElementById("purokFilter");
  const tableRows = document.querySelectorAll(".resident-row");

  function applyResidentFilters() {
    const searchValue = searchInput
      ? searchInput.value.toLowerCase().trim()
      : "";

    const selectedStatus = statusFilter
      ? statusFilter.value.toLowerCase()
      : "all status";

    const selectedPurok = purokFilter ? purokFilter.value : "";

    tableRows.forEach((row) => {
      const rowText = row.textContent.toLowerCase();

      const rowStatus = (row.getAttribute("data-status") || "").toLowerCase();

      const rowPurokId = row.getAttribute("data-purok-id") || "unassigned";

      const matchesSearch = searchValue === "" || rowText.includes(searchValue);

      const matchesStatus =
        selectedStatus === "all status" || rowStatus === selectedStatus;

      const matchesPurok = selectedPurok === "" || rowPurokId === selectedPurok;

      row.style.display =
        matchesSearch && matchesStatus && matchesPurok ? "" : "none";
    });
  }

  if (searchInput) {
    searchInput.addEventListener("input", applyResidentFilters);
  }

  if (statusFilter) {
    statusFilter.addEventListener("change", applyResidentFilters);
  }

  if (purokFilter) {
    purokFilter.addEventListener("change", applyResidentFilters);
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

      // ==========================
      // ACCOUNT STATUS
      // ==========================

      const statusBadge = document.getElementById("residentModalStatus");
      const statusForm = document.getElementById("residentStatusForm");
      const statusButton = document.getElementById("residentStatusButton");
      const statusButtonIcon = document.getElementById(
        "residentStatusButtonIcon",
      );
      const statusButtonText = document.getElementById(
        "residentStatusButtonText",
      );

      const isActive = Number(resident.is_active) === 1;

      // Status badge
      if (isActive) {
        statusBadge.textContent = "Active";
        statusBadge.className = "badge bg-success";
      } else {
        statusBadge.textContent = "Inactive";
        statusBadge.className = "badge bg-secondary";
      }

      // Connect form to real resident
      statusForm.action = `/admin/residents/${resident.user_id}/status`;

      statusButton.disabled = false;

      // Button appearance
      if (isActive) {
        statusButton.className = "btn btn-danger";
        statusButtonIcon.className = "bi bi-person-dash-fill me-1";

        statusButtonText.textContent = "Deactivate Account";
      } else {
        statusButton.className = "btn btn-success";
        statusButtonIcon.className = "bi bi-person-check-fill me-1";

        statusButtonText.textContent = "Activate Account";
      }

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

  // ==========================
  // ADD RESIDENT PASSWORD UX
  // ==========================

  document.querySelectorAll(".toggle-resident-password").forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.dataset.target;
      const input = document.getElementById(targetId);
      const icon = button.querySelector("i");

      if (!input) return;

      const isHidden = input.type === "password";

      input.type = isHidden ? "text" : "password";

      if (icon) {
        icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
      }

      button.title = isHidden ? "Hide Password" : "Show Password";
    });
  });

  const addResidentForm = document.getElementById("addResidentForm");

  const residentPassword = document.getElementById("residentPassword");

  const residentConfirmPassword = document.getElementById(
    "residentConfirmPassword",
  );

  if (addResidentForm && residentPassword && residentConfirmPassword) {
    addResidentForm.addEventListener("submit", (event) => {
      residentConfirmPassword.setCustomValidity("");

      if (residentPassword.value !== residentConfirmPassword.value) {
        event.preventDefault();

        residentConfirmPassword.setCustomValidity("Passwords do not match.");

        residentConfirmPassword.reportValidity();
      }
    });

    residentConfirmPassword.addEventListener("input", () => {
      residentConfirmPassword.setCustomValidity("");

      if (
        residentConfirmPassword.value !== "" &&
        residentPassword.value !== residentConfirmPassword.value
      ) {
        residentConfirmPassword.setCustomValidity("Passwords do not match.");
      }
    });

    residentPassword.addEventListener("input", () => {
      residentConfirmPassword.setCustomValidity("");
    });
  }

  // ==========================
  // ACTIVATE / DEACTIVATE
  // ==========================

  const residentStatusForm = document.getElementById("residentStatusForm");

  if (residentStatusForm) {
    residentStatusForm.addEventListener("submit", function (event) {
      const buttonText = document
        .getElementById("residentStatusButtonText")
        ?.textContent.trim();

      const isDeactivate = buttonText === "Deactivate Account";

      const message = isDeactivate
        ? "Deactivate this resident account? The resident will no longer be able to log in until the account is activated again."
        : "Activate this resident account? The resident will be able to log in and use the system again.";

      if (!confirm(message)) {
        event.preventDefault();
      }
    });
  }
});
