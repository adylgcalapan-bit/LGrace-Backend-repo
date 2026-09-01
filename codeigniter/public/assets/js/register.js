document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");

  if (!registerForm) return;

  const formMessage = document.getElementById("formMessage");

  const sendVerificationCodeBtn = document.getElementById(
    "sendVerificationCodeBtn",
  );

  const verifyEmailCodeBtn = document.getElementById("verifyEmailCodeBtn");

  const verificationCodeSection = document.getElementById(
    "verificationCodeSection",
  );

  const verificationCode = document.getElementById("verificationCode");

  const verificationCodeMessage = document.getElementById(
    "verificationCodeMessage",
  );

  const emailVerificationStatus = document.getElementById(
    "emailVerificationStatus",
  );

  const csrfInput = registerForm.querySelector('input[type="hidden"]');

  let verifiedEmail = "";
  let resendTimer = null;

  const fieldMap = {
    fullName: {
      input: document.getElementById("fullName"),
      error: document.getElementById("fullNameError"),
    },
    email: {
      input: document.getElementById("email"),
      error: document.getElementById("emailError"),
    },
    mobileNumber: {
      input: document.getElementById("mobileNumber"),
      error: document.getElementById("mobileNumberError"),
    },
    username: {
      input: document.getElementById("registerUsername"),
      error: document.getElementById("usernameError"),
    },
    password: {
      input: document.getElementById("password"),
      error: document.getElementById("passwordError"),
    },
    confirmPassword: {
      input: document.getElementById("confirmPassword"),
      error: document.getElementById("confirmPasswordError"),
    },
    address: {
      input: document.getElementById("address"),
      error: document.getElementById("addressError"),
    },
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

  function updateCsrfToken(newHash) {
    if (csrfInput && newHash) {
      csrfInput.value = newHash;
    }
  }

  function resetEmailVerification() {
    verifiedEmail = "";

    if (emailVerificationStatus) {
      emailVerificationStatus.textContent = "Email not verified";
      emailVerificationStatus.className = "align-self-center small text-muted";
    }

    if (verificationCode) {
      verificationCode.value = "";
    }

    if (verificationCodeMessage) {
      verificationCodeMessage.textContent = "";
      verificationCodeMessage.className = "small mt-2";
    }
  }

  function setVerificationMessage(message, type) {
    if (!verificationCodeMessage) return;

    verificationCodeMessage.textContent = message;

    verificationCodeMessage.className =
      type === "success" ? "small mt-2 text-success" : "small mt-2 text-danger";
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
      setFieldState(
        fieldMap.username,
        "Username must be at least 4 characters.",
      );
      isValid = false;
    } else {
      setFieldState(fieldMap.username, "");
    }

    if (!password) {
      setFieldState(fieldMap.password, "Please enter a password.");
      isValid = false;
    } else if (password.length < 8) {
      setFieldState(
        fieldMap.password,
        "Password must be at least 8 characters.",
      );
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

  sendVerificationCodeBtn?.addEventListener("click", async function () {
    const email = fieldMap.email.input.value.trim().toLowerCase();

    if (!email) {
      setFieldState(fieldMap.email, "Please enter your email address.");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setFieldState(fieldMap.email, "Please enter a valid email address.");
      return;
    }

    const originalText = sendVerificationCodeBtn.textContent;

    sendVerificationCodeBtn.disabled = true;
    sendVerificationCodeBtn.textContent = "Sending...";

    setVerificationMessage("", "error");

    try {
      const formData = new FormData();

      formData.append("email", email);

      if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
      }

      const response = await fetch("/register/send-verification-code", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await response.json();

      updateCsrfToken(data.csrfHash);

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Unable to send verification code.");
      }

      verificationCodeSection.style.display = "block";

      setVerificationMessage(
        "Verification code sent. Check your email.",
        "success",
      );

      emailVerificationStatus.textContent = "Waiting for verification";

      emailVerificationStatus.className =
        "align-self-center small text-warning";

      let seconds = 60;

      sendVerificationCodeBtn.textContent = `Resend in ${seconds}s`;

      clearInterval(resendTimer);

      resendTimer = setInterval(function () {
        seconds--;

        if (seconds <= 0) {
          clearInterval(resendTimer);

          sendVerificationCodeBtn.disabled = false;
          sendVerificationCodeBtn.textContent = "Resend Code";

          return;
        }

        sendVerificationCodeBtn.textContent = `Resend in ${seconds}s`;
      }, 1000);
    } catch (error) {
      setVerificationMessage(
        error.message || "Unable to send verification code.",
        "error",
      );

      sendVerificationCodeBtn.disabled = false;
      sendVerificationCodeBtn.textContent = originalText;
    }
  });

  verifyEmailCodeBtn?.addEventListener("click", async function () {
    const email = fieldMap.email.input.value.trim().toLowerCase();
    const code = verificationCode.value.trim();

    if (!/^\d{6}$/.test(code)) {
      setVerificationMessage("Enter the 6-digit verification code.", "error");

      verificationCode.focus();
      return;
    }

    const originalText = verifyEmailCodeBtn.textContent;

    verifyEmailCodeBtn.disabled = true;
    verifyEmailCodeBtn.textContent = "Verifying...";

    try {
      const formData = new FormData();

      formData.append("email", email);
      formData.append("code", code);

      if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
      }

      const response = await fetch("/register/verify-code", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await response.json();

      updateCsrfToken(data.csrfHash);

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Unable to verify email.");
      }

      verifiedEmail = email;

      emailVerificationStatus.textContent = "✓ Email verified";

      emailVerificationStatus.className =
        "align-self-center small text-success fw-semibold";

      setVerificationMessage("Email verified successfully.", "success");

      verificationCode.readOnly = true;
      verifyEmailCodeBtn.disabled = true;
      verifyEmailCodeBtn.textContent = "Verified";
    } catch (error) {
      setVerificationMessage(
        error.message || "Unable to verify email.",
        "error",
      );

      verifyEmailCodeBtn.disabled = false;
      verifyEmailCodeBtn.textContent = originalText;
    }
  });

  fieldMap.email.input.addEventListener("input", function () {
    const currentEmail = fieldMap.email.input.value.trim().toLowerCase();

    if (verifiedEmail !== "" && currentEmail !== verifiedEmail) {
      resetEmailVerification();

      verificationCodeSection.style.display = "none";

      verificationCode.readOnly = false;

      verifyEmailCodeBtn.disabled = false;
      verifyEmailCodeBtn.textContent = "Verify";
    }
  });

  registerForm.addEventListener("submit", function (event) {
    const formValid = validateRegisterForm();

    const currentEmail = fieldMap.email.input.value.trim().toLowerCase();

    if (!formValid) {
      event.preventDefault();

      formMessage.textContent = "Please correct the highlighted fields.";

      formMessage.className = "form-message error";

      return;
    }

    if (verifiedEmail === "" || currentEmail !== verifiedEmail) {
      event.preventDefault();

      formMessage.textContent =
        "Please verify your email address before creating your account.";

      formMessage.className = "form-message error";

      fieldMap.email.input.focus();

      return;
    }

    formMessage.textContent = "";
  });
});
