document.addEventListener("DOMContentLoaded", () => {
  const saveBtn = document.getElementById("saveChangesBtn");
  const changeBtn = document.getElementById("changePasswordBtn");

  const adminEmail = document.getElementById("admin_email");

  const sendAdminEmailCodeBtn = document.getElementById(
    "sendAdminEmailCodeBtn",
  );

  const verifyAdminEmailCodeBtn = document.getElementById(
    "verifyAdminEmailCodeBtn",
  );

  const adminEmailCodeSection = document.getElementById(
    "adminEmailCodeSection",
  );

  const adminEmailCode = document.getElementById("adminEmailCode");

  const adminEmailCodeMessage = document.getElementById(
    "adminEmailCodeMessage",
  );

  const adminEmailVerificationStatus = document.getElementById(
    "adminEmailVerificationStatus",
  );
  const uploadPhotoBtn = document.getElementById("uploadPhotoBtn");
  const profileImageInput = document.getElementById("profileImageInput");
  const selectedProfileFile = document.getElementById("selectedProfileFile");
  const profilePreview = document.getElementById("profilePreview");
  const topProfileImage = document.getElementById("topProfileImage");
  const currentPassword = document.getElementById("currentPassword");
  const newPassword = document.getElementById("newPassword");
  const confirmPassword = document.getElementById("confirmPassword");

  profileImageInput?.addEventListener("change", () => {
    const file = profileImageInput.files?.[0];

    if (!file) {
      if (selectedProfileFile) {
        selectedProfileFile.textContent = "No image selected";
      }
      return;
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

    if (!allowedTypes.includes(file.type)) {
      showMessage("Only JPG, PNG, and WebP images are allowed.", "danger");

      profileImageInput.value = "";
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      showMessage("Profile image must not exceed 2 MB.", "danger");

      profileImageInput.value = "";
      return;
    }

    if (selectedProfileFile) {
      selectedProfileFile.textContent = file.name;
    }

    // Preview selected image only.
    // This does NOT save it yet.
    const reader = new FileReader();

    reader.onload = function (event) {
      if (profilePreview) {
        profilePreview.src = event.target.result;
      }
    };

    reader.readAsDataURL(file);
  });

  const showMessage = (message, type = "success") => {
    const oldToast = document.querySelector(".account-toast");

    if (oldToast) {
      oldToast.remove();
    }

    const toast = document.createElement("div");

    toast.className = `account-toast account-toast-${type}`;

    toast.innerHTML = `
    <div class="account-toast-icon">
      <i class="bi ${
        type === "success"
          ? "bi-check-circle-fill"
          : "bi-exclamation-circle-fill"
      }"></i>
    </div>

    <div class="account-toast-message">
      ${message}
    </div>

    <button type="button" class="account-toast-close">
      <i class="bi bi-x-lg"></i>
    </button>
  `;

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    toast
      .querySelector(".account-toast-close")
      ?.addEventListener("click", () => {
        toast.remove();
      });

    setTimeout(() => {
      toast.classList.remove("show");

      setTimeout(() => {
        toast.remove();
      }, 250);
    }, 4000);
  };
  // =========================================
  // ADMIN EMAIL VERIFICATION
  // =========================================

  function setAdminEmailCodeMessage(message, type = "muted") {
    if (!adminEmailCodeMessage) {
      return;
    }

    adminEmailCodeMessage.textContent = message;

    if (type === "success") {
      adminEmailCodeMessage.className = "small mt-2 text-success";
    } else if (type === "error") {
      adminEmailCodeMessage.className = "small mt-2 text-danger";
    } else {
      adminEmailCodeMessage.className = "small mt-2 text-muted";
    }
  }

  function resetAdminEmailVerification() {
    if (adminEmailVerificationStatus) {
      adminEmailVerificationStatus.textContent = "New email not verified";
      adminEmailVerificationStatus.className = "small text-danger";
    }

    if (adminEmailCode) {
      adminEmailCode.value = "";
      adminEmailCode.readOnly = false;
    }

    if (adminEmailCodeSection) {
      adminEmailCodeSection.style.display = "none";
    }

    if (verifyAdminEmailCodeBtn) {
      verifyAdminEmailCodeBtn.disabled = false;
      verifyAdminEmailCodeBtn.textContent = "Verify";
    }

    setAdminEmailCodeMessage("");
  }

  sendAdminEmailCodeBtn?.addEventListener("click", async () => {
    const emailValue = adminEmail?.value.trim();

    if (!emailValue) {
      showMessage("Please enter the new email address first.", "danger");
      adminEmail?.focus();
      return;
    }

    const sendUrl = sendAdminEmailCodeBtn.dataset.sendUrl;

    const csrfName = saveBtn?.dataset.csrfName;
    const csrfHash = saveBtn?.dataset.csrfHash;

    const formData = new FormData();

    formData.append("email", emailValue);

    if (csrfName && csrfHash) {
      formData.append(csrfName, csrfHash);
    }

    const originalText = sendAdminEmailCodeBtn.textContent;

    sendAdminEmailCodeBtn.disabled = true;
    sendAdminEmailCodeBtn.textContent = "Sending...";

    try {
      const response = await fetch(sendUrl, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json().catch(() => null);

      if (data?.csrfHash && saveBtn) {
        saveBtn.dataset.csrfHash = data.csrfHash;
      }

      if (!response.ok || !data?.success) {
        throw new Error(data?.message || "Unable to send verification code.");
      }

      if (adminEmailCodeSection) {
        adminEmailCodeSection.style.display = "block";
      }

      if (adminEmailVerificationStatus) {
        adminEmailVerificationStatus.textContent = "Waiting for verification";
        adminEmailVerificationStatus.className = "small text-warning";
      }

      setAdminEmailCodeMessage(
        "Verification code sent. Check your new email.",
        "success",
      );

      let seconds = 60;

      sendAdminEmailCodeBtn.textContent = `Resend in ${seconds}s`;

      const timer = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
          clearInterval(timer);

          sendAdminEmailCodeBtn.disabled = false;
          sendAdminEmailCodeBtn.textContent = "Resend Code";
          return;
        }

        sendAdminEmailCodeBtn.textContent = `Resend in ${seconds}s`;
      }, 1000);
    } catch (error) {
      showMessage(
        error.message || "Unable to send verification code.",
        "danger",
      );

      sendAdminEmailCodeBtn.disabled = false;
      sendAdminEmailCodeBtn.textContent = originalText;
    }
  });

  verifyAdminEmailCodeBtn?.addEventListener("click", async () => {
    const emailValue = adminEmail?.value.trim();
    const code = adminEmailCode?.value.trim();

    if (!code || !/^\d{6}$/.test(code)) {
      setAdminEmailCodeMessage("Enter the 6-digit verification code.", "error");
      adminEmailCode?.focus();
      return;
    }

    const verifyUrl = verifyAdminEmailCodeBtn.dataset.verifyUrl;

    const csrfName = saveBtn?.dataset.csrfName;
    const csrfHash = saveBtn?.dataset.csrfHash;

    const formData = new FormData();

    formData.append("email", emailValue);
    formData.append("code", code);

    if (csrfName && csrfHash) {
      formData.append(csrfName, csrfHash);
    }

    const originalText = verifyAdminEmailCodeBtn.textContent;

    verifyAdminEmailCodeBtn.disabled = true;
    verifyAdminEmailCodeBtn.textContent = "Verifying...";

    try {
      const response = await fetch(verifyUrl, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json().catch(() => null);

      if (data?.csrfHash && saveBtn) {
        saveBtn.dataset.csrfHash = data.csrfHash;
      }

      if (!response.ok || !data?.success) {
        throw new Error(data?.message || "Unable to verify email.");
      }

      if (adminEmailVerificationStatus) {
        adminEmailVerificationStatus.textContent = "✓ New email verified";
        adminEmailVerificationStatus.className = "small text-success";
      }

      setAdminEmailCodeMessage(
        data.message || "New administrator email verified successfully.",
        "success",
      );

      if (adminEmailCode) {
        adminEmailCode.readOnly = true;
      }

      verifyAdminEmailCodeBtn.disabled = true;
      verifyAdminEmailCodeBtn.textContent = "Verified";

      if (sendAdminEmailCodeBtn) {
        sendAdminEmailCodeBtn.style.display = "none";
      }
    } catch (error) {
      setAdminEmailCodeMessage(
        error.message || "Unable to verify email.",
        "error",
      );

      verifyAdminEmailCodeBtn.disabled = false;
      verifyAdminEmailCodeBtn.textContent = originalText;
    }
  });

  adminEmail?.addEventListener("input", () => {
    resetAdminEmailVerification();
  });
  // =========================================
  // PASSWORD SHOW / HIDE TOGGLE
  // =========================================
  document.addEventListener("click", (event) => {
    const button = event.target.closest(".password-toggle-btn");

    if (!button) {
      return;
    }

    event.preventDefault();

    const targetId = button.getAttribute("data-target");
    const passwordInput = document.getElementById(targetId);
    const icon = button.querySelector("i");

    if (!passwordInput) {
      return;
    }

    if (passwordInput.type === "password") {
      passwordInput.type = "text";

      if (icon) {
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      }

      button.setAttribute("aria-label", "Hide password");
    } else {
      passwordInput.type = "password";

      if (icon) {
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }

      button.setAttribute("aria-label", "Show password");
    }
  });

  uploadPhotoBtn?.addEventListener("click", async () => {
    const file = profileImageInput?.files?.[0];

    if (!file) {
      showMessage("Please choose an image first.", "danger");
      return;
    }

    const photoUrl = uploadPhotoBtn.dataset.photoUrl;
    const csrfName = uploadPhotoBtn.dataset.csrfName;
    const csrfHash = uploadPhotoBtn.dataset.csrfHash;

    const formData = new FormData();

    formData.append("profile_image", file);

    if (csrfName && csrfHash) {
      formData.append(csrfName, csrfHash);
    }

    const originalContent = uploadPhotoBtn.innerHTML;

    uploadPhotoBtn.disabled = true;
    uploadPhotoBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
    Uploading...
  `;

    try {
      const response = await fetch(photoUrl, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data?.success) {
        throw new Error(data?.message || "Unable to upload profile photo.");
      }

      if (data.image_url) {
        if (profilePreview) {
          profilePreview.src = data.image_url;
        }

        if (topProfileImage) {
          topProfileImage.src = data.image_url;
        }
      }

      showMessage(
        data.message || "Profile photo updated successfully.",
        "success",
      );

      if (profileImageInput) {
        profileImageInput.value = "";
      }
    } catch (error) {
      showMessage(error.message || "Unable to upload profile photo.", "danger");
    } finally {
      uploadPhotoBtn.disabled = false;
      uploadPhotoBtn.innerHTML = originalContent;
    }
  });

  saveBtn?.addEventListener("click", async () => {
    const fullName = document.getElementById("admin_full_name");
    const username = document.getElementById("admin_username");
    const email = document.getElementById("admin_email");
    const mobileNumber = document.getElementById("admin_mobile_number");
    const address = document.getElementById("admin_address");

    const updateUrl = saveBtn.dataset.updateUrl;
    const csrfName = saveBtn.dataset.csrfName;
    const csrfHash = saveBtn.dataset.csrfHash;

    // =========================
    // BASIC FRONTEND VALIDATION
    // =========================

    if (!fullName?.value.trim()) {
      showMessage("Full name is required.", "danger");
      fullName?.focus();
      return;
    }

    if (!username?.value.trim()) {
      showMessage("Username is required.", "danger");
      username?.focus();
      return;
    }

    if (!email?.value.trim()) {
      showMessage("Email address is required.", "danger");
      email?.focus();
      return;
    }

    // =========================
    // PREPARE FORM DATA
    // =========================

    const formData = new FormData();

    formData.append("full_name", fullName.value.trim());
    formData.append("username", username.value.trim());
    formData.append("email", email.value.trim());
    formData.append(
      "mobile_number",
      mobileNumber ? mobileNumber.value.trim() : "",
    );
    formData.append("address", address ? address.value.trim() : "");

    if (csrfName && csrfHash) {
      formData.append(csrfName, csrfHash);
    }

    // =========================
    // SAVE TO DATABASE
    // =========================

    const originalContent = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
    Saving...
  `;

    try {
      const response = await fetch(updateUrl, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data?.success) {
        throw new Error(
          data?.message || "Unable to update profile information.",
        );
      }

      showMessage(
        data.message || "Profile information updated successfully.",
        "success",
      );

      // Reload so all admin names/details use the latest DB values
      setTimeout(() => {
        window.location.reload();
      }, 800);
    } catch (error) {
      showMessage(
        error.message || "Unable to update profile information.",
        "danger",
      );

      saveBtn.disabled = false;
      saveBtn.innerHTML = originalContent;
    }
  });

  changeBtn?.addEventListener("click", async () => {
    if (
      !currentPassword?.value ||
      !newPassword?.value ||
      !confirmPassword?.value
    ) {
      showMessage("Please fill in all password fields.", "danger");
      return;
    }

    if (newPassword.value !== confirmPassword.value) {
      showMessage("Passwords do not match.", "danger");
      return;
    }

    const passwordUrl = changeBtn.dataset.passwordUrl;
    const csrfName = changeBtn.dataset.csrfName;
    const csrfHash = changeBtn.dataset.csrfHash;

    const formData = new FormData();

    formData.append("current_password", currentPassword.value);
    formData.append("new_password", newPassword.value);
    formData.append("confirm_password", confirmPassword.value);

    if (csrfName && csrfHash) {
      formData.append(csrfName, csrfHash);
    }

    const originalContent = changeBtn.innerHTML;

    changeBtn.disabled = true;
    changeBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
    Changing...
  `;

    try {
      const response = await fetch(passwordUrl, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data?.success) {
        throw new Error(data?.message || "Unable to change password.");
      }

      showMessage(data.message || "Password changed successfully.", "success");

      currentPassword.value = "";
      newPassword.value = "";
      confirmPassword.value = "";
    } catch (error) {
      showMessage(error.message || "Unable to change password.", "danger");
    } finally {
      changeBtn.disabled = false;
      changeBtn.innerHTML = originalContent;
    }
  });
});
