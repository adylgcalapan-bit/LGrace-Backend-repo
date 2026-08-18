<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Community Visibility System | Register</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>">

</head>

<body>

<div class="container-fluid">

    <div class="row min-vh-100">

        <!-- LEFT PANEL -->
        <div class="col-lg-5 left-panel d-none d-lg-flex">

            <div class="text-center">

                <img src="<?= base_url('assets/images/logo.jpg') ?>"
                     class="logo mb-4"
                     alt="Barangay Logo">

                <h1>Community Visibility System</h1>

                <p>
                    Register to become part of the
                    Community Visibility System of
                    Barangay Saguing.
                </p>

            </div>

        </div>

        <!-- RIGHT PANEL -->

        <div class="col-lg-7 d-flex align-items-center justify-content-center">

            <div class="register-card">

                <h2>Create an Account</h2>

                <p class="text-muted mb-4">
                    Fill in the information below.
                </p>

               <form id="registerForm" action="<?= site_url('register') ?>" method="POST">
    <?= csrf_field() ?>
                    <!-- Profile -->

                    <div class="text-center mb-4">

                        <img src="<?= base_url('assets/images/logo.jpg') ?>"
                             class="profile-preview"
                             alt="Profile">

                        <label for="profileImage" class="form-label mt-3 mb-2">
                            Profile Picture
                        </label>

                        <input
                            id="profileImage"
                            name="profileImage"
                            type="file"
                            class="form-control"
                            accept="image/*">

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="fullName">
                                Full Name
                            </label>

                            <div class="field-group">
                                <input
                                    id="fullName"
                                    name="fullName"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter full name"
                                    required>
                                <div class="error-text" id="fullNameError"></div>
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="email">
                                Email
                            </label>

                            <div class="field-group">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control"
                                    placeholder="Enter email"
                                    required>
                                <div class="error-text" id="emailError"></div>
                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="mobileNumber">
                                Mobile Number
                            </label>

                            <div class="field-group">
                                <input
                                    id="mobileNumber"
                                    name="mobileNumber"
                                    type="text"
                                    class="form-control"
                                    placeholder="09XXXXXXXXX"
                                    required>
                                <div class="error-text" id="mobileNumberError"></div>
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="registerUsername">
                                Username
                            </label>

                            <div class="field-group">
                                <input
                                    id="registerUsername"
                                    name="registerUsername"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter username"
                                    required>
                                <div class="error-text" id="usernameError"></div>
                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="password">
                                Password
                            </label>

                            <div class="field-group">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control"
                                    placeholder="Enter password"
                                    required>
                                <div class="error-text" id="passwordError"></div>
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label" for="confirmPassword">
                                Confirm Password
                            </label>

                            <div class="field-group">
                                <input
                                    id="confirmPassword"
                                    name="confirmPassword"
                                    type="password"
                                    class="form-control"
                                    placeholder="Confirm password"
                                    required>
                                <div class="error-text" id="confirmPasswordError"></div>
                            </div>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label" for="address">
                            Address
                        </label>

                        <div class="field-group">
                            <textarea
                                id="address"
                                name="address"
                                class="form-control"
                                rows="3"
                                placeholder="Complete Address"
                                required></textarea>
                            <div class="error-text" id="addressError"></div>
                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label" for="role">
                            Register As
                        </label>

                        <select id="role" name="role" class="form-select">

                            <option selected>
                                Resident
                            </option>

                            <option disabled>
                                Admin
                            </option>

                        </select>

                        <small class="text-muted">
                            Admin registration is only available on the Barangay computer.
                        </small>

                    </div>

                    <div id="formMessage" class="form-message" aria-live="polite"></div>

                    <button
                        type="submit"
                        class="btn register-btn w-100">

                        Create Account

                    </button>

                </form>

                <div class="text-center mt-4">

                    Already have an account?

                    <a href="<?= site_url('login') ?>">

                        Login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="<?= base_url('assets/js/register.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
