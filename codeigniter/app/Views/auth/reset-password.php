<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Reset Password | Community Visibility System
    </title>

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

                            <i class="bi bi-shield-lock fs-1 text-success"></i>

                            <h2 class="mt-2">
                                Reset Password
                            </h2>

                            <p class="text-muted">
                                Enter your new password below.
                            </p>

                        </div>

                        <?php if (session()->getFlashdata('error')): ?>

                            <div class="alert alert-danger">

                                <?= esc(session()->getFlashdata('error')) ?>

                            </div>

                        <?php endif; ?>

                        <form
                            action="<?= site_url('reset-password/' . ($token ?? '')) ?>"
                            method="POST">

                            <?= csrf_field() ?>

                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label">

                                    New Password

                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    minlength="8"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label
                                    for="confirm_password"
                                    class="form-label">

                                    Confirm New Password

                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirm_password"
                                    name="confirm_password"
                                    minlength="8"
                                    required>

                            </div>

                            <button
                                type="submit"
                                class="btn btn-success w-100">

                                <i class="bi bi-check-circle"></i>
                                Reset Password

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>