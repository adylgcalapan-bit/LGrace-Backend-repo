<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Community Visibility System | Login</title>

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

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #f4faf5;
            color: #233128;
            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
        }


        /* ==============================
           LEFT PHOTO
        ============================== */

        .photo-side {
            position: relative;
            min-height: 100vh;
            padding: 0;
            overflow: hidden;
        }

        .barangay-photo {
            position: absolute;
            inset: 0;

            width: 100%;
            height: 100%;

            object-fit: cover;
            object-position: center;

            z-index: 0;
        }

        .photo-overlay {
            position: absolute;
            inset: 0;

            background:
                linear-gradient(180deg,
                    rgba(22, 82, 48, 0.04) 0%,
                    rgba(22, 82, 48, 0.10) 45%,
                    rgba(20, 75, 43, 0.68) 100%);

            z-index: 1;
        }

        .photo-content {
            position: relative;
            z-index: 2;

            min-height: 100vh;

            display: flex;
            flex-direction: column;
            justify-content: flex-end;

            padding: 50px;
        }

        .photo-text {
            max-width: 580px;
            color: #ffffff;
            margin-bottom: 25px;
        }

        .photo-text .mini-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 7px 13px;
            margin-bottom: 16px;

            border-radius: 100px;

            background: rgba(255, 255, 255, 0.17);
            border: 1px solid rgba(255, 255, 255, 0.22);

            backdrop-filter: blur(8px);

            font-size: 0.84rem;
            font-weight: 500;
        }

        .photo-text h1 {
            margin: 0 0 14px;

            font-size: clamp(2.1rem, 3.4vw, 3.7rem);
            line-height: 1.08;
            letter-spacing: -1px;

            font-weight: 700;
            color: #ffffff;

            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.18);
        }

        .photo-text p {
            max-width: 510px;

            margin: 0;

            font-size: 1rem;
            line-height: 1.7;

            color: rgba(255, 255, 255, 0.94);
        }


        /* ==============================
           RIGHT PANEL
        ============================== */

        .form-side {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 35px;

            background:
                radial-gradient(circle at top right,
                    #e5f6e9 0%,
                    transparent 35%),
                linear-gradient(145deg,
                    #f8fcf8 0%,
                    #edf7ef 100%);
        }

        .login-card {
            width: 100%;
            max-width: 500px;

            padding: 38px 40px;

            background: rgba(255, 255, 255, 0.96);

            border: 1px solid #e2eee4;
            border-radius: 26px;

            box-shadow:
                0 20px 55px rgba(42, 100, 61, 0.11);
        }


        /* ==============================
           RIGHT-SIDE LOGO
        ============================== */

        .login-brand {
            text-align: center;
            margin-bottom: 22px;
        }

        .login-logo-wrap {
            width: 96px;
            height: 96px;

            margin: 0 auto 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #f0f8f2;

            border: 5px solid #e0f1e4;

            box-shadow:
                0 8px 22px rgba(37, 116, 65, 0.12);
        }

        .login-logo {
            display: block;

            width: 82px;
            height: 82px;

            object-fit: contain;
            border-radius: 50%;
        }

        .login-brand h5 {
            margin: 0 0 2px;

            font-size: 1.08rem;
            font-weight: 700;

            color: #286b40;
        }

        .login-brand p {
            margin: 0;

            font-size: 0.82rem;

            color: #829087;
        }


        /* ==============================
           TITLE
        ============================== */

        .welcome-section {
            text-align: center;
            margin-bottom: 27px;
        }

        .welcome-section h2 {
            margin-bottom: 7px;

            color: #214d32;

            font-size: 2rem;
            font-weight: 700;
        }

        .welcome-section p {
            margin: 0;

            color: #748078;

            font-size: 0.94rem;
        }


        /* ==============================
           FORM
        ============================== */

        .form-label {
            color: #35443a;

            font-size: 0.92rem;
            font-weight: 600;

            margin-bottom: 7px;
        }

        .input-group {
            min-height: 52px;
        }

        .input-group-text {
            min-width: 52px;

            display: flex;
            justify-content: center;

            background: #edf7ef;

            color: #39915a;

            border-color: #dbe7dd;
            border-right: 0;

            border-radius: 11px 0 0 11px;
        }

        .input-group-text i {
            font-size: 1rem;
        }

        .form-control {
            min-height: 52px;

            color: #324037;

            border-color: #dbe7dd;
            border-left: 0;

            font-size: 0.94rem;
        }

        .form-control::placeholder {
            color: #9ba59e;
        }

        .form-control:focus {
            border-color: #74bd87;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #74bd87;
        }

        .password-toggle {
            min-width: 52px;

            background: #ffffff;

            color: #7d8a82;

            border-color: #dbe7dd;

            border-radius: 0 11px 11px 0;
        }

        .password-toggle:hover {
            background: #f2f8f4;
            color: #378d57;
            border-color: #74bd87;
        }


        /* ==============================
           REMEMBER + FORGOT
        ============================== */

        .login-options {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 15px;

            margin-top: 18px;
            margin-bottom: 24px;
        }

        .form-check {
            margin: 0;
        }

        .form-check-label {
            color: #59645d;
            font-size: 0.88rem;
        }

        .form-check-input {
            border-color: #cdd9d0;
        }

        .form-check-input:checked {
            background-color: #57ad70;
            border-color: #57ad70;
        }

        .forgot-link {
            color: #489d61;

            font-size: 0.88rem;
            font-weight: 600;

            text-decoration: none;

            white-space: nowrap;
        }

        .forgot-link:hover {
            color: #307b49;
            text-decoration: underline;
        }


        /* ==============================
           LOGIN BUTTON
        ============================== */

        .login-btn {
            width: 100%;
            min-height: 52px;

            border: 0;
            border-radius: 12px;

            background:
                linear-gradient(135deg,
                    #69bf7e,
                    #4ba969);

            color: #ffffff;

            font-weight: 600;

            box-shadow:
                0 8px 18px rgba(75, 169, 105, 0.17);

            transition: 0.2s ease;
        }

        .login-btn:hover {
            color: #ffffff;

            background:
                linear-gradient(135deg,
                    #5bb872,
                    #409b5d);

            transform: translateY(-1px);
        }


        /* ==============================
           REGISTER LINK
        ============================== */

        .register-section {
            text-align: center;

            margin-top: 24px;

            color: #66726a;

            font-size: 0.89rem;
        }

        .register-section a {
            color: #489d61;

            font-weight: 600;

            text-decoration: none;
        }

        .register-section a:hover {
            color: #307b49;
            text-decoration: underline;
        }


        /* ==============================
           ALERTS
        ============================== */

        .alert {
            border: 0;
            border-radius: 11px;

            font-size: 0.88rem;
        }

        .alert-danger {
            background: #fff0f0;
            color: #a54343;
        }

        .alert-success {
            background: #eef9f0;
            color: #39754b;
        }

        .error-text {
            margin-top: 5px;

            color: #c94e4e;

            font-size: 0.78rem;
        }


        /* ==============================
           RESPONSIVE
        ============================== */

        @media (max-width: 991.98px) {

            .photo-side {
                display: none;
            }

            .form-side {
                padding: 30px 20px;
            }

            .login-card {
                max-width: 520px;
            }
        }


        @media (max-width: 575.98px) {

            .form-side {
                padding: 18px 12px;
            }

            .login-card {
                padding: 28px 20px;

                border-radius: 20px;
            }

            .login-logo-wrap {
                width: 84px;
                height: 84px;
            }

            .login-logo {
                width: 72px;
                height: 72px;
            }

            .welcome-section h2 {
                font-size: 1.65rem;
            }

            .login-options {
                align-items: flex-start;
                flex-direction: column;

                gap: 10px;
            }
        }

        /* ==============================
   LOGIN SUPPORT CONTACT
============================== */

        .login-support {
            margin-top: 20px;
            padding: 0;

            background: transparent;
            border: none;
            border-radius: 0;
            box-shadow: none;

            text-align: left;
        }


        /* TITLE */

        .login-support-title {
            display: flex;
            align-items: center;
            justify-content: flex-start;

            gap: 7px;
            margin-bottom: 9px;

            color: #ffffff;

            font-size: 0.88rem;
            font-weight: 700;
        }


        /* CONTACT DETAILS */

        .login-support-details {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;

            gap: 8px 20px;
        }

        .login-support-details a {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            color: rgba(255, 255, 255, 0.92);

            font-size: 0.82rem;
            font-weight: 500;

            text-decoration: none;

            transition: 0.2s ease;
        }

        .login-support-details a i {
            color: #ffffff;
        }

        .login-support-details a:hover {
            color: #ffffff;
            text-decoration: underline;
        }


        /* ==============================
   MOBILE
============================== */

        @media (max-width: 576px) {

            .login-support {
                margin-top: 16px;
            }

            .login-support-details {
                flex-direction: column;
                align-items: flex-start;

                gap: 7px;
            }
        }
    </style>

</head>

<body>

    <div class="container-fluid p-0">
        <div class="row g-0 login-page">

            <!-- =========================================
                 LEFT SIDE - BARANGAY BUILDING
            ========================================== -->
            <div class="col-lg-6 photo-side">

                <img
                    src="<?= base_url('assets/images/saguing-building.png') ?>"
                    alt="<?= esc($settings['barangay_name'] ?? 'Barangay Saguing') ?> Building"
                    class="barangay-photo">

                <div class="photo-overlay"></div>

                <div class="photo-content">

                    <div class="photo-text">

                        <div class="mini-label">
                            <i class="bi bi-geo-alt-fill"></i>
                            <?= esc($settings['barangay_name'] ?? 'Barangay Saguing') ?>
                        </div>

                        <h1>
                            <?= esc($settings['system_name'] ?? 'Community Problems Visibility System') ?>
                        </h1>

                        <p>
                            <?= esc(
                                $settings['system_description']
                                    ?? 'A simple and user-friendly platform for reporting and monitoring community concerns.'
                            ) ?>
                        </p>

                        <?php
                        $contactEmail = trim((string) ($settings['contact_email'] ?? ''));
                        $contactNumber = trim((string) ($settings['contact_number'] ?? ''));
                        ?>

                        <?php if ($contactEmail !== '' || $contactNumber !== ''): ?>

                            <div class="login-support login-support-left">

                                <div class="login-support-title">
                                    <i class="bi bi-headset"></i>
                                    Need assistance?
                                </div>

                                <div class="login-support-details">

                                    <?php if ($contactEmail !== ''): ?>
                                        <a href="mailto:<?= esc($contactEmail) ?>">
                                            <i class="bi bi-envelope"></i>
                                            <?= esc($contactEmail) ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($contactNumber !== ''): ?>
                                        <a href="tel:<?= esc($contactNumber) ?>">
                                            <i class="bi bi-telephone"></i>
                                            <?= esc($contactNumber) ?>
                                        </a>
                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 RIGHT SIDE - LOGIN
            ========================================== -->
            <div class="col-lg-6 form-side">

                <div class="login-card">

                    <!-- LOGO -->
                    <div class="login-brand">

                        <div class="login-logo-wrap">

                            <img
                                src="<?= base_url('assets/images/logo.jpg') ?>"
                                alt="<?= esc($settings['barangay_name'] ?? 'Barangay Saguing') ?> Logo"
                                class="login-logo">

                        </div>

                        <h5>
                            <?= esc($settings['barangay_name'] ?? 'Barangay Saguing') ?>
                        </h5>

                        <p>
                            <?= esc($settings['system_name'] ?? 'Community Problems Visibility System') ?>
                        </p>
                    </div>


                    <!-- WELCOME -->
                    <div class="welcome-section">

                        <h2>
                            Welcome Back!
                        </h2>

                        <p>
                            Sign in to continue to your account.
                        </p>

                    </div>


                    <!-- ERROR / SUCCESS -->
                    <?php
                    $errorMessage = $errorMessage
                        ?? session()->getFlashdata('error');
                    ?>

                    <?php if ($errorMessage): ?>

                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-1"></i>

                            <?= esc($errorMessage) ?>
                        </div>

                    <?php endif; ?>


                    <?php if (session()->getFlashdata('success')): ?>

                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-1"></i>

                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>

                    <?php endif; ?>


                    <!-- LOGIN FORM -->
                    <form
                        id="loginForm"
                        action="<?= site_url('login') ?>"
                        method="POST">

                        <?= csrf_field() ?>


                        <!-- EMAIL -->
                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label">

                                Email or Username

                            </label>

                            <div class="input-wrapper">

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Enter your email or username"
                                        autocomplete="email"
                                        required>

                                </div>

                                <div
                                    class="error-text"
                                    id="emailError">
                                </div>

                            </div>

                        </div>


                        <!-- PASSWORD -->
                        <div class="mb-2">

                            <label
                                for="password"
                                class="form-label">

                                Password

                            </label>

                            <div class="input-wrapper">

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Enter your password"
                                        autocomplete="current-password"
                                        required>

                                    <button
                                        type="button"
                                        class="btn password-toggle"
                                        onclick="togglePassword()"
                                        aria-label="Show or hide password">

                                        <i
                                            class="bi bi-eye"
                                            id="eyeIcon">
                                        </i>

                                    </button>

                                </div>

                                <div
                                    class="error-text"
                                    id="passwordError">
                                </div>

                            </div>

                        </div>


                        <!-- REMEMBER / FORGOT -->
                        <div class="login-options">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember"
                                    name="remember"
                                    value="1">

                                <label
                                    class="form-check-label"
                                    for="remember">

                                    Remember Me

                                </label>

                            </div>


                            <a
                                href="<?= site_url('forgot-password') ?>"
                                class="forgot-link">

                                Forgot Password?

                            </a>

                        </div>


                        <!-- LOGIN -->
                        <button
                            type="submit"
                            class="btn login-btn">

                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login

                        </button>

                    </form>



                    <!-- REGISTER -->
                    <div class="register-section">

                        Don't have an account?

                        <a href="<?= site_url('register') ?>">
                            Create Account
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>


    <script>
        function togglePassword() {

            const password =
                document.getElementById("password");

            const eye =
                document.getElementById("eyeIcon");

            if (password.type === "password") {

                password.type = "text";

                eye.classList.remove("bi-eye");
                eye.classList.add("bi-eye-slash");

            } else {

                password.type = "password";

                eye.classList.remove("bi-eye-slash");
                eye.classList.add("bi-eye");

            }
        }
    </script>

    <!-- Keep existing login validation -->
    <script src="<?= base_url('assets/js/login.js') ?>"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>