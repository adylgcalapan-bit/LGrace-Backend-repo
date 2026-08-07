document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveChangesBtn');
    const changeBtn = document.getElementById('changePasswordBtn');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const profileImageInput = document.getElementById('profileImageInput');
    const profilePreview = document.getElementById('profilePreview');
    const topProfileImage = document.getElementById('topProfileImage');
    const currentPassword = document.getElementById('currentPassword');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    const showMessage = (message, type = 'success') => {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} mt-3`;
        alert.textContent = message;
        document.querySelector('.main-content').insertBefore(alert, document.querySelector('.main-content').firstChild);
    };

    uploadPhotoBtn?.addEventListener('click', () => {
        const file = profileImageInput?.files?.[0];
        if (!file) {
            showMessage('Please choose an image first.', 'danger');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            const imageData = event.target.result;
            if (profilePreview) profilePreview.src = imageData;
            if (topProfileImage) topProfileImage.src = imageData;
            showMessage('Profile photo updated successfully.');
        };
        reader.readAsDataURL(file);
    });

    saveBtn?.addEventListener('click', () => {
        const requiredFields = document.querySelectorAll('.card input[type="text"], .card input[type="email"]');
        let valid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim()) valid = false;
        });
        if (!valid) {
            showMessage('Please complete all required fields.', 'danger');
            return;
        }
        showMessage('Account details updated successfully.');
    });

    changeBtn?.addEventListener('click', () => {
        if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
            showMessage('Please fill in all password fields.', 'danger');
            return;
        }
        if (newPassword.value !== confirmPassword.value) {
            showMessage('Passwords do not match.', 'danger');
            return;
        }
        showMessage('Password changed successfully.');
    });
});
