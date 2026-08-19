document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("settingsForm");
    const resetBtn = document.getElementById("resetBtn");
    const saveBtn = document.getElementById("saveBtn");

    if (!form) {
        return;
    }

    // Confirm reset of unsaved changes
    resetBtn?.addEventListener("click", (event) => {
        const confirmed = window.confirm(
            "Discard all unsaved changes?"
        );

        if (!confirmed) {
            event.preventDefault();
        }
    });

    // Prevent accidental double-submit
    form.addEventListener("submit", () => {
        if (!saveBtn) {
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    });
});
