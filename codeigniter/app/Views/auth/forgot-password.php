<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Forgot Password | Community Visibility System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">

                            <i class="bi bi-key fs-1 text-success"></i>

                            <h2 class="mt-2">
                                Forgot Password?
                            </h2>

                            <p class="text-muted">
                                Enter the email address associated with your account.
                                We will send you a password reset link.
                            </p>

                        </div>
                        <?php
                        $errorMessage = $errorMessage ?? session()->getFlashdata('error');
                        ?>

                        <?php if (!empty($errorMessage)): ?>

                            <div class="alert alert-danger">
                                <?= esc($errorMessage) ?>
                            </div>

                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>

                            <div class="alert alert-success">

                                <?= esc(session()->getFlashdata('success')) ?>

                            </div>

                        <?php endif; ?>

                        <form
                            action="<?= site_url('forgot-password') ?>"
                            method="POST">

                            <?= csrf_field() ?>

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label">

                                    Email Address

                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= esc(old('email')) ?>"
                                    placeholder="Enter your registered email"
                                    required>

                            </div>

                            <button
                                type="submit"
                                class="btn btn-success w-100">

                                <i class="bi bi-envelope"></i>

                                Send Reset Link

                            </button>

                        </form>

                        <div class="text-center mt-3">

                            <a
                                href="<?= site_url('login') ?>"
                                class="text-decoration-none">

                                <i class="bi bi-arrow-left"></i>

                                Back to Login

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>