document.addEventListener("DOMContentLoaded", () => {
    const profileForm = document.getElementById("profileForm");
    const profileImage = document.getElementById("profileImage");

    if (!profileForm) {
        return;
    }

    profileForm.addEventListener("reset", (event) => {
        const confirmed = window.confirm(
            "Discard all unsaved profile changes?"
        );

        if (!confirmed) {
            event.preventDefault();
        }
    });

    if (profileImage) {
        profileImage.addEventListener("change", () => {
            const file = profileImage.files?.[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp",
            ];

            const maxSize = 5 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                alert("Profile picture must be JPG, PNG, or WebP.");
                profileImage.value = "";
                return;
            }

            if (file.size > maxSize) {
                alert("Profile picture must not exceed 5 MB.");
                profileImage.value = "";
            }
        });
    }
});