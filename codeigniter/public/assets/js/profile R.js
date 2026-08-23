document.addEventListener("DOMContentLoaded", function () {

    // =========================================
    // ELEMENTS
    // =========================================

    const profileForm =
        document.getElementById("profileForm");

    const profileImageInput =
        document.getElementById("profileImage");

    const profilePhotoPreview =
        document.getElementById("profilePhotoPreview");

    const summaryAvatar =
        document.querySelector(".summary-avatar");

    const residentSidebar =
        document.getElementById("residentSidebar");

    const mobileSidebarButton =
        document.getElementById("mobileSidebarButton");

    const sidebarOverlay =
        document.getElementById("sidebarOverlay");


    // =========================================
    // MOBILE SIDEBAR
    // =========================================

    function openSidebar() {

        if (residentSidebar) {
            residentSidebar.classList.add("show");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.add("show");
        }

        if (mobileSidebarButton) {
            mobileSidebarButton.setAttribute(
                "aria-expanded",
                "true"
            );
        }

        document.body.classList.add(
            "sidebar-open"
        );
    }


    function closeSidebar() {

        if (residentSidebar) {
            residentSidebar.classList.remove("show");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("show");
        }

        if (mobileSidebarButton) {
            mobileSidebarButton.setAttribute(
                "aria-expanded",
                "false"
            );
        }

        document.body.classList.remove(
            "sidebar-open"
        );
    }


    if (mobileSidebarButton) {

        mobileSidebarButton.addEventListener(
            "click",
            function () {

                const isOpen =
                    residentSidebar &&
                    residentSidebar.classList.contains(
                        "show"
                    );

                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }

            }
        );
    }


    if (sidebarOverlay) {

        sidebarOverlay.addEventListener(
            "click",
            closeSidebar
        );
    }


    // Close sidebar after clicking a menu item
    // on tablet / mobile.
    if (residentSidebar) {

        const sidebarLinks =
            residentSidebar.querySelectorAll(
                "a"
            );

        sidebarLinks.forEach(function (link) {

            link.addEventListener(
                "click",
                function () {

                    if (
                        window.innerWidth < 992
                    ) {
                        closeSidebar();
                    }

                }
            );

        });
    }


    // ESC closes mobile sidebar.
    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Escape") {
                closeSidebar();
            }

        }
    );


    // Return to normal desktop layout.
    window.addEventListener(
        "resize",
        function () {

            if (window.innerWidth >= 992) {
                closeSidebar();
            }

        }
    );


    // =========================================
    // PROFILE PHOTO PREVIEW
    // =========================================

    if (profileImageInput) {

        profileImageInput.addEventListener(
            "change",
            function () {

                const file =
                    profileImageInput.files &&
                    profileImageInput.files[0];

                if (!file) {
                    return;
                }


                // Allowed image types
                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    window.alert(
                        "Please select a JPG, PNG, or WebP image."
                    );

                    profileImageInput.value = "";

                    return;
                }


                // Maximum file size = 5 MB
                const maximumFileSize =
                    5 * 1024 * 1024;


                if (
                    file.size >
                    maximumFileSize
                ) {

                    window.alert(
                        "Profile picture must not exceed 5 MB."
                    );

                    profileImageInput.value = "";

                    return;
                }


                // Preview selected photo.
                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        const previewUrl =
                            event.target.result;


                        if (
                            profilePhotoPreview
                        ) {
                            profilePhotoPreview.src =
                                previewUrl;
                        }


                        // Also update the large
                        // profile summary image.
                        if (summaryAvatar) {
                            summaryAvatar.src =
                                previewUrl;
                        }

                    };


                reader.readAsDataURL(file);

            }
        );
    }


    // =========================================
    // PASSWORD SHOW / HIDE
    // =========================================

    const passwordToggleButtons =
        document.querySelectorAll(
            ".password-toggle"
        );


    passwordToggleButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    const targetId =
                        button.getAttribute(
                            "data-target"
                        );


                    if (!targetId) {
                        return;
                    }


                    const passwordInput =
                        document.getElementById(
                            targetId
                        );


                    if (!passwordInput) {
                        return;
                    }


                    const icon =
                        button.querySelector("i");


                    if (
                        passwordInput.type ===
                        "password"
                    ) {

                        passwordInput.type =
                            "text";

                        button.setAttribute(
                            "aria-label",
                            "Hide password"
                        );


                        if (icon) {
                            icon.className =
                                "bi bi-eye-slash";
                        }

                    } else {

                        passwordInput.type =
                            "password";

                        button.setAttribute(
                            "aria-label",
                            "Show password"
                        );


                        if (icon) {
                            icon.className =
                                "bi bi-eye";
                        }

                    }

                }
            );

        }
    );


    // =========================================
    // PASSWORD MATCH CHECK
    // =========================================

    const newPassword =
        document.getElementById(
            "newPassword"
        );

    const confirmPassword =
        document.getElementById(
            "confirmPassword"
        );


    function validatePasswords() {

        if (
            !newPassword ||
            !confirmPassword
        ) {
            return true;
        }


        // No password change requested.
        if (
            newPassword.value === "" &&
            confirmPassword.value === ""
        ) {

            confirmPassword.setCustomValidity(
                ""
            );

            return true;
        }


        if (
            newPassword.value !==
            confirmPassword.value
        ) {

            confirmPassword.setCustomValidity(
                "Passwords do not match."
            );

            return false;
        }


        confirmPassword.setCustomValidity(
            ""
        );

        return true;
    }


    if (newPassword) {

        newPassword.addEventListener(
            "input",
            validatePasswords
        );
    }


    if (confirmPassword) {

        confirmPassword.addEventListener(
            "input",
            validatePasswords
        );
    }


    // =========================================
    // RESET PROFILE FORM
    // =========================================

    if (profileForm) {

        profileForm.addEventListener(
            "reset",
            function (event) {

                const confirmed =
                    window.confirm(
                        "Discard all unsaved profile changes?"
                    );


                if (!confirmed) {

                    event.preventDefault();

                    return;
                }


                // After browser resets the form,
                // return preview to original image.
                window.setTimeout(
                    function () {

                        if (
                            profilePhotoPreview
                        ) {

                            const originalSrc =
                                profilePhotoPreview.getAttribute(
                                    "data-original-src"
                                );

                            if (originalSrc) {
                                profilePhotoPreview.src =
                                    originalSrc;
                            }

                        }

                    },
                    0
                );

            }
        );
    }


    // =========================================
    // SAVE ORIGINAL PROFILE IMAGE
    // =========================================

    if (profilePhotoPreview) {

        profilePhotoPreview.setAttribute(
            "data-original-src",
            profilePhotoPreview.src
        );
    }


    if (summaryAvatar) {

        summaryAvatar.setAttribute(
            "data-original-src",
            summaryAvatar.src
        );
    }


    // =========================================
    // FORM SUBMIT VALIDATION
    // =========================================

    if (profileForm) {

        profileForm.addEventListener(
            "submit",
            function (event) {

                if (!validatePasswords()) {

                    event.preventDefault();

                    if (confirmPassword) {

                        confirmPassword.reportValidity();

                        confirmPassword.focus();
                    }

                }

            }
        );
    }

});