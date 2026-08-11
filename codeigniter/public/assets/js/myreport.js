/*
=========================================
My Reports
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {
  console.log("My Reports Page Loaded");

  const searchInput = document.querySelector(".search-card .form-control");
  const statusFilter = document.querySelector(".search-card .form-select");
  const tableRows = document.querySelectorAll(".reports-card tbody tr");

  function filterReports() {
    if (!searchInput || !statusFilter || tableRows.length === 0) return;

    const searchText = searchInput.value.trim().toLowerCase();
    const selectedStatus = statusFilter.value.trim().toLowerCase();

    tableRows.forEach((row) => {
      const title = row.cells[0].textContent.toLowerCase();
      const status = row.cells[3].textContent.toLowerCase();

      const matchesSearch = title.includes(searchText);
      const matchesStatus =
        selectedStatus === "all status" || status.includes(selectedStatus);

      row.style.display = matchesSearch && matchesStatus ? "" : "none";
    });
  }

  if (searchInput) {
    searchInput.addEventListener("keyup", filterReports);
  }

  if (statusFilter) {
    statusFilter.addEventListener("change", filterReports);
  }
});
