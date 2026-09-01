document.addEventListener("DOMContentLoaded", () => {
  const profileForm = document.getElementById("profileForm");

  const profileImage = document.getElementById("profileImage");
  const profilePreview = document.getElementById("profilePreview");
  const sideProfilePreview = document.getElementById("sideProfilePreview");
  const selectedFileName = document.getElementById("selectedFileName");

  const currentPassword = document.getElementById("currentPassword");
  const newPassword = document.getElementById("newPassword");
  const confirmPassword = document.getElementById("confirmPassword");
  const passwordMatchMessage = document.getElementById("passwordMatchMessage");

  const saveProfileButton = document.getElementById("saveProfileButton");

  const emailInput = document.getElementById("email");

  const sendProfileEmailCodeBtn = document.getElementById(
    "sendProfileEmailCodeBtn",
  );

  const verifyProfileEmailCodeBtn = document.getElementById(
    "verifyProfileEmailCodeBtn",
  );

  const profileEmailCodeSection = document.getElementById(
    "profileEmailCodeSection",
  );

  const profileEmailCode = document.getElementById("profileEmailCode");

  const profileEmailCodeMessage = document.getElementById(
    "profileEmailCodeMessage",
  );

  const profileEmailVerificationStatus = document.getElementById(
    "profileEmailVerificationStatus",
  );

  const csrfInput = profileForm?.querySelector('input[type="hidden"]');

  const originalEmail = emailInput?.value.trim().toLowerCase() || "";

  let verifiedProfileEmail = originalEmail;
  let profileEmailResendTimer = null;

  function updateProfileCsrfToken(newHash) {
    if (csrfInput && newHash) {
      csrfInput.value = newHash;
    }
  }

  function setProfileEmailMessage(message, type) {
    if (!profileEmailCodeMessage) return;

    profileEmailCodeMessage.textContent = message;

    profileEmailCodeMessage.className =
      type === "success" ? "small mt-2 text-success" : "small mt-2 text-danger";
  }

  function resetProfileEmailVerification() {
    verifiedProfileEmail = "";

    if (profileEmailVerificationStatus) {
      profileEmailVerificationStatus.textContent = "New email not verified";

      profileEmailVerificationStatus.className = "small text-danger";
    }

    if (profileEmailCode) {
      profileEmailCode.value = "";
      profileEmailCode.readOnly = false;
    }

    if (verifyProfileEmailCodeBtn) {
      verifyProfileEmailCodeBtn.disabled = false;
      verifyProfileEmailCodeBtn.textContent = "Verify";
    }

    if (profileEmailCodeMessage) {
      profileEmailCodeMessage.textContent = "";
      profileEmailCodeMessage.className = "small mt-2";
    }
  }

  sendProfileEmailCodeBtn?.addEventListener("click", async () => {
    const newEmail = emailInput?.value.trim().toLowerCase() || "";

    if (!newEmail) {
      setProfileEmailMessage("Please enter your new email address.", "error");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
      setProfileEmailMessage("Please enter a valid email address.", "error");
      return;
    }

    if (newEmail === originalEmail) {
      setProfileEmailMessage(
        "This is already your current email address.",
        "error",
      );
      return;
    }

    const originalText = sendProfileEmailCodeBtn.textContent;

    sendProfileEmailCodeBtn.disabled = true;
    sendProfileEmailCodeBtn.textContent = "Sending...";

    setProfileEmailMessage("", "error");

    try {
      const formData = new FormData();

      formData.append("email", newEmail);

      if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
      }

      const response = await fetch("/resident/profile/send-email-code", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await response.json();

      updateProfileCsrfToken(data.csrfHash);

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Unable to send verification code.");
      }

      if (profileEmailCodeSection) {
        profileEmailCodeSection.style.display = "block";
      }

      if (profileEmailVerificationStatus) {
        profileEmailVerificationStatus.textContent = "Waiting for verification";

        profileEmailVerificationStatus.className = "small text-warning";
      }

      setProfileEmailMessage(
        "Verification code sent. Check your new email.",
        "success",
      );

      let seconds = 60;

      sendProfileEmailCodeBtn.textContent = `Resend in ${seconds}s`;

      clearInterval(profileEmailResendTimer);

      profileEmailResendTimer = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
          clearInterval(profileEmailResendTimer);

          sendProfileEmailCodeBtn.disabled = false;
          sendProfileEmailCodeBtn.textContent = "Resend Code";
          return;
        }

        sendProfileEmailCodeBtn.textContent = `Resend in ${seconds}s`;
      }, 1000);
    } catch (error) {
      setProfileEmailMessage(
        error.message || "Unable to send verification code.",
        "error",
      );

      sendProfileEmailCodeBtn.disabled = false;
      sendProfileEmailCodeBtn.textContent = originalText;
    }
  });

  verifyProfileEmailCodeBtn?.addEventListener("click", async () => {
    const newEmail = emailInput?.value.trim().toLowerCase() || "";
    const code = profileEmailCode?.value.trim() || "";

    if (!/^\d{6}$/.test(code)) {
      setProfileEmailMessage("Enter the 6-digit verification code.", "error");

      profileEmailCode?.focus();
      return;
    }

    const originalText = verifyProfileEmailCodeBtn.textContent;

    verifyProfileEmailCodeBtn.disabled = true;
    verifyProfileEmailCodeBtn.textContent = "Verifying...";

    try {
      const formData = new FormData();

      formData.append("email", newEmail);
      formData.append("code", code);

      if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
      }

      const response = await fetch("/resident/profile/verify-email-code", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await response.json();

      updateProfileCsrfToken(data.csrfHash);

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Unable to verify email.");
      }

      verifiedProfileEmail = newEmail;

      if (profileEmailVerificationStatus) {
        profileEmailVerificationStatus.textContent = "✓ New email verified";

        profileEmailVerificationStatus.className =
          "small text-success fw-semibold";
      }

      setProfileEmailMessage("New email verified successfully.", "success");

      if (profileEmailCode) {
        profileEmailCode.readOnly = true;
      }

      verifyProfileEmailCodeBtn.disabled = true;
      verifyProfileEmailCodeBtn.textContent = "Verified";
    } catch (error) {
      setProfileEmailMessage(
        error.message || "Unable to verify email.",
        "error",
      );

      verifyProfileEmailCodeBtn.disabled = false;
      verifyProfileEmailCodeBtn.textContent = originalText;
    }
  });

  emailInput?.addEventListener("input", () => {
    const currentEmail = emailInput.value.trim().toLowerCase();

    if (currentEmail === originalEmail) {
      verifiedProfileEmail = originalEmail;

      if (profileEmailVerificationStatus) {
        profileEmailVerificationStatus.textContent =
          "Current email is verified";

        profileEmailVerificationStatus.className = "small text-muted";
      }

      if (profileEmailCodeSection) {
        profileEmailCodeSection.style.display = "none";
      }

      if (profileEmailCode) {
        profileEmailCode.value = "";
        profileEmailCode.readOnly = false;
      }

      if (verifyProfileEmailCodeBtn) {
        verifyProfileEmailCodeBtn.disabled = false;
        verifyProfileEmailCodeBtn.textContent = "Verify";
      }

      if (profileEmailCodeMessage) {
        profileEmailCodeMessage.textContent = "";
        profileEmailCodeMessage.className = "small mt-2";
      }

      return;
    }

    if (verifiedProfileEmail !== "" && currentEmail !== verifiedProfileEmail) {
      resetProfileEmailVerification();

      if (profileEmailCodeSection) {
        profileEmailCodeSection.style.display = "none";
      }
    }
  });

  const deleteConfirmation = document.getElementById("deleteConfirmation");

  const deleteAccountButton = document.getElementById("deleteAccountButton");

  /* =====================================================
       PROFILE PHOTO
    ===================================================== */

  if (profileImage) {
    profileImage.addEventListener("change", () => {
      const file = profileImage.files?.[0];

      if (!file) {
        resetProfilePreview();
        return;
      }

      const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

      const maxSize = 5 * 1024 * 1024;

      if (!allowedTypes.includes(file.type)) {
        alert("Profile picture must be JPG, PNG, or WebP.");

        profileImage.value = "";

        resetProfilePreview();

        return;
      }

      if (file.size > maxSize) {
        alert("Profile picture must not exceed 5 MB.");

        profileImage.value = "";

        resetProfilePreview();

        return;
      }

      const previewUrl = URL.createObjectURL(file);

      if (profilePreview) {
        profilePreview.src = previewUrl;
      }

      if (sideProfilePreview) {
        sideProfilePreview.src = previewUrl;
      }

      if (selectedFileName) {
        selectedFileName.textContent = `Selected: ${file.name}`;
      }
    });
  }

  function resetProfilePreview() {
    if (profilePreview) {
      profilePreview.src = profilePreview.dataset.originalSrc;
    }

    if (sideProfilePreview) {
      sideProfilePreview.src = sideProfilePreview.dataset.originalSrc;
    }

    if (selectedFileName) {
      selectedFileName.textContent = "";
    }
  }

  /* =====================================================
       PASSWORD SHOW / HIDE
    ===================================================== */

  document.querySelectorAll(".password-toggle").forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.dataset.passwordTarget;

      const input = document.getElementById(targetId);

      if (!input) {
        return;
      }

      const icon = button.querySelector("i");

      const isPassword = input.type === "password";

      input.type = isPassword ? "text" : "password";

      if (icon) {
        icon.classList.toggle("bi-eye", !isPassword);

        icon.classList.toggle("bi-eye-slash", isPassword);
      }

      button.setAttribute(
        "aria-label",
        isPassword ? "Hide password" : "Show password",
      );
    });
  });

  /* =====================================================
       PASSWORD MATCH INDICATOR
    ===================================================== */

  function checkPasswordMatch() {
    if (!newPassword || !confirmPassword || !passwordMatchMessage) {
      return;
    }

    const newValue = newPassword.value;
    const confirmValue = confirmPassword.value;

    passwordMatchMessage.classList.remove("match", "mismatch");

    if (newValue === "" && confirmValue === "") {
      passwordMatchMessage.textContent = "";
      return;
    }

    if (confirmValue === "") {
      passwordMatchMessage.textContent = "";
      return;
    }

    if (newValue === confirmValue) {
      passwordMatchMessage.textContent = "Passwords match.";

      passwordMatchMessage.classList.add("match");
    } else {
      passwordMatchMessage.textContent = "Passwords do not match.";

      passwordMatchMessage.classList.add("mismatch");
    }
  }

  if (newPassword) {
    newPassword.addEventListener("input", checkPasswordMatch);
  }

  if (confirmPassword) {
    confirmPassword.addEventListener("input", checkPasswordMatch);
  }

  /* =====================================================
       RESET CHANGES
    ===================================================== */

  if (profileForm) {
    profileForm.addEventListener("reset", (event) => {
      const confirmed = window.confirm("Discard all unsaved account changes?");

      if (!confirmed) {
        event.preventDefault();
        return;
      }

      setTimeout(() => {
        resetProfilePreview();

        if (passwordMatchMessage) {
          passwordMatchMessage.textContent = "";

          passwordMatchMessage.classList.remove("match", "mismatch");
        }

        document.querySelectorAll(".password-toggle").forEach((button) => {
          const targetId = button.dataset.passwordTarget;

          const input = document.getElementById(targetId);

          const icon = button.querySelector("i");

          if (input) {
            input.type = "password";
          }

          if (icon) {
            icon.classList.add("bi-eye");

            icon.classList.remove("bi-eye-slash");
          }
        });
      }, 0);
    });
  }

  /* =====================================================
       PROFILE FORM SUBMIT
    ===================================================== */

  if (profileForm) {
    profileForm.addEventListener("submit", (event) => {
      const currentProfileEmail = emailInput?.value.trim().toLowerCase() || "";

      if (
        currentProfileEmail !== originalEmail &&
        currentProfileEmail !== verifiedProfileEmail
      ) {
        event.preventDefault();

        setProfileEmailMessage(
          "Please verify your new email address before saving your profile.",
          "error",
        );

        if (profileEmailVerificationStatus) {
          profileEmailVerificationStatus.textContent = "New email not verified";

          profileEmailVerificationStatus.className = "small text-danger";
        }

        emailInput?.focus();
        return;
      }

      if (
        newPassword &&
        confirmPassword &&
        (newPassword.value !== "" || confirmPassword.value !== "")
      ) {
        if (newPassword.value !== confirmPassword.value) {
          event.preventDefault();

          checkPasswordMatch();

          confirmPassword.focus();

          return;
        }

        if (newPassword.value.length < 8) {
          event.preventDefault();

          alert("New password must be at least 8 characters.");

          newPassword.focus();

          return;
        }

        if (currentPassword && currentPassword.value === "") {
          event.preventDefault();

          alert("Enter your current password before changing your password.");

          currentPassword.focus();

          return;
        }
      }

      if (saveProfileButton) {
        saveProfileButton.disabled = true;

        saveProfileButton.innerHTML =
          '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Saving...';
      }
    });
  }

  /* =====================================================
       DELETE ACCOUNT CONFIRMATION
    ===================================================== */

  if (deleteConfirmation && deleteAccountButton) {
    deleteAccountButton.disabled = true;

    deleteConfirmation.addEventListener("input", () => {
      const confirmed = deleteConfirmation.value === "DELETE";

      deleteAccountButton.disabled = !confirmed;
    });
  }

  /* =====================================================
       CLEAR DELETE MODAL WHEN CLOSED
    ===================================================== */

  const deleteModal = document.getElementById("deleteAccountModal");

  if (deleteModal) {
    deleteModal.addEventListener("hidden.bs.modal", () => {
      const passwordInput = document.getElementById("deleteCurrentPassword");

      if (passwordInput) {
        passwordInput.value = "";
      }

      if (deleteConfirmation) {
        deleteConfirmation.value = "";
      }

      if (deleteAccountButton) {
        deleteAccountButton.disabled = true;
      }
    });
  }
});
