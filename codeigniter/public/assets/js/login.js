document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");

    if (!loginForm) return;

    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");

    function setFieldState(input, errorElement, message) {
        input.classList.remove("is-valid", "is-invalid");

        if (message) {
            input.classList.add("is-invalid");
            errorElement.textContent = message;
        } else if (input.value.trim()) {
            input.classList.add("is-valid");
            errorElement.textContent = "";
        } else {
            errorElement.textContent = "";
        }
    }

    function validateLoginForm() {
        let isValid = true;
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        if (!username) {
            setFieldState(usernameInput, usernameError, "Please enter your username.");
            isValid = false;
        } else {
            setFieldState(usernameInput, usernameError, "");
        }

        if (!password) {
            setFieldState(passwordInput, passwordError, "Please enter your password.");
            isValid = false;
        } else if (password.length < 6) {
            setFieldState(passwordInput, passwordError, "Password must be at least 6 characters.");
            isValid = false;
        } else {
            setFieldState(passwordInput, passwordError, "");
        }

        return isValid;
    }

    [usernameInput, passwordInput].forEach(function (input) {
        input.addEventListener("input", function () {
            if (input === usernameInput) {
                setFieldState(usernameInput, usernameError, "");
            } else {
                setFieldState(passwordInput, passwordError, "");
            }
        });

        input.addEventListener("blur", validateLoginForm);
    });

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!validateLoginForm()) {
            return;
        }

        const username = usernameInput.value.trim().toLowerCase();
        const isAdminLogin = username === "admin" || username === "barangay";

        if (isAdminLogin) {
            window.location.href = "/admin/dashboard";
        } else {
            window.location.href = "/resident/dashboard";
        }
    });
});
