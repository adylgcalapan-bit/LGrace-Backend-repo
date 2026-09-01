<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Community Visibility System | Register</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/register.css') ?>">
</head>

<body>

    <div class="container-fluid register-page px-0">

        <div class="row g-0 min-vh-100">

            <!-- =========================================
                 LEFT SIDE - BARANGAY SAGUING
            ========================================== -->
            <div class="col-12 col-lg-5 left-panel">

                <div class="left-decoration decoration-one"></div>
                <div class="left-decoration decoration-two"></div>

                <div class="left-panel-content">

                    <span class="welcome-badge">
                        <i class="bi bi-geo-alt-fill"></i>
                        Barangay Saguing
                    </span>

                    <div class="building-image-wrapper">

                        <img
                            src="<?= base_url('assets/images/saguing-building.png') ?>"
                            class="saguing-building"
                            alt="Barangay Saguing Building">

                    </div>

                    <div class="left-panel-text">

                        <h1>
                            Community Problems
                            <span>Visibility System</span>
                        </h1>

                        <p>
                            A simple and accessible platform for residents
                            to report community concerns, receive updates,
                            and stay connected with Barangay Saguing.
                        </p>

                    </div>

                    <div class="feature-pills">

                        <span>
                            <i class="bi bi-megaphone-fill"></i>
                            Report
                        </span>

                        <span>
                            <i class="bi bi-geo-alt-fill"></i>
                            Locate
                        </span>

                        <span>
                            <i class="bi bi-bell-fill"></i>
                            Stay Updated
                        </span>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 RIGHT SIDE - REGISTRATION FORM
            ========================================== -->
            <div class="col-12 col-lg-7 register-side">

                <div class="register-card">

                    <!-- HEADER -->
                    <div class="register-header">

                        <div class="register-header-text">

                            <span class="register-badge">
                                Resident Registration
                            </span>

                            <h2>Create an Account</h2>

                            <p>
                                Fill in your information to get started.
                            </p>

                        </div>

                        <!-- ONLY BARANGAY LOGO ON RIGHT SIDE -->
                        <div class="header-logo-wrapper">

                            <img
                                src="<?= base_url('assets/images/logo.jpg') ?>"
                                class="header-logo"
                                alt="Barangay Saguing Logo">

                        </div>

                    </div>


                    <!-- =========================================
                         SERVER ERROR MESSAGE
                    ========================================== -->
                    <?php if (session()->getFlashdata('error')): ?>

                        <div class="soft-alert soft-alert-danger">

                            <i class="bi bi-exclamation-circle-fill"></i>

                            <span>
                                <?= esc(session()->getFlashdata('error')) ?>
                            </span>

                        </div>

                    <?php endif; ?>


                    <!-- =========================================
                         REGISTRATION FORM
                    ========================================== -->
                    <form
                        id="registerForm"
                        action="<?= site_url('register') ?>"
                        method="POST"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>


                        <!-- =========================================
                             PROFILE PICTURE
                        ========================================== -->
                        <div class="profile-upload-card">

                            <div class="profile-upload-content">

                                <!-- DEFAULT AVATAR - NO LOGO -->
                                <div
                                    class="profile-placeholder"
                                    id="profilePlaceholder">

                                    <i class="bi bi-person-fill"></i>

                                </div>

                                <!-- IMAGE PREVIEW -->
                                <img
                                    src=""
                                    id="profilePreview"
                                    class="profile-preview d-none"
                                    alt="Selected Profile Picture">

                                <div class="profile-upload-text">

                                    <h6>
                                        Profile Picture
                                    </h6>

                                    <p>
                                        Upload a clear photo for your account.
                                        This is optional.
                                    </p>

                                </div>

                            </div>

                            <input
                                id="profileImage"
                                name="profileImage"
                                type="file"
                                class="form-control profile-file-input"
                                accept="image/png,image/jpeg,image/jpg,image/webp">

                        </div>


                        <!-- =========================================
                             FULL NAME + EMAIL
                        ========================================== -->
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="fullName">

                                    Full Name
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="fullName"
                                        name="fullName"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter full name"
                                        value="<?= esc(old('fullName')) ?>"
                                        autocomplete="name"
                                        required>

                                    <div
                                        class="error-text"
                                        id="fullNameError">
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="email">

                                    Email Address
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="form-control"
                                        placeholder="example@email.com"
                                        value="<?= esc(old('email')) ?>"
                                        autocomplete="email"
                                        required>

                                    <div
                                        class="error-text"
                                        id="emailError">
                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =========================================
                             MOBILE + USERNAME
                        ========================================== -->
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="mobileNumber">

                                    Mobile Number
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="mobileNumber"
                                        name="mobileNumber"
                                        type="text"
                                        class="form-control"
                                        placeholder="09XXXXXXXXX"
                                        value="<?= esc(old('mobileNumber')) ?>"
                                        inputmode="numeric"
                                        autocomplete="tel"
                                        maxlength="11"
                                        required>

                                    <div
                                        class="error-text"
                                        id="mobileNumberError">
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="registerUsername">

                                    Username
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="registerUsername"
                                        name="registerUsername"
                                        type="text"
                                        class="form-control"
                                        placeholder="Choose a username"
                                        value="<?= esc(old('registerUsername')) ?>"
                                        autocomplete="username"
                                        required>

                                    <div
                                        class="error-text"
                                        id="usernameError">
                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =========================================
                             PASSWORD + CONFIRM PASSWORD
                        ========================================== -->
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="password">

                                    Password
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="form-control"
                                        placeholder="At least 8 characters"
                                        autocomplete="new-password"
                                        required>

                                    <div
                                        class="error-text"
                                        id="passwordError">
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                    for="confirmPassword">

                                    Confirm Password
                                    <span class="required">*</span>

                                </label>

                                <div class="field-group">

                                    <input
                                        id="confirmPassword"
                                        name="confirmPassword"
                                        type="password"
                                        class="form-control"
                                        placeholder="Re-enter password"
                                        autocomplete="new-password"
                                        required>

                                    <div
                                        class="error-text"
                                        id="confirmPasswordError">
                                    </div>

                                </div>

                            </div>

                            <div class="mt-2">
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        id="sendVerificationCodeBtn"
                                        class="btn btn-outline-success">
                                        Send Code
                                    </button>

                                    <span
                                        id="emailVerificationStatus"
                                        class="align-self-center small text-muted">
                                        Email not verified
                                    </span>
                                </div>
                            </div>

                            <div
                                id="verificationCodeSection"
                                class="mt-3"
                                style="display: none;">

                                <label
                                    for="verificationCode"
                                    class="form-label">
                                    Verification Code
                                </label>

                                <div class="d-flex gap-2">
                                    <input
                                        type="text"
                                        id="verificationCode"
                                        class="form-control"
                                        maxlength="6"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        placeholder="Enter 6-digit code">

                                    <button
                                        type="button"
                                        id="verifyEmailCodeBtn"
                                        class="btn btn-success">
                                        Verify
                                    </button>
                                </div>

                                <div
                                    id="verificationCodeMessage"
                                    class="small mt-2">
                                </div>
                            </div>

                        </div>


                        <!-- =========================================
                             PUROK
                        ========================================== -->
                        <div class="mb-3">

                            <label
                                class="form-label"
                                for="purok_id">

                                Purok
                                <span class="required">*</span>

                            </label>

                            <div class="field-group">

                                <select
                                    id="purok_id"
                                    name="purok_id"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Select your Purok
                                    </option>

                                    <?php foreach ($puroks ?? [] as $purok): ?>

                                        <option
                                            value="<?= (int) $purok['purok_id'] ?? '' ?>"
                                            <?= old('purok_id') == ($purok['purok_id'] ?? '')
                                                ? 'selected'
                                                : '' ?>>

                                            <?= esc($purok['purok_name'] ?? '') ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <div
                                    class="error-text"
                                    id="purokError">
                                </div>

                            </div>

                        </div>


                        <!-- =========================================
                             ADDRESS
                        ========================================== -->
                        <div class="mb-3">

                            <label
                                class="form-label"
                                for="address">

                                Additional Address Details
                                <span class="required">*</span>

                            </label>

                            <div class="field-group">

                                <textarea
                                    id="address"
                                    name="address"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Sitio, street, nearby landmark, or other location details"
                                    required><?= esc(old('address')) ?></textarea>

                                <div
                                    class="error-text"
                                    id="addressError">
                                </div>

                            </div>

                            <small class="field-helper">
                                <i class="bi bi-info-circle"></i>
                                Add a nearby landmark to make your location easier to identify.
                            </small>

                        </div>


                        <!-- =========================================
                             ROLE
                        ========================================== -->
                        <div class="mb-4">

                            <label
                                class="form-label"
                                for="role">

                                Register As

                            </label>

                            <select
                                id="role"
                                name="role"
                                class="form-select"
                                disabled>

                                <option selected>
                                    Resident
                                </option>

                            </select>

                            <small class="field-helper">
                                <i class="bi bi-shield-lock"></i>
                                Admin accounts are managed separately by Barangay personnel.
                            </small>

                        </div>


                        <!-- JS FORM MESSAGE -->
                        <div
                            id="formMessage"
                            class="form-message"
                            aria-live="polite">
                        </div>


                        <!-- SUBMIT -->
                        <button
                            type="submit"
                            class="btn register-btn w-100">

                            <i class="bi bi-person-plus-fill"></i>

                            Create Account

                        </button>

                    </form>


                    <!-- LOGIN LINK -->
                    <div class="login-bottom">

                        <span>
                            Already have an account?
                        </span>

                        <a href="<?= site_url('login') ?>">
                            Login
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Bootstrap -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Existing Registration JS -->
    <script src="<?= base_url('assets/js/register.js') ?>"></script>


    <!-- PROFILE PICTURE PREVIEW ONLY -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const profileInput =
                document.getElementById('profileImage');

            const profilePreview =
                document.getElementById('profilePreview');

            const profilePlaceholder =
                document.getElementById('profilePlaceholder');


            if (!profileInput) {
                return;
            }


            profileInput.addEventListener('change', function() {

                const file = this.files[0];

                if (!file) {

                    profilePreview.src = '';
                    profilePreview.classList.add('d-none');

                    profilePlaceholder.classList.remove('d-none');

                    return;
                }


                if (!file.type.startsWith('image/')) {

                    this.value = '';

                    profilePreview.src = '';
                    profilePreview.classList.add('d-none');

                    profilePlaceholder.classList.remove('d-none');

                    return;
                }


                const reader = new FileReader();


                reader.onload = function(event) {

                    profilePreview.src = event.target.result;

                    profilePreview.classList.remove('d-none');

                    profilePlaceholder.classList.add('d-none');

                };


                reader.readAsDataURL(file);

            });

        });
    </script>

</body>

</html>