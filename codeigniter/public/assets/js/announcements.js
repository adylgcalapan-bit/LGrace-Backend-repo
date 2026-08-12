document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchAnnouncement");
  const params = new URLSearchParams(window.location.search);
  const shouldOpenAddModal = params.get("open") === "add";
  const filterSelect = document.getElementById("filterAnnouncement");
  const tbody = document.getElementById("announcementTableBody");
  const addForm = document.getElementById("addAnnouncementForm");
  const editForm = document.getElementById("editAnnouncementForm");
  const viewContent = document.getElementById("viewAnnouncementContent");
  const totalAnnouncements = document.getElementById("totalAnnouncements");
  const publishedAnnouncements = document.getElementById(
    "publishedAnnouncements",
  );
  const draftAnnouncements = document.getElementById("draftAnnouncements");
  const deleteModal = document.getElementById("deleteAnnouncementModal");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  let currentRow = null;

  const rows = () => Array.from(tbody?.querySelectorAll("tr[data-id]") || []);

  const updateSummary = () => {
    const allRows = rows();
    const published = allRows.filter(
      (row) => row.dataset.status === "Published",
    ).length;
    const draft = allRows.filter(
      (row) => row.dataset.status === "Draft",
    ).length;
    if (totalAnnouncements) totalAnnouncements.textContent = allRows.length;
    if (publishedAnnouncements) publishedAnnouncements.textContent = published;
    if (draftAnnouncements) draftAnnouncements.textContent = draft;
  };

  const applyFilters = () => {
    const keyword = searchInput ? searchInput.value.toLowerCase() : "";
    const filterValue = filterSelect ? filterSelect.value : "all";

    rows().forEach((row) => {
      const text = row.textContent.toLowerCase();
      const matchesFilter =
        filterValue === "all" ||
        row.dataset.status?.toLowerCase() === filterValue;
      row.style.display = matchesFilter && text.includes(keyword) ? "" : "none";
    });
  };

  const attachRowEvents = () => {
    rows().forEach((row) => {
      row.querySelector(".view-btn")?.addEventListener("click", () => {
        const modal = new bootstrap.Modal(
          document.getElementById("viewAnnouncementModal"),
        );
        viewContent.innerHTML = `<h5>${row.dataset.title}</h5><p>${row.dataset.content}</p><hr><p class="text-muted mb-0"><strong>Category:</strong> ${row.dataset.category}<br><strong>Status:</strong> ${row.dataset.status}<br><strong>Published:</strong> ${row.dataset.publishDate}</p>`;
        modal.show();
      });

      row.querySelector(".edit-btn")?.addEventListener("click", () => {
        currentRow = row;
        const modal = new bootstrap.Modal(
          document.getElementById("editAnnouncementModal"),
        );
        const form = document.getElementById("editAnnouncementForm");
        form.title.value = row.dataset.title;
        form.content.value = row.dataset.content;
        form.category.value = row.dataset.category;
        form.publishDate.value = row.dataset.publishDate;
        form.status.value = row.dataset.status;
        modal.show();
      });

      row.querySelector(".delete-btn")?.addEventListener("click", () => {
        currentRow = row;
        const modal = new bootstrap.Modal(deleteModal);
        modal.show();
      });
    });
  };

  editForm?.addEventListener("submit", (event) => {
    event.preventDefault();
    if (!currentRow) return;
    const formData = new FormData(editForm);
    currentRow.dataset.title = formData.get("title");
    currentRow.dataset.content = formData.get("content");
    currentRow.dataset.category = formData.get("category") || "General";
    currentRow.dataset.publishDate = formData.get("publishDate") || "";
    currentRow.dataset.status = formData.get("status") || "Published";
    currentRow.children[0].textContent = formData.get("title");
    currentRow.children[1].textContent = formData.get("content");
    currentRow.children[3].textContent = formData.get("publishDate") || "";
    const badge = currentRow.querySelector(".badge");
    const status = formData.get("status") || "Published";
    badge.className = `badge ${status === "Published" ? "bg-success" : status === "Draft" ? "bg-warning text-dark" : "bg-secondary"}`;
    badge.textContent = status;
    updateSummary();
    bootstrap.Modal.getInstance(
      document.getElementById("editAnnouncementModal"),
    )?.hide();
    alert("Announcement updated.");
  });

  confirmDeleteBtn?.addEventListener("click", () => {
    if (currentRow) {
      currentRow.remove();
      updateSummary();
      bootstrap.Modal.getInstance(deleteModal)?.hide();
      alert("Announcement deleted.");
    }
  });

  searchInput?.addEventListener("input", applyFilters);
  filterSelect?.addEventListener("change", applyFilters);
  document.getElementById("filterBtn")?.addEventListener("click", applyFilters);

  if (shouldOpenAddModal) {
    const modal = new bootstrap.Modal(
      document.getElementById("addAnnouncementModal"),
    );
    modal.show();
  }

  updateSummary();
  attachRowEvents();
});
