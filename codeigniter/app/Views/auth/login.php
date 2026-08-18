<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Community Visibility System | Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>

<body>

<div class="container-fluid">

    <div class="row min-vh-100">

        <!-- LEFT SIDE -->
        <div class="col-lg-6 left-panel d-none d-lg-flex">

            <div class="text-center">

                <img src="<?= base_url('assets/images/logo.jpg') ?>"
                     class="logo mb-4"
                     alt="Barangay Logo">

                <p>
                    Web-Based Community Problems Visibility System
                    with Location Feature for Barangay Saguing.
                </p>

                

            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">

            <div class="login-card">

                <h2 class="mb-2">Welcome Back!</h2>

                <p class="text-muted mb-4">
                    Sign in to continue.
                </p>

               <form id="loginForm" action="<?= site_url('login') ?>" method="POST">
    <?= csrf_field() ?>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <div class="input-wrapper">

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-person"></i>

                                </span>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                     class="form-control"
                                    placeholder="Enter email"
                                    autocomplete="email"
                                    required>
                            </div>

                            <div class="error-text" id="emailError"></div>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <div class="input-wrapper">

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-lock"></i>

                                </span>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="Enter password"
                                    autocomplete="current-password"
                                    required>

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    onclick="togglePassword()">

                                    <i class="bi bi-eye" id="eyeIcon"></i>

                                </button>

                            </div>

                            <div class="error-text" id="passwordError"></div>

                        </div>

                    </div>

                    <div class="d-flex justify-content-between mb-4">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember">

                            <label
                                class="form-check-label"
                                for="remember">

                                Remember Me

                            </label>

                        </div>

                        <a href="#" class="forgot-link">

                            Forgot Password?

                        </a>

                    </div>

                    <button type="submit" class="btn login-btn w-100">

                        Login

                    </button>

                </form>

                <div class="text-center mt-4">

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

function togglePassword(){
    const password = document.getElementById("password");
    const eye = document.getElementById("eyeIcon");

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
<script src="<?= base_url('assets/js/login.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
