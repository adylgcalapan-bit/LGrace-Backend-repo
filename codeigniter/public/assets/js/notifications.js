document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchNotification');
    const filterSelect = document.getElementById('filterNotification');
    const items = Array.from(document.querySelectorAll('.notification-item'));
    const badge = document.querySelector('.badge-count');

    const updateBadge = () => {
        const unread = items.filter(item => item.classList.contains('unread')).length;
        if (badge) badge.textContent = `${unread} Unread`;
    };

    const applyFilters = () => {
        const keyword = searchInput ? searchInput.value.toLowerCase() : '';
        const filterValue = filterSelect ? filterSelect.value : 'all';

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const isUnread = item.classList.contains('unread');
            const matchesFilter = filterValue === 'all' || (filterValue === 'unread' && isUnread) || (filterValue === 'read' && !isUnread);
            item.style.display = matchesFilter && text.includes(keyword) ? '' : 'none';
        });
    };

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterSelect) filterSelect.addEventListener('change', applyFilters);

    document.querySelectorAll('.read-btn').forEach(btn => btn.addEventListener('click', () => {
        const item = btn.closest('.notification-item');
        item.classList.remove('unread');
        updateBadge();
        btn.textContent = 'Read';
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-outline-secondary');
    }));

    document.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', () => {
        btn.closest('.notification-item').remove();
        updateBadge();
    }));

    document.getElementById('markAllBtn')?.addEventListener('click', () => {
        items.forEach(item => item.classList.remove('unread'));
        updateBadge();
        document.querySelectorAll('.read-btn').forEach(btn => {
            btn.textContent = 'Read';
            btn.classList.remove('btn-outline-success');
            btn.classList.add('btn-outline-secondary');
        });
    });

    updateBadge();
});
