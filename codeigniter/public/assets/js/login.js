document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  if (!loginForm) return;

  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");

  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");

  // ============================
  // SHOW ERROR
  // ============================

  function showError(input, errorElement, message) {
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");

    errorElement.textContent = message;
  }

  // ============================
  // CLEAR ERROR
  // ============================

  function clearError(input, errorElement) {
    input.classList.remove("is-invalid", "is-valid");
    errorElement.textContent = "";
  }

  // ============================
  // CLEAR ERROR WHILE TYPING
  // ============================

  emailInput.addEventListener("input", function () {
    clearError(emailInput, emailError);
  });

  passwordInput.addEventListener("input", function () {
    clearError(passwordInput, passwordError);
  });

  // ============================
  // VALIDATE ONLY ON LOGIN
  // ============================

  loginForm.addEventListener("submit", function (event) {
    let isValid = true;

    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    // EMAIL
    if (email === "") {
      showError(emailInput, emailError, "Please enter your email.");

      isValid = false;
    }

    // PASSWORD
    if (password === "") {
      showError(passwordInput, passwordError, "Please enter your password.");

      isValid = false;
    }

    // Stop only when fields are invalid
    if (!isValid) {
      event.preventDefault();
    }

    // If valid:
    // DO NOT preventDefault()
    // Real CodeIgniter login will continue normally.
  });
});
