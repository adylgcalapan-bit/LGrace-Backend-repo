document.addEventListener('DOMContentLoaded', () => {
    const photoInput = document.getElementById('photoInput');
    const preview = document.getElementById('profilePreview');
    const uploadBtn = document.getElementById('uploadBtn');
    const removeBtn = document.getElementById('removeBtn');
    const saveBtn = document.getElementById('saveProfileBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    uploadBtn?.addEventListener('click', () => photoInput?.click());

    photoInput?.addEventListener('change', () => {
        const file = photoInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
    });

    removeBtn?.addEventListener('click', () => {
        preview.src = 'https://i.pravatar.cc/180?img=12';
        photoInput.value = '';
    });

    saveBtn?.addEventListener('click', () => {
        alert('Profile updated successfully.');
    });

    cancelBtn?.addEventListener('click', () => {
        window.location.reload();
    });
});
