/*
=========================================
My Reports
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("reportSearch");
  const statusFilter = document.getElementById("statusFilter");

  function applyFilters() {
    const searchText = searchInput
      ? searchInput.value.trim().toLowerCase()
      : "";

    const selectedStatus = statusFilter
      ? statusFilter.value.trim().toLowerCase()
      : "";

    /* =========================
       DESKTOP / TABLET
    ========================= */

    const tableRows = document.querySelectorAll(
      ".reports-card tbody tr[data-status]",
    );

    tableRows.forEach(function (row) {
      const titleCell = row.querySelector("td:nth-child(1)");
      const categoryCell = row.querySelector("td:nth-child(2)");

      const title = titleCell ? titleCell.textContent.trim().toLowerCase() : "";

      const category = categoryCell
        ? categoryCell.textContent.trim().toLowerCase()
        : "";

      const status = (row.dataset.status || "").trim().toLowerCase();

      /* Search both Title and Category */
      const searchableText = title + " " + category;

      const matchesSearch =
        searchText === "" || searchableText.includes(searchText);

      const matchesStatus = selectedStatus === "" || status === selectedStatus;

      row.style.display = matchesSearch && matchesStatus ? "" : "none";
    });

    /* =========================
       MOBILE
    ========================= */

    const mobileReports = document.querySelectorAll(
      ".mobile-report-item[data-status]",
    );

    mobileReports.forEach(function (card) {
      const titleElement = card.querySelector(".mobile-report-top h5");

      const categoryElement = card.querySelector(
        ".mobile-report-meta div:first-child span",
      );

      const title = titleElement
        ? titleElement.textContent.trim().toLowerCase()
        : "";

      const category = categoryElement
        ? categoryElement.textContent.trim().toLowerCase()
        : "";

      const status = (card.dataset.status || "").trim().toLowerCase();

      /* Search both Title and Category */
      const searchableText = title + " " + category;

      const matchesSearch =
        searchText === "" || searchableText.includes(searchText);

      const matchesStatus = selectedStatus === "" || status === selectedStatus;

      card.style.display = matchesSearch && matchesStatus ? "" : "none";
    });
  }

  /* Search while typing */
  if (searchInput) {
    searchInput.addEventListener("input", applyFilters);
  }

  /* Filter when status changes */
  if (statusFilter) {
    statusFilter.addEventListener("change", applyFilters);
  }

  /* Run once when page loads */
  applyFilters();
});
