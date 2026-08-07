window.openCategoryModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('show');
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    let backdrop = document.getElementById(`${modalId}-backdrop`);
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = `${modalId}-backdrop`;
        document.body.appendChild(backdrop);
    }
};

window.closeCategoryModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    const backdrop = document.getElementById(`${modalId}-backdrop`);
    if (backdrop) backdrop.remove();
};

window.saveCategory = function() {
    alert('Category saved successfully.');
    closeCategoryModal('addCategoryModal');
    closeCategoryModal('editCategoryModal');
};

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchCategory');
    const filterSelect = document.getElementById('filterStatus');
    const filterBtn = document.getElementById('filterBtn');
    const rows = Array.from(document.querySelectorAll('#categoryTableBody tr'));

    document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.closest('.modal')?.id;
            if (modalId) closeCategoryModal(modalId);
        });
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeCategoryModal(modal.id);
        });
    });

    const applyFilters = () => {
        const keyword = searchInput ? searchInput.value.toLowerCase() : '';
        const status = filterSelect ? filterSelect.value : 'all';

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const hasStatus = status === 'all' || text.includes(status);
            row.style.display = text.includes(keyword) && hasStatus ? '' : 'none';
        });
    };

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterSelect) filterSelect.addEventListener('change', applyFilters);
    if (filterBtn) filterBtn.addEventListener('click', applyFilters);

    document.querySelectorAll('.view-btn').forEach(btn => btn.addEventListener('click', () => {
        alert('Category details preview opened.');
    }));

    document.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', () => {
        openCategoryModal('editCategoryModal');
    }));

    document.querySelectorAll('.toggle-btn').forEach(btn => btn.addEventListener('click', () => {
        const badge = btn.closest('tr').querySelector('.badge');
        if (badge) {
            badge.classList.toggle('bg-success');
            badge.classList.toggle('bg-secondary');
            badge.textContent = badge.textContent === 'Active' ? 'Inactive' : 'Active';
        }
    }));

    document.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', () => {
        openCategoryModal('deleteCategoryModal');
    }));

    const saveBtn = document.getElementById('saveCategoryBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            saveCategory();
        });
    }
});
