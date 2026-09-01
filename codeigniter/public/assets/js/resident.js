// ==========================================
// RESIDENTS PAGE JAVASCRIPT
// File: public/assets/js/resident.js
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  console.log("Residents Management Loaded");

  // ==========================================
  // SEARCH + FILTERS
  // Backend filtering across ALL residents
  // ==========================================

  const filtersForm = document.getElementById("residentFiltersForm");
  const searchInput = document.getElementById("residentSearch");
  const statusFilter = document.getElementById("statusFilter");
  const purokFilter = document.getElementById("purokFilter");

  let residentSearchTimer;

  // ==========================================
  // SEARCH
  // ==========================================

  if (searchInput && filtersForm) {
    searchInput.addEventListener("input", () => {
      clearTimeout(residentSearchTimer);

      residentSearchTimer = setTimeout(() => {
        filtersForm.requestSubmit();
      }, 500);
    });
  }

  // ==========================================
  // CUSTOM STATUS DROPDOWN
  // ==========================================

  document.querySelectorAll(".resident-status-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!statusFilter || !filtersForm) {
        return;
      }

      const selectedValue = this.dataset.value || "all";

      statusFilter.value = selectedValue;

      const label = document.getElementById("residentStatusLabel");

      if (label) {
        label.textContent = this.textContent.trim();
      }

      filtersForm.requestSubmit();
    });
  });

  // ==========================================
  // CUSTOM PUROK FILTER DROPDOWN
  // ==========================================

  document.querySelectorAll(".resident-purok-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!purokFilter || !filtersForm) {
        return;
      }

      const selectedValue = this.dataset.value ?? "";

      purokFilter.value = selectedValue;

      const label = document.getElementById("residentPurokLabel");

      if (label) {
        label.textContent = this.textContent.trim();
      }

      filtersForm.requestSubmit();
    });
  });

  // ==========================================
  // RESIDENT DETAILS
  // ==========================================

  const residentRows = document.querySelectorAll(".resident-row");
  const residentModal = document.getElementById("residentModal");

  let residentDateFormat = "MM/DD/YYYY";

  function formatResidentDate(value) {
    if (!value) {
      return "N/A";
    }

    const datePart = String(value).trim().substring(0, 10);
    const parts = datePart.split("-");

    if (parts.length !== 3) {
      return value;
    }

    const [year, month, day] = parts;

    if (residentDateFormat === "DD/MM/YYYY") {
      return `${day}/${month}/${year}`;
    }

    if (residentDateFormat === "YYYY/MM/DD") {
      return `${year}/${month}/${day}`;
    }

    return `${month}/${day}/${year}`;
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

      const titleCell = document.createElement("td");
      titleCell.textContent =
        "#" + report.report_id + " - " + (report.title || "Untitled Report");

      const categoryCell = document.createElement("td");
      categoryCell.textContent = report.category_name || "Uncategorized";

      const dateCell = document.createElement("td");
      dateCell.textContent = formatResidentDate(report.date_reported);

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

    const modal = bootstrap.Modal.getOrCreateInstance(residentModal);

    modal.show();

    const modalName = document.getElementById("residentModalName");

    const modalUsername = document.getElementById("residentModalUsername");

    const modalEmail = document.getElementById("residentModalEmail");

    const modalContact = document.getElementById("residentModalContact");

    const modalAddress = document.getElementById("residentModalAddress");

    const modalRegistered = document.getElementById("residentModalRegistered");

    const modalId = document.getElementById("residentModalId");

    const statTotal = document.getElementById("residentStatTotal");

    const statPending = document.getElementById("residentStatPending");

    const statProgress = document.getElementById("residentStatProgress");

    const statResolved = document.getElementById("residentStatResolved");

    if (modalName) {
      modalName.textContent = "Loading...";
    }

    if (modalUsername) {
      modalUsername.textContent = "—";
    }

    if (modalEmail) {
      modalEmail.textContent = "—";
    }

    if (modalContact) {
      modalContact.textContent = "—";
    }

    if (modalAddress) {
      modalAddress.textContent = "—";
    }

    if (modalRegistered) {
      modalRegistered.textContent = "—";
    }

    if (modalId) {
      modalId.textContent = "—";
    }

    if (statTotal) {
      statTotal.textContent = "0";
    }

    if (statPending) {
      statPending.textContent = "0";
    }

    if (statProgress) {
      statProgress.textContent = "0";
    }

    if (statResolved) {
      statResolved.textContent = "0";
    }

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

      residentDateFormat = result.date_format || "MM/DD/YYYY";

      if (!response.ok || !result.success) {
        throw new Error(result.message || "Unable to load resident details.");
      }

      const resident = result.resident || {};
      const stats = result.statistics || {};

      // ==========================================
      // ACCOUNT STATUS
      // ==========================================

      const statusBadge = document.getElementById("residentModalStatus");

      const statusForm = document.getElementById("residentStatusForm");

      const statusButton = document.getElementById("residentStatusButton");

      const statusButtonIcon = document.getElementById(
        "residentStatusButtonIcon",
      );

      const statusButtonText = document.getElementById(
        "residentStatusButtonText",
      );

      const residentResendOtpForm = document.getElementById(
        "residentResendOtpForm",
      );

      const residentDeletePendingForm = document.getElementById(
        "residentDeletePendingForm",
      );

      const residentResendOtpButton = document.getElementById(
        "residentResendOtpButton",
      );

      const isActive = Number(resident.is_active) === 1;

      const isEmailVerified = Boolean(resident.email_verified_at);

      const isPendingVerification = !isActive && !isEmailVerified;

      if (statusBadge) {
        if (isPendingVerification) {
          statusBadge.textContent = "Pending Verification";

          statusBadge.className = "badge bg-warning text-dark";
        } else if (isActive) {
          statusBadge.textContent = "Active";

          statusBadge.className = "badge bg-success";
        } else {
          statusBadge.textContent = "Inactive";

          statusBadge.className = "badge bg-secondary";
        }
      }

      if (statusForm) {
        statusForm.action = `/admin/residents/${resident.user_id}/status`;
      }

      if (residentResendOtpForm) {
        residentResendOtpForm.action = `/admin/residents/${resident.user_id}/resend-verification`;

        if (isPendingVerification) {
          residentResendOtpForm.classList.remove("d-none");
        } else {
          residentResendOtpForm.classList.add("d-none");
        }
      }

      if (residentDeletePendingForm) {
        residentDeletePendingForm.action = `/admin/residents/${resident.user_id}/delete-pending`;

        if (isPendingVerification) {
          residentDeletePendingForm.classList.remove("d-none");
        } else {
          residentDeletePendingForm.classList.add("d-none");
        }
      }

      if (residentResendOtpButton) {
        residentResendOtpButton.disabled = !isPendingVerification;
      }

      if (statusButton) {
        if (isPendingVerification) {
          statusButton.disabled = true;
          statusButton.className = "btn btn-secondary";
        } else {
          statusButton.disabled = false;

          statusButton.className = isActive
            ? "btn btn-danger"
            : "btn btn-success";
        }
      }

      if (statusButtonIcon) {
        statusButtonIcon.className = isPendingVerification
          ? "bi bi-hourglass-split me-1"
          : isActive
            ? "bi bi-person-dash-fill me-1"
            : "bi bi-person-check-fill me-1";
      }

      if (statusButtonText) {
        statusButtonText.textContent = isPendingVerification
          ? "Waiting for Verification"
          : isActive
            ? "Deactivate Account"
            : "Activate Account";
      }

      const modalImage = document.getElementById("residentModalImage");

      if (modalImage) {
        modalImage.src = resident.image_url || "";
      }

      if (modalId) {
        modalId.textContent = resident.resident_id || "N/A";
      }

      if (modalName) {
        modalName.textContent = resident.full_name || "N/A";
      }

      if (modalUsername) {
        modalUsername.textContent = resident.username || "N/A";
      }

      if (modalEmail) {
        modalEmail.textContent = resident.email || "N/A";
      }

      if (modalContact) {
        modalContact.textContent = resident.mobile_number || "N/A";
      }

      if (modalAddress) {
        modalAddress.textContent = resident.address || "No address provided";
      }

      if (modalRegistered) {
        modalRegistered.textContent = formatResidentDate(resident.created_at);
      }

      if (statTotal) {
        statTotal.textContent = stats.total ?? 0;
      }

      if (statPending) {
        statPending.textContent = stats.pending ?? 0;
      }

      if (statProgress) {
        statProgress.textContent = stats.in_progress ?? 0;
      }

      if (statResolved) {
        statResolved.textContent = stats.resolved ?? 0;
      }

      renderRecentReports(result.recent_reports || []);
    } catch (error) {
      console.error("Resident details error:", error);

      if (modalName) {
        modalName.textContent = "Unable to load resident";
      }

      if (recentReportsBody) {
        recentReportsBody.innerHTML =
          '<tr><td colspan="4" class="text-center text-danger">Unable to load resident information.</td></tr>';
      }
    }
  }

  // ==========================================
  // ROW CLICK → OPEN RESIDENT DETAILS
  // ==========================================

  residentRows.forEach((row) => {
    row.addEventListener("click", () => {
      loadResidentDetails(row);
    });
  });

  // ==========================================
  // AUTO-OPEN RESIDENT FROM URL
  // ==========================================

  const params = new URLSearchParams(window.location.search);

  const residentIdFromUrl = params.get("resident_id");

  if (residentIdFromUrl) {
    const targetRow = document.querySelector(
      `.resident-row[data-user-id="${residentIdFromUrl}"]`,
    );

    if (targetRow) {
      loadResidentDetails(targetRow);

      targetRow.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });

      targetRow.classList.add("table-active");

      const cleanUrl = new URL(window.location.href);

      cleanUrl.searchParams.delete("resident_id");

      window.history.replaceState(
        {},
        document.title,
        cleanUrl.pathname + cleanUrl.search + cleanUrl.hash,
      );
    }
  }

  // ==========================================
  // DELETE RESIDENT MODAL
  // ==========================================

  const deleteButtons = document.querySelectorAll("tbody .btn-danger");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.stopPropagation();

      const deleteModal = document.getElementById("deleteResidentModal");

      if (!deleteModal) {
        return;
      }

      const modal = bootstrap.Modal.getOrCreateInstance(deleteModal);

      modal.show();
    });
  });

  // ==========================================
  // SAVE CHANGES
  // ==========================================

  const saveButton = document.querySelector("#editResidentModal .btn-success");

  if (saveButton) {
    saveButton.addEventListener("click", () => {
      console.log("Resident save button clicked.");
    });
  }

  // ==========================================
  // DELETE CONFIRMATION
  // ==========================================

  const confirmDelete = document.getElementById("confirmDeletePendingResident");

  if (confirmDelete) {
    confirmDelete.addEventListener("click", () => {
      const deleteForm = document.getElementById("residentDeletePendingForm");

      if (!deleteForm || !deleteForm.action) {
        return;
      }

      confirmDelete.disabled = true;
      confirmDelete.textContent = "Deleting...";

      deleteForm.submit();
    });
  }

  // ==========================================
  // LOGOUT
  // ==========================================

  const logout = document.querySelector(".logout a");

  if (logout) {
    logout.addEventListener("click", function (event) {
      const answer = confirm("Are you sure you want to logout?");

      if (!answer) {
        event.preventDefault();
      }
    });
  }

  // ==========================================
  // ACTIVATE / DEACTIVATE
  // ==========================================

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

  // ==========================================
  // ADD RESIDENT MOBILE NUMBER
  // ==========================================

  const mobileNumberInput = document.getElementById("mobile_number");

  if (mobileNumberInput) {
    mobileNumberInput.addEventListener("input", () => {
      mobileNumberInput.value = mobileNumberInput.value
        .replace(/\D/g, "")
        .slice(0, 11);
    });
  }

  // ==========================================
  // ADD RESIDENT PUROK
  // ==========================================

  const addResidentPurok = document.getElementById("addResidentPurok");

  const addResidentPurokLabel = document.getElementById(
    "addResidentPurokLabel",
  );

  document.querySelectorAll(".add-resident-purok-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!addResidentPurok) {
        return;
      }

      addResidentPurok.value = this.dataset.value || "";

      if (addResidentPurokLabel) {
        addResidentPurokLabel.textContent = this.textContent.trim();
      }
    });
  });

  // ==========================================
  // ADD RESIDENT PASSWORD UX
  // ==========================================

  const residentPassword = document.getElementById("password");

  const residentConfirmPassword = document.getElementById("confirm_password");

  const toggleResidentPassword = document.getElementById(
    "toggleResidentPassword",
  );

  const toggleResidentConfirmPassword = document.getElementById(
    "toggleResidentConfirmPassword",
  );

  const residentPasswordMatch = document.getElementById(
    "residentPasswordMatch",
  );

  function togglePasswordVisibility(input, button) {
    if (!input || !button) {
      return;
    }

    const isHidden = input.type === "password";

    input.type = isHidden ? "text" : "password";

    const icon = button.querySelector("i");

    if (icon) {
      icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
    }

    button.setAttribute(
      "aria-label",
      isHidden ? "Hide password" : "Show password",
    );
  }

  if (toggleResidentPassword) {
    toggleResidentPassword.addEventListener("click", () => {
      togglePasswordVisibility(residentPassword, toggleResidentPassword);
    });
  }

  if (toggleResidentConfirmPassword) {
    toggleResidentConfirmPassword.addEventListener("click", () => {
      togglePasswordVisibility(
        residentConfirmPassword,
        toggleResidentConfirmPassword,
      );
    });
  }

  function checkResidentPasswordMatch() {
    if (
      !residentPassword ||
      !residentConfirmPassword ||
      !residentPasswordMatch
    ) {
      return;
    }

    residentConfirmPassword.classList.remove("is-valid", "is-invalid");

    residentConfirmPassword.setCustomValidity("");

    if (residentConfirmPassword.value === "") {
      residentPasswordMatch.textContent = "";

      residentPasswordMatch.className = "form-text";

      return;
    }

    if (residentPassword.value === residentConfirmPassword.value) {
      residentConfirmPassword.classList.add("is-valid");

      residentPasswordMatch.textContent = "Passwords match.";

      residentPasswordMatch.className = "form-text text-success";
    } else {
      residentConfirmPassword.classList.add("is-invalid");

      residentConfirmPassword.setCustomValidity("Passwords do not match.");

      residentPasswordMatch.textContent = "Passwords do not match.";

      residentPasswordMatch.className = "form-text text-danger";
    }
  }

  residentPassword?.addEventListener("input", checkResidentPasswordMatch);

  residentConfirmPassword?.addEventListener(
    "input",
    checkResidentPasswordMatch,
  );

  // ==========================================
  // PROFILE IMAGE PREVIEW
  // ==========================================

  const residentProfileInput = document.getElementById("profile_image");

  const residentProfilePreview = document.getElementById(
    "residentProfilePreview",
  );

  const residentProfilePreviewWrap = document.getElementById(
    "residentProfilePreviewWrap",
  );

  if (
    residentProfileInput &&
    residentProfilePreview &&
    residentProfilePreviewWrap
  ) {
    residentProfileInput.addEventListener("change", () => {
      const file = residentProfileInput.files?.[0];

      if (!file) {
        residentProfilePreview.src = "";

        residentProfilePreviewWrap.classList.add("d-none");

        return;
      }

      const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

      if (!allowedTypes.includes(file.type)) {
        residentProfileInput.value = "";
        residentProfilePreview.src = "";

        residentProfilePreviewWrap.classList.add("d-none");

        alert("Profile picture must be a JPG, PNG, or WebP image.");

        return;
      }

      const maxSize = 2 * 1024 * 1024;

      if (file.size > maxSize) {
        residentProfileInput.value = "";
        residentProfilePreview.src = "";

        residentProfilePreviewWrap.classList.add("d-none");

        alert("Profile picture must not exceed 2 MB.");

        return;
      }

      const reader = new FileReader();

      reader.onload = (event) => {
        residentProfilePreview.src = event.target.result;

        residentProfilePreviewWrap.classList.remove("d-none");
      };

      reader.readAsDataURL(file);
    });
  }

  // ==========================================
  // ADD RESIDENT SUBMIT
  // Prevent password mismatch + double submit
  // ==========================================

  const addResidentForm = document.getElementById("addResidentForm");

  const addResidentSubmitBtn = document.getElementById("addResidentSubmitBtn");

  const addResidentSubmitSpinner = document.getElementById(
    "addResidentSubmitSpinner",
  );

  const addResidentSubmitText = document.getElementById(
    "addResidentSubmitText",
  );

  if (addResidentForm) {
    addResidentForm.addEventListener("submit", (event) => {
      checkResidentPasswordMatch();

      // Password mismatch
      if (residentConfirmPassword && !residentConfirmPassword.checkValidity()) {
        event.preventDefault();
        residentConfirmPassword.reportValidity();
        return;
      }

      // Purok not selected
      if (addResidentPurok && addResidentPurok.value === "") {
        event.preventDefault();

        if (addResidentPurokLabel) {
          addResidentPurokLabel.textContent = "Please select a Purok";
        }

        return;
      }

      // Check all normal required HTML fields
      if (!addResidentForm.checkValidity()) {
        event.preventDefault();
        addResidentForm.reportValidity();
        return;
      }

      // For now, DO NOT disable the button.
      // Just show loading text.
      if (addResidentSubmitSpinner) {
        addResidentSubmitSpinner.classList.remove("d-none");
      }

      if (addResidentSubmitText) {
        addResidentSubmitText.textContent = "Creating Resident...";
      }
    });
  }
  // ==========================================
  // SUCCESS / ERROR CENTER POPUP
  // ==========================================

  const residentMessageModal = document.getElementById("residentMessageModal");

  if (residentMessageModal && window.bootstrap) {
    const messageModal = new bootstrap.Modal(residentMessageModal);

    messageModal.show();
  }
});
