document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");

    if (!registerForm) return;

    const formMessage = document.getElementById("formMessage");

    const fieldMap = {
        fullName: {
            input: document.getElementById("fullName"),
            error: document.getElementById("fullNameError")
        },
        email: {
            input: document.getElementById("email"),
            error: document.getElementById("emailError")
        },
        mobileNumber: {
            input: document.getElementById("mobileNumber"),
            error: document.getElementById("mobileNumberError")
        },
        username: {
            input: document.getElementById("registerUsername"),
            error: document.getElementById("usernameError")
        },
        password: {
            input: document.getElementById("password"),
            error: document.getElementById("passwordError")
        },
        confirmPassword: {
            input: document.getElementById("confirmPassword"),
            error: document.getElementById("confirmPasswordError")
        },
        address: {
            input: document.getElementById("address"),
            error: document.getElementById("addressError")
        }
    };

    function setFieldState(field, message) {
        const { input, error } = field;
        input.classList.remove("is-valid", "is-invalid");

        if (message) {
            input.classList.add("is-invalid");
            error.textContent = message;
        } else if (input.value.trim()) {
            input.classList.add("is-valid");
            error.textContent = "";
        } else {
            error.textContent = "";
        }
    }

    function validateRegisterForm() {
        let isValid = true;

        const fullName = fieldMap.fullName.input.value.trim();
        const email = fieldMap.email.input.value.trim();
        const mobileNumber = fieldMap.mobileNumber.input.value.trim();
        const username = fieldMap.username.input.value.trim();
        const password = fieldMap.password.input.value.trim();
        const confirmPassword = fieldMap.confirmPassword.input.value.trim();
        const address = fieldMap.address.input.value.trim();

        if (!fullName) {
            setFieldState(fieldMap.fullName, "Please enter your full name.");
            isValid = false;
        } else {
            setFieldState(fieldMap.fullName, "");
        }

        if (!email) {
            setFieldState(fieldMap.email, "Please enter your email address.");
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setFieldState(fieldMap.email, "Please enter a valid email address.");
            isValid = false;
        } else {
            setFieldState(fieldMap.email, "");
        }

        if (!mobileNumber) {
            setFieldState(fieldMap.mobileNumber, "Please enter your mobile number.");
            isValid = false;
        } else if (!/^09\d{9}$/.test(mobileNumber)) {
            setFieldState(fieldMap.mobileNumber, "Use the format 09XXXXXXXXX.");
            isValid = false;
        } else {
            setFieldState(fieldMap.mobileNumber, "");
        }

        if (!username) {
            setFieldState(fieldMap.username, "Please enter a username.");
            isValid = false;
        } else if (username.length < 4) {
            setFieldState(fieldMap.username, "Username must be at least 4 characters.");
            isValid = false;
        } else {
            setFieldState(fieldMap.username, "");
        }

        if (!password) {
            setFieldState(fieldMap.password, "Please enter a password.");
            isValid = false;
        } else if (password.length < 8) {
            setFieldState(fieldMap.password, "Password must be at least 8 characters.");
            isValid = false;
        } else {
            setFieldState(fieldMap.password, "");
        }

        if (!confirmPassword) {
            setFieldState(fieldMap.confirmPassword, "Please confirm your password.");
            isValid = false;
        } else if (password && confirmPassword !== password) {
            setFieldState(fieldMap.confirmPassword, "Passwords do not match.");
            isValid = false;
        } else {
            setFieldState(fieldMap.confirmPassword, "");
        }

        if (!address) {
            setFieldState(fieldMap.address, "Please enter your address.");
            isValid = false;
        } else {
            setFieldState(fieldMap.address, "");
        }

        return isValid;
    }

    Object.values(fieldMap).forEach(function (field) {
        field.input.addEventListener("input", validateRegisterForm);
        field.input.addEventListener("blur", validateRegisterForm);
    });

    registerForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!validateRegisterForm()) {
            formMessage.textContent = "Please correct the highlighted fields.";
            formMessage.className = "form-message error";
            return;
        }

        formMessage.textContent = "Account created successfully! You can now log in.";
        formMessage.className = "form-message success";

        window.setTimeout(function () {
            window.location.href = "login.html";
        }, 900);
    });
});
