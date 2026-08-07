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

            tableRows.forEach(row => {
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
    viewButtons.forEach(button => {
        button.addEventListener("click", function () {
            const modal = new bootstrap.Modal(document.getElementById("reportModal"));
            modal.show();
        });
    });

    const reportRows = document.querySelectorAll(".report-row");
    const reportModal = document.getElementById("reportModal");

    reportRows.forEach(row => {
        row.addEventListener("click", function (event) {
            if (event.target.closest("button") || event.target.closest("select")) return;

            const modal = new bootstrap.Modal(reportModal);
            const reportId = this.getAttribute("data-id");
            const resident = this.getAttribute("data-resident");
            const category = this.getAttribute("data-category");
            const location = this.getAttribute("data-location");
            const date = this.getAttribute("data-date");
            const status = this.getAttribute("data-status");
            const priority = this.getAttribute("data-priority");
            const description = this.getAttribute("data-description");

            if (reportModal) {
                reportModal.querySelector(".modal-title").textContent = `Report Details - ${reportId}`;
                reportModal.querySelector("#reportId").textContent = reportId;
                reportModal.querySelector("#reportResident").textContent = resident;
                reportModal.querySelector("#reportCategory").textContent = category;
                reportModal.querySelector("#reportStatus").textContent = status;
                reportModal.querySelector("#reportLocation").textContent = location;
                reportModal.querySelector("#reportDate").textContent = date;
                reportModal.querySelector("#reportPriority").textContent = priority;
                reportModal.querySelector("#reportDescription").textContent = description;
            }

            modal.show();
        });
    });

    const tableBody = document.querySelector("tbody");
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
                    actionCell.innerHTML = '<div class="action-buttons"><button class="btn btn-sm btn-success resolve-btn">Resolve</button></div>';
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
                    actionCell.innerHTML = '<span class="text-muted small">Declined</span>';
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
                    actionCell.innerHTML = '<span class="text-success small">Completed</span>';
                }
            }
        });
    }

    tableRows.forEach(row => {
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
    pages.forEach(page => {
        page.addEventListener("click", function (e) {
            e.preventDefault();

            pages.forEach(item => {
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