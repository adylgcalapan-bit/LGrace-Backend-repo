document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveBtn');
    const resetBtn = document.getElementById('resetBtn');

    saveBtn?.addEventListener('click', () => {
        const message = document.createElement('div');
        message.className = 'alert alert-success mt-3';
        message.textContent = 'Settings saved successfully.';
        document.querySelector('.main-content').insertBefore(message, document.querySelector('.main-content').firstChild);
    });

    resetBtn?.addEventListener('click', () => {
        document.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.tagName === 'INPUT' && field.type === 'checkbox') return;
            field.value = field.defaultValue || '';
        });
        alert('Changes reset to default values.');
    });
});
