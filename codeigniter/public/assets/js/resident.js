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

            tableRows.forEach(row => {

                const text = row.textContent.toLowerCase();

                row.style.display = text.includes(value) ? "" : "none";

            });

        });

    }

    // ==========================
    // VIEW RESIDENT
    // ==========================

    const viewButtons = document.querySelectorAll("tbody .btn-primary");
    const residentRows = document.querySelectorAll(".resident-row");
    const residentModal = document.getElementById("residentModal");

    viewButtons.forEach(button => {

        button.addEventListener("click", (event) => {
            event.stopPropagation();
            const row = button.closest("tr");
            const modal = new bootstrap.Modal(residentModal);

            if (row && residentModal) {
                residentModal.querySelector("#residentModalId").textContent = row.getAttribute("data-id") || "";
                residentModal.querySelector("#residentModalName").textContent = row.getAttribute("data-name") || "";
                residentModal.querySelector("#residentModalEmail").textContent = row.getAttribute("data-email") || "";
                residentModal.querySelector("#residentModalContact").textContent = row.getAttribute("data-contact") || "";
                residentModal.querySelector("#residentModalAddress").textContent = row.getAttribute("data-address") || "";
                residentModal.querySelector("#residentModalStatus").textContent = row.getAttribute("data-status") || "";
                residentModal.querySelector("#residentModalImage").src = row.getAttribute("data-image") || "";
            }

            modal.show();

        });

    });

    residentRows.forEach(row => {
        row.addEventListener("click", (event) => {
            if (event.target.closest("button")) return;

            const modal = new bootstrap.Modal(residentModal);

            if (residentModal) {
                residentModal.querySelector("#residentModalId").textContent = row.getAttribute("data-id") || "";
                residentModal.querySelector("#residentModalName").textContent = row.getAttribute("data-name") || "";
                residentModal.querySelector("#residentModalEmail").textContent = row.getAttribute("data-email") || "";
                residentModal.querySelector("#residentModalContact").textContent = row.getAttribute("data-contact") || "";
                residentModal.querySelector("#residentModalAddress").textContent = row.getAttribute("data-address") || "";
                residentModal.querySelector("#residentModalStatus").textContent = row.getAttribute("data-status") || "";
                residentModal.querySelector("#residentModalImage").src = row.getAttribute("data-image") || "";
            }

            modal.show();
        });
    });

    // ==========================
    // EDIT RESIDENT
    // ==========================

    const editButtons = document.querySelectorAll("tbody .btn-warning");

    editButtons.forEach(button => {

        button.addEventListener("click", () => {

            const modal = new bootstrap.Modal(
                document.getElementById("editResidentModal")
            );

            modal.show();

        });

    });

    // ==========================
    // DELETE RESIDENT
    // ==========================

    const deleteButtons = document.querySelectorAll("tbody .btn-danger");

    deleteButtons.forEach(button => {

        button.addEventListener("click", () => {

            const modal = new bootstrap.Modal(
                document.getElementById("deleteResidentModal")
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

    const confirmDelete = document.querySelector("#deleteResidentModal .btn-danger");

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

    pageLinks.forEach(link => {

        link.addEventListener("click", function (e) {

            e.preventDefault();

            pageLinks.forEach(item => {

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
