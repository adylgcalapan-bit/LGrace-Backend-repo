window.openCategoryModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  modal.classList.add("show");
  modal.style.display = "block";
  modal.setAttribute("aria-hidden", "false");
  document.body.classList.add("modal-open");
  let backdrop = document.getElementById(`${modalId}-backdrop`);
  if (!backdrop) {
    backdrop = document.createElement("div");
    backdrop.className = "modal-backdrop fade show";
    backdrop.id = `${modalId}-backdrop`;
    document.body.appendChild(backdrop);
  }
};

window.closeCategoryModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  modal.classList.remove("show");
  modal.style.display = "none";
  modal.setAttribute("aria-hidden", "true");
  document.body.classList.remove("modal-open");
  const backdrop = document.getElementById(`${modalId}-backdrop`);
  if (backdrop) backdrop.remove();
};

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchCategory");
  const filterSelect = document.getElementById("filterStatus");
  // ==========================
  // CUSTOM STATUS DROPDOWN
  // ==========================

  document.querySelectorAll(".category-status-option").forEach((option) => {
    option.addEventListener("click", function () {
      if (!filterSelect) {
        return;
      }

      const selectedValue = this.dataset.value || "all";

      filterSelect.value = selectedValue;

      const label = document.getElementById("categoryStatusLabel");

      if (label) {
        label.textContent = this.textContent.trim();
      }

      applyFilters();
    });
  });

  const rows = Array.from(document.querySelectorAll("#categoryTableBody tr"));

  document
    .querySelectorAll('.btn-close, [data-bs-dismiss="modal"]')
    .forEach((button) => {
      button.addEventListener("click", () => {
        const modalId = button.closest(".modal")?.id;
        if (modalId) closeCategoryModal(modalId);
      });
    });

  document.querySelectorAll(".modal").forEach((modal) => {
    modal.addEventListener("click", (event) => {
      if (event.target === modal) closeCategoryModal(modal.id);
    });
  });

  const applyFilters = () => {
    const keyword = searchInput ? searchInput.value.toLowerCase().trim() : "";

    const status = filterSelect ? filterSelect.value.toLowerCase() : "all";

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      const rowStatus = (row.dataset.status || "").toLowerCase();

      const matchesSearch = text.includes(keyword);
      const matchesStatus = status === "all" || rowStatus === status;

      row.style.display = matchesSearch && matchesStatus ? "" : "none";
    });
  };

  if (searchInput) {
    searchInput.addEventListener("input", applyFilters);
  }

  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const categoryId = btn.dataset.categoryId;
      const categoryName = btn.dataset.categoryName || "";
      const description = btn.dataset.description || "";
      const status = btn.dataset.status || "1";

      const editForm = document.getElementById("editCategoryForm");
      const nameInput = document.getElementById("editCategoryName");
      const descriptionInput = document.getElementById(
        "editCategoryDescription",
      );
      const statusSelect = document.getElementById("editCategoryStatus");

      if (!editForm) {
        return;
      }

      editForm.action = `/admin/categories/update/${categoryId}`;

      if (nameInput) {
        nameInput.value = categoryName;
      }

      if (descriptionInput) {
        descriptionInput.value = description;
      }

      if (statusSelect) {
        statusSelect.value = status;
      }

      openCategoryModal("editCategoryModal");
    });
  });
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const categoryId = btn.dataset.categoryId;
      const categoryName = btn.dataset.categoryName || "";

      const deleteForm = document.getElementById("deleteCategoryForm");
      const deleteName = document.getElementById("deleteCategoryName");

      if (!deleteForm) return;

      deleteForm.action = `/admin/categories/delete/${categoryId}`;

      if (deleteName) {
        deleteName.textContent = categoryName;
      }

      openCategoryModal("deleteCategoryModal");
    });
  });
});
