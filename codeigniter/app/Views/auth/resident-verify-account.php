<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify Resident Account</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f5f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-card {
            width: 100%;
            max-width: 460px;
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .otp-input {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 10px;
            text-align: center;
        }

        .email-box {
            background: #f1f3f2;
            border-radius: 10px;
            padding: 10px 12px;
            word-break: break-word;
        }
    </style>
</head>

<body>

    <div class="card verify-card">
        <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
                <h3 class="fw-bold mb-2">
                    Verify Your Account
                </h3>

                <p class="text-muted mb-0">
                    Enter the 6-digit verification code sent to your email.
                </p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <small class="text-muted">
                    Verification email
                </small>

                <div class="email-box mt-1">
                    <?= esc($email ?? '') ?>
                </div>
            </div>

            <form
                action="<?= site_url('resident/verify-account') ?>"
                method="post">

                <?= csrf_field() ?>

                <div class="mb-4">
                    <label
                        for="verificationCode"
                        class="form-label">
                        Verification Code
                    </label>

                    <input
                        type="text"
                        class="form-control otp-input"
                        id="verificationCode"
                        name="code"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        placeholder="000000"
                        required
                        autofocus>
                </div>

                <button
                    type="submit"
                    class="btn btn-success w-100 py-2">
                    Verify and Activate Account
                </button>

            </form>

            <form
                action="<?= site_url('resident/verify-account/resend') ?>"
                method="post"
                class="mt-3">
                <?= csrf_field() ?>

                <button
                    type="submit"
                    class="btn btn-outline-secondary w-100">
                    Resend Verification Code
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    The verification code expires after 10 minutes.
                </small>
            </div>

            <div class="text-center mt-3">
                <a
                    href="<?= site_url('login') ?>"
                    class="text-decoration-none">
                    Back to Login
                </a>
            </div>

        </div>
    </div>

</body>

</html>