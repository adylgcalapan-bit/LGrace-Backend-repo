<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;

class AuthController extends BaseController
{
    public function showLogin()
    {
        // Already logged in
        if (session()->get('logged_in')) {

            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            if (session()->get('role') === 'resident') {
                return redirect()->to('/resident/dashboard');
            }
        }

        // =====================================
        // Remember Me automatic login
        // =====================================

        $rawToken = (string) $this->request->getCookie('remember_token');

        if ($rawToken !== '') {

            $db = \Config\Database::connect();

            $tokenHash = hash('sha256', $rawToken);

            $rememberedUser = $db->table('remember_tokens rt')
                ->select('
                rt.remember_id,
                rt.user_id,
                u.full_name,
                u.email,
                u.username,
                u.role
            ')
                ->join(
                    'users u',
                    'u.user_id = rt.user_id',
                    'inner'
                )

                ->where('rt.token_hash', $tokenHash)
                ->where('rt.expires_at > NOW()', null, false)
                ->where('u.is_active', 1)
                ->get()
                ->getRowArray();

            // Valid remember token
            if ($rememberedUser) {

                session()->regenerate(true);

                session()->set([
                    'user_id'       => $rememberedUser['user_id'],
                    'full_name'     => $rememberedUser['full_name'],
                    'email'         => $rememberedUser['email'],
                    'username'      => $rememberedUser['username'],
                    'role'          => $rememberedUser['role'],
                    'logged_in'     => true,
                    'last_activity' => time(),
                ]);

                if ($rememberedUser['role'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                }

                return redirect()->to('/resident/dashboard');
            }

            // Invalid/expired token: remove DB record and cookie
            $db->table('remember_tokens')
                ->where('token_hash', $tokenHash)
                ->delete();

            $response = redirect()->to('/login');
            $response->deleteCookie('remember_token');

            return $response;
        }

        return view('auth/login', [
            'settings' => $this->getSystemSettings()
        ]);
    }
    public function login()
    {
        $userModel = new UserModel();

        $loginIdentifier = trim(
            (string) $this->request->getPost('email')
        );

        $password =
            (string) $this->request->getPost('password');

        $remember =
            $this->request->getPost('remember') === '1';

        // Check if login/password are empty
        if ($loginIdentifier === '' || $password === '') {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter your email or username and password.'
                );
        }

        // Find user by email OR username
        $user = $userModel
            ->groupStart()
            ->where('email', $loginIdentifier)
            ->orWhere('username', $loginIdentifier)
            ->groupEnd()
            ->first();

        // Check account and password
        if (
            !$user ||
            !password_verify(
                $password,
                $user['password']
            )
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid email/username or password.'
                );
        }


        // =====================================
        // RESIDENT ACCOUNT VERIFICATION / STATUS
        // =====================================
        if (
            ($user['role'] ?? '') === 'resident' &&
            (int) ($user['is_active'] ?? 1) !== 1
        ) {

            // Newly created by Admin and still waiting for email verification
            if (empty($user['email_verified_at'])) {

                session()->regenerate(true);

                session()->set([
                    'pending_resident_verification' => true,
                    'pending_resident_user_id'      => (int) $user['user_id'],
                    'pending_resident_email'        => strtolower(
                        trim((string) $user['email'])
                    ),
                    'pending_resident_remember'     => $remember,
                ]);

                return redirect()->to(
                    '/resident/verify-account'
                );
            }

            // Already verified before but manually deactivated by Admin
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Your account is inactive. Please contact the administrator.'
                );
        }

        session()->regenerate(true);

        // Save logged-in user information sa session
        session()->set([
            'user_id'   => $user['user_id'],
            'full_name' => $user['full_name'],
            'email'     => $user['email'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'logged_in' => true,
            'last_activity' => time(),
        ]);

        // =====================================
        // Remember Me
        // =====================================

        $redirectUrl = $user['role'] === 'admin'
            ? '/admin/dashboard'
            : '/resident/dashboard';

        $response = redirect()->to($redirectUrl);

        if ($remember) {

            $db = \Config\Database::connect();

            // Remove old remember tokens for this user
            $db->table('remember_tokens')
                ->where('user_id', $user['user_id'])
                ->delete();

            // Generate secure random token
            $rawToken = bin2hex(random_bytes(32));

            // Only the hashed token is stored in database
            $tokenHash = hash('sha256', $rawToken);

            $expirySeconds = 60 * 60 * 24 * 30; // 30 days

            $db->query(
                'INSERT INTO remember_tokens
        (user_id, token_hash, expires_at)
     VALUES
        (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))',
                [
                    $user['user_id'],
                    $tokenHash
                ]
            );

            $response->setCookie([
                'name'     => 'remember_token',
                'value'    => $rawToken,
                'expire'   => $expirySeconds,
                'path'     => '/',
                'secure'   => false, // localhost uses HTTP
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        return $response;
    }

    public function forgotPassword()
    {
        if (session()->get('logged_in')) {

            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            if (session()->get('role') === 'resident') {
                return redirect()->to('/resident/dashboard');
            }
        }

        return view('auth/forgot-password');
    }

    public function sendResetLink()
    {
        $emailAddress = trim((string) $this->request->getPost('email'));

        // Validate email
        if ($emailAddress === '' || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid email address.');
        }

        $userModel = new UserModel();

        $user = $userModel
            ->where('email', $emailAddress)
            ->first();









        /*
     * Generic success message.
     * Dili nato ipahibalo kung registered ba ang email
     * para mas secure against account enumeration.
     */
        $genericMessage =
            'If an account exists with that email address, a password reset link has been sent.';

        if (!$user) {
            return redirect()->to('/forgot-password')
                ->with('success', $genericMessage);
        }

        $db = \Config\Database::connect();

        // Remove previous reset tokens sa same user
        $db->table('password_reset_tokens')
            ->where('user_id', $user['user_id'])
            ->delete();

        // Generate secure random token
        $rawToken = bin2hex(random_bytes(32));

        // Hash only ang i-store sa database
        $tokenHash = hash('sha256', $rawToken);

        // Token valid for 30 minutes
        $db->query(
            'INSERT INTO password_reset_tokens
            (user_id, token_hash, expires_at)
         VALUES
            (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))',
            [
                $user['user_id'],
                $tokenHash,
            ]
        );

        // Actual reset link
        $resetLink = site_url('reset-password/' . $rawToken);

        // Email service
        $emailService = service('email');

        $emailConfig = config('Email');

        $emailService->setFrom(
            $emailConfig->fromEmail,
            $emailConfig->fromName
        );

        $emailService->setTo($user['email']);

        $emailService->setSubject(
            'Reset Your Password - Community Visibility System'
        );

        $message = '
        <div style="font-family: Arial, sans-serif; line-height: 1.6;">
            <h2>Password Reset Request</h2>

            <p>Hello ' . esc($user['full_name']) . ',</p>

            <p>
                We received a request to reset the password
                for your Community Visibility System account.
            </p>

            <p>
                Click the button below to create a new password:
            </p>

            <p>
                <a href="' . esc($resetLink) . '"
                   style="
                       display: inline-block;
                       padding: 12px 20px;
                       background-color: #198754;
                       color: white;
                       text-decoration: none;
                       border-radius: 5px;
                   ">
                    Reset Password
                </a>
            </p>

            <p>
                This password reset link will expire in
                <strong>30 minutes</strong>.
            </p>
<p>
    If you did not request a password reset,
    you can safely ignore this email.
</p>

<hr>

<small>
    Community Visibility System
</small>

<p style="
    font-size: 12px;
    color: #6c757d;
    margin-top: 20px;
">
    This is an automated message from the Community Visibility System.
    Please do not reply to this email.
</p>

</div>
';
        $emailService->setMessage($message);

        // Send email
        if (!$emailService->send()) {

            // Delete token if email failed
            $db->table('password_reset_tokens')
                ->where('token_hash', $tokenHash)
                ->delete();

            log_message(
                'error',
                'Password reset email failed for user ID: '
                    . $user['user_id']
            );

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to send the reset email right now. Please try again later.'
                );
        }

        return redirect()->to('/forgot-password')
            ->with('success', $genericMessage);
    }


    public function resetPassword($rawToken = null)
    {
        // No token supplied
        if (!$rawToken) {
            return view('auth/forgot-password', [
                'errorMessage' => 'Invalid password reset link.',
            ]);
        }

        $db = \Config\Database::connect();

        $tokenHash = hash('sha256', $rawToken);

        // Check if token exists, is not expired, and has not been used
        $resetToken = $db->table('password_reset_tokens')
            ->where('token_hash', $tokenHash)
            ->where('expires_at > NOW()', null, false)
            ->where('used_at', null)
            ->get()
            ->getRowArray();

        // Used, expired, or invalid token
        if (!$resetToken) {
            return view('auth/forgot-password', [
                'errorMessage' => 'This password reset link is invalid or has expired.',
            ]);
        }

        // Valid token
        return view('auth/reset-password', [
            'token' => $rawToken,
        ]);
    }

    public function updatePassword($rawToken = null)
    {
        if (!$rawToken) {
            return redirect()->to('/forgot-password')
                ->with('error', 'Invalid password reset link.');
        }

        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        if ($password === '' || $confirmPassword === '') {
            return redirect()->back()
                ->with('error', 'Please complete both password fields.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()
                ->with('error', 'Password must be at least 8 characters.');
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()
                ->with('error', 'Passwords do not match.');
        }

        $db = \Config\Database::connect();
        $tokenHash = hash('sha256', $rawToken);

        $resetToken = $db->table('password_reset_tokens')
            ->where('token_hash', $tokenHash)
            ->where('expires_at > NOW()', null, false)
            ->where('used_at', null)
            ->get()
            ->getRowArray();

        if (!$resetToken) {
            return redirect()->to('/forgot-password')
                ->with('error', 'This password reset link is invalid or has expired.');
        }

        $db->transStart();

        $db->table('users')
            ->where('user_id', $resetToken['user_id'])
            ->update([
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $db->table('password_reset_tokens')
            ->where('token_hash', $tokenHash)
            ->update([
                'used_at' => date('Y-m-d H:i:s'),
            ]);

        // Logout remembered sessions after password change.
        $db->table('remember_tokens')
            ->where('user_id', $resetToken['user_id'])
            ->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->with('error', 'Unable to reset the password. Please try again.');
        }



        return redirect()->to('/login')
            ->with('success', 'Password reset successfully. You can now log in.');
    }

    public function sendVerificationCode()
    {
        $email = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                ]);
        }

        $userModel = new UserModel();

        if ($userModel->where('email', $email)->first()) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already registered.',
                ]);
        }

        $db = \Config\Database::connect();

        $existing = $db->table('email_verifications')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        // Prevent repeated requests within 60 seconds
        if (!empty($existing['updated_at'])) {
            $lastSent = strtotime($existing['updated_at']);

            if ($lastSent !== false && (time() - $lastSent) < 60) {
                $remaining = 60 - (time() - $lastSent);

                return $this->response
                    ->setStatusCode(429)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Please wait ' . $remaining
                            . ' seconds before requesting another code.',
                    ]);
            }
        }

        $verificationCode = (string) random_int(100000, 999999);

        // Never store the actual OTP in the database.
        $codeHash = hash('sha256', $verificationCode);

        $now = date('Y-m-d H:i:s');
        $expiresAt = date(
            'Y-m-d H:i:s',
            time() + (10 * 60)
        );

        $verificationData = [
            'code_hash'   => $codeHash,
            'expires_at'  => $expiresAt,
            'attempts'    => 0,
            'verified_at' => null,
            'updated_at'  => $now,
        ];

        if ($existing) {
            $saved = $db->table('email_verifications')
                ->where('email', $email)
                ->update($verificationData);
        } else {
            $verificationData['email'] = $email;
            $verificationData['created_at'] = $now;

            $saved = $db->table('email_verifications')
                ->insert($verificationData);
        }

        if (!$saved) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create verification code.',
                ]);
        }

        try {
            $emailService = service('email');
            $emailService->clear(true);

            $emailConfig = config('Email');

            $emailService->setFrom(
                $emailConfig->fromEmail,
                $emailConfig->fromName
            );

            $emailService->setTo($email);

            $emailService->setSubject(
                'Verify Your Email - Community Visibility System'
            );

            $message = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                <h2>Email Verification</h2>

                <p>
                    Use the verification code below to continue
                    creating your Community Visibility System account.
                </p>

                <div style="
                    font-size: 30px;
                    font-weight: bold;
                    letter-spacing: 8px;
                    margin: 24px 0;
                ">
                    ' . esc($verificationCode) . '
                </div>

                <p>
                    This code will expire in
                    <strong>10 minutes</strong>.
                </p>

              <p>
    If you did not request this code,
    you can safely ignore this email.
</p>

<hr>

<small>
    Barangay Saguing Community Visibility System
</small>

<p style="
    font-size: 12px;
    color: #6c757d;
    margin-top: 20px;
">
    This is an automated message from the Community Visibility System.
    Please do not reply to this email.
</p>

</div>
';

            $emailService->setMessage($message);

            if (!$emailService->send()) {
                // Don't leave an unusable OTP behind.
                $db->table('email_verifications')
                    ->where('email', $email)
                    ->delete();

                log_message(
                    'error',
                    'Registration email verification failed for: {email}',
                    ['email' => $email]
                );

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Unable to send the verification email. Please try again.',
                    ]);
            }
        } catch (\Throwable $e) {
            $db->table('email_verifications')
                ->where('email', $email)
                ->delete();

            log_message(
                'error',
                'Registration verification email error: {message}',
                ['message' => $e->getMessage()]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to send the verification email. Please try again.',
                ]);
        }

        // Any new OTP invalidates an earlier verified session.
        session()->remove('verified_registration_email');

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Verification code sent. Please check your email.',
            'csrfHash' => csrf_hash(),
        ]);
    }


    public function verifyEmailCode()
    {
        $email = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        $code = trim(
            (string) $this->request->getPost('code')
        );

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                ]);
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter the 6-digit verification code.',
                ]);
        }

        $db = \Config\Database::connect();

        $verification = $db->table('email_verifications')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if (!$verification) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'No verification request was found for this email.',
                ]);
        }

        if ((int) $verification['attempts'] >= 5) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON([
                    'success' => false,
                    'message' => 'Too many incorrect attempts. Please request a new code.',
                ]);
        }

        $expiresAt = strtotime(
            (string) $verification['expires_at']
        );

        if ($expiresAt === false || time() > $expiresAt) {
            return $this->response
                ->setStatusCode(410)
                ->setJSON([
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new code.',
                ]);
        }

        $submittedHash = hash('sha256', $code);

        if (!hash_equals(
            (string) $verification['code_hash'],
            $submittedHash
        )) {
            $newAttempts =
                (int) $verification['attempts'] + 1;

            $db->table('email_verifications')
                ->where(
                    'verification_id',
                    $verification['verification_id']
                )
                ->update([
                    'attempts'   => $newAttempts,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Incorrect verification code.',
                ]);
        }

        $verifiedAt = date('Y-m-d H:i:s');

        $db->table('email_verifications')
            ->where(
                'verification_id',
                $verification['verification_id']
            )
            ->update([
                'verified_at' => $verifiedAt,
                'updated_at'  => $verifiedAt,
            ]);

        /*
     * This session value is important.
     * Later, registerSubmit() will require the exact same
     * email before allowing account creation.
     */
        session()->set(
            'verified_registration_email',
            $email
        );

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Email verified successfully.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function register()
    {
        if (session()->get('logged_in')) {

            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            if (session()->get('role') === 'resident') {
                return redirect()->to('/resident/dashboard');
            }
        }

        $db = \Config\Database::connect();

        $puroks = $db->table('puroks')
            ->select('purok_id, purok_name')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('auth/register', [
            'puroks' => $puroks,
        ]);
    }

    public function registerSubmit()
    {
        $userModel = new UserModel();

        $fullName = trim((string) $this->request->getPost('fullName'));
        $email = trim((string) $this->request->getPost('email'));
        $mobileNumber = trim((string) $this->request->getPost('mobileNumber'));
        $username = trim((string) $this->request->getPost('registerUsername'));
        $address = trim((string) $this->request->getPost('address'));
        $purokId = (int) $this->request->getPost('purok_id');
        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('confirmPassword');

        if (
            $fullName === '' ||
            $email === '' ||
            $username === '' ||
            $purokId <= 0 ||
            $password === '' ||
            $address === '' ||
            $confirmPassword === ''
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please complete all required fields.');
        }

        $db = \Config\Database::connect();

        $selectedPurok = $db->table('puroks')
            ->select('purok_id')
            ->where('purok_id', $purokId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$selectedPurok) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select a valid Purok.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid email address.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Password must be at least 8 characters.');
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Passwords do not match.');
        }

        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email address is already registered.');
        }

        if ($userModel->where('username', $username)->first()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username is already taken.');
        }

        $normalizedEmail = strtolower(trim($email));

        $verifiedRegistrationEmail = strtolower(trim(
            (string) session()->get('verified_registration_email')
        ));

        if (
            $verifiedRegistrationEmail === '' ||
            !hash_equals($verifiedRegistrationEmail, $normalizedEmail)
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please verify your email address before creating your account.'
                );
        }

        $verificationRecord = $db->table('email_verifications')
            ->where('email', $normalizedEmail)
            ->where('verified_at IS NOT NULL', null, false)
            ->get()
            ->getRowArray();

        if (!$verificationRecord) {
            session()->remove('verified_registration_email');

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Your email verification is no longer valid. Please verify your email again.'
                );
        }

        // =====================================
        // OPTIONAL PROFILE PICTURE
        // =====================================

        $profileImage = $this->request->getFile('profileImage');

        $profileImagePath = null;
        $newPhysicalPath = null;

        $hasProfileImage =
            $profileImage !== null &&
            $profileImage->getError() !== UPLOAD_ERR_NO_FILE;

        if ($hasProfileImage) {

            if (! $profileImage->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to upload the profile picture.');
            }

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (! in_array(
                $profileImage->getMimeType(),
                $allowedMimeTypes,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Profile picture must be JPG, PNG, or WebP.'
                    );
            }

            // Maximum 5 MB
            if ($profileImage->getSize() > (5 * 1024 * 1024)) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Profile picture must not exceed 5 MB.'
                    );
            }

            $uploadDirectory = FCPATH . 'uploads/profiles';

            if (! is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0775, true);
            }

            $newName = $profileImage->getRandomName();

            try {
                $profileImage->move(
                    $uploadDirectory,
                    $newName
                );
            } catch (\Throwable $e) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Unable to save the profile picture.'
                    );
            }

            $profileImagePath =
                'uploads/profiles/' . $newName;

            $newPhysicalPath =
                FCPATH . $profileImagePath;
        }

        $inserted = $userModel->insert([
            'full_name' => $fullName,
            'email' => $normalizedEmail,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'username' => $username,
            'address' => $address !== '' ? $address : null,
            'purok_id' => $purokId,
            'profile_image' => $profileImagePath,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'resident',
        ]);

        if ($inserted === false) {

            if (
                $newPhysicalPath &&
                is_file($newPhysicalPath)
            ) {
                unlink($newPhysicalPath);
            }

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create your account. Please try again.'
                );
        }

        $settings = $this->getSystemSettings();

        if ((int) ($settings['registration_notifications'] ?? 1) === 1) {

            try {
                $db = \Config\Database::connect();

                $notificationModel = new NotificationModel();

                $admins = $db->table('users')
                    ->select('user_id')
                    ->where('role', 'admin')
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();

                foreach ($admins as $admin) {

                    $adminId = (int) ($admin['user_id'] ?? 0);

                    if ($adminId <= 0) {
                        continue;
                    }

                    $notificationModel->insert([
                        'user_id' => $adminId,
                        'related_user_id' => (int) $inserted,
                        'message' => 'New resident registered: ' . $fullName,
                        'status'  => 'Unread',
                        'date'    => gmdate('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Unable to create admin registration notification: {message}',
                    ['message' => $e->getMessage()]
                );
            }
        }

        $db->table('email_verifications')
            ->where('email', $normalizedEmail)
            ->delete();

        session()->remove('verified_registration_email');

        return redirect()->to('/login')
            ->with('success', 'Account created successfully. You can now log in.');
    }

    public function showResidentVerification()
    {
        if (!session()->get('pending_resident_verification')) {
            return redirect()->to('/login');
        }

        $email = (string) session()->get('pending_resident_email');

        if ($email === '') {
            session()->remove([
                'pending_resident_verification',
                'pending_resident_user_id',
                'pending_resident_email',
                'pending_resident_remember',
            ]);

            return redirect()->to('/login')
                ->with('error', 'Verification session expired. Please log in again.');
        }

        return view('auth/resident-verify-account', [
            'email' => $email,
        ]);
    }


    public function verifyResidentAccount()
    {
        if (!session()->get('pending_resident_verification')) {
            return redirect()->to('/login')
                ->with('error', 'Verification session expired. Please log in again.');
        }

        $db = \Config\Database::connect();

        $userId = (int) session()->get('pending_resident_user_id');

        $email = strtolower(trim(
            (string) session()->get('pending_resident_email')
        ));

        $remember = (bool) session()->get('pending_resident_remember');

        $code = trim(
            (string) $this->request->getPost('code')
        );

        if ($userId <= 0 || $email === '') {
            session()->remove([
                'pending_resident_verification',
                'pending_resident_user_id',
                'pending_resident_email',
                'pending_resident_remember',
            ]);

            return redirect()->to('/login')
                ->with('error', 'Verification session expired. Please log in again.');
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter the 6-digit verification code.'
                );
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('email', $email)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            session()->remove([
                'pending_resident_verification',
                'pending_resident_user_id',
                'pending_resident_email',
                'pending_resident_remember',
            ]);

            return redirect()->to('/login')
                ->with('error', 'Resident account not found.');
        }

        // If already verified, do not reuse an old OTP.
        if (!empty($resident['email_verified_at'])) {
            return redirect()->to('/login')
                ->with(
                    'error',
                    'This resident account has already been verified.'
                );
        }

        $verification = $db->table('email_verifications')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if (!$verification) {
            return redirect()->back()
                ->with(
                    'error',
                    'No verification code was found. Please contact the administrator.'
                );
        }

        if ((int) ($verification['attempts'] ?? 0) >= 5) {
            return redirect()->back()
                ->with(
                    'error',
                    'Too many incorrect attempts. Please contact the administrator for a new code.'
                );
        }

        $expiresAt = strtotime(
            (string) ($verification['expires_at'] ?? '')
        );

        if ($expiresAt === false || time() > $expiresAt) {
            return redirect()->back()
                ->with(
                    'error',
                    'Verification code has expired. Please contact the administrator for a new code.'
                );
        }

        $submittedHash = hash('sha256', $code);

        if (!hash_equals(
            (string) $verification['code_hash'],
            $submittedHash
        )) {
            $newAttempts =
                (int) ($verification['attempts'] ?? 0) + 1;

            $db->table('email_verifications')
                ->where(
                    'verification_id',
                    $verification['verification_id']
                )
                ->update([
                    'attempts'   => $newAttempts,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return redirect()->back()
                ->with(
                    'error',
                    'Incorrect verification code.'
                );
        }

        $verifiedAt = date('Y-m-d H:i:s');

        $db->transStart();

        // Activate resident and mark email as verified
        $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->update([
                'is_active'         => 1,
                'email_verified_at' => $verifiedAt,
                'updated_at'        => $verifiedAt,
            ]);

        // Mark OTP verified
        $db->table('email_verifications')
            ->where(
                'verification_id',
                $verification['verification_id']
            )
            ->update([
                'verified_at' => $verifiedAt,
                'updated_at'  => $verifiedAt,
            ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()
                ->with(
                    'error',
                    'Unable to activate your account. Please try again.'
                );
        }

        // Remove OTP record after successful activation
        $db->table('email_verifications')
            ->where('email', $email)
            ->delete();

        // Refresh resident data
        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/login')
                ->with('error', 'Unable to load the resident account.');
        }

        session()->regenerate(true);

        session()->remove([
            'pending_resident_verification',
            'pending_resident_user_id',
            'pending_resident_email',
            'pending_resident_remember',
        ]);

        session()->set([
            'user_id'       => $resident['user_id'],
            'full_name'     => $resident['full_name'],
            'email'         => $resident['email'],
            'username'      => $resident['username'],
            'role'          => $resident['role'],
            'logged_in'     => true,
            'last_activity' => time(),
        ]);

        // Preserve Remember Me choice from login
        if ($remember) {
            $db->table('remember_tokens')
                ->where('user_id', $resident['user_id'])
                ->delete();

            $rawToken = bin2hex(random_bytes(32));

            $tokenHash = hash('sha256', $rawToken);

            $db->query(
                'INSERT INTO remember_tokens
            (user_id, token_hash, expires_at)
            VALUES
            (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))',
                [
                    $resident['user_id'],
                    $tokenHash,
                ]
            );

            $response = redirect()->to('/resident/dashboard');

            $response->setCookie(
                'remember_token',
                $rawToken,
                60 * 60 * 24 * 30,
                '',
                '',
                false,
                true
            );

            return $response->with(
                'success',
                'Email verified successfully. Your resident account is now active.'
            );
        }

        return redirect()->to('/resident/dashboard')
            ->with(
                'success',
                'Email verified successfully. Your resident account is now active.'
            );
    }

    public function resendResidentVerificationCode()
    {
        if (!session()->get('pending_resident_verification')) {
            return redirect()->to('/login')
                ->with(
                    'error',
                    'Verification session expired. Please log in again.'
                );
        }

        $db = \Config\Database::connect();

        $userId = (int) session()->get('pending_resident_user_id');

        $email = strtolower(trim(
            (string) session()->get('pending_resident_email')
        ));

        if ($userId <= 0 || $email === '') {
            session()->remove([
                'pending_resident_verification',
                'pending_resident_user_id',
                'pending_resident_email',
                'pending_resident_remember',
            ]);

            return redirect()->to('/login')
                ->with(
                    'error',
                    'Verification session expired. Please log in again.'
                );
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('email', $email)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/login')
                ->with(
                    'error',
                    'Resident account not found.'
                );
        }

        if (!empty($resident['email_verified_at'])) {
            return redirect()->to('/login')
                ->with(
                    'error',
                    'This resident account has already been verified.'
                );
        }

        $existingVerification = $db->table('email_verifications')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        // 60-second resend cooldown
        if (!empty($existingVerification['updated_at'])) {

            $lastSent = strtotime(
                (string) $existingVerification['updated_at']
            );

            if (
                $lastSent !== false &&
                (time() - $lastSent) < 60
            ) {
                $remaining =
                    60 - (time() - $lastSent);

                return redirect()->back()
                    ->with(
                        'error',
                        'Please wait ' . $remaining .
                            ' seconds before requesting another code.'
                    );
            }
        }

        $verificationCode =
            (string) random_int(100000, 999999);

        $codeHash =
            hash('sha256', $verificationCode);

        $now =
            date('Y-m-d H:i:s');

        $expiresAt =
            date(
                'Y-m-d H:i:s',
                time() + (10 * 60)
            );

        $verificationData = [
            'code_hash'   => $codeHash,
            'expires_at'  => $expiresAt,
            'attempts'    => 0,
            'verified_at' => null,
            'updated_at'  => $now,
        ];

        if ($existingVerification) {

            $saved = $db->table('email_verifications')
                ->where('email', $email)
                ->update($verificationData);
        } else {

            $verificationData['email'] =
                $email;

            $verificationData['created_at'] =
                $now;

            $saved = $db->table('email_verifications')
                ->insert($verificationData);
        }

        if (!$saved) {
            return redirect()->back()
                ->with(
                    'error',
                    'Unable to create a new verification code.'
                );
        }

        try {

            $emailService = service('email');

            $emailService->clear(true);

            $emailConfig = config('Email');

            $emailService->setFrom(
                $emailConfig->fromEmail,
                $emailConfig->fromName
            );

            $emailService->setTo($email);

            $emailService->setSubject(
                'New Resident Verification Code - Community Visibility System'
            );

            $message = '
            <div style="
                font-family: Arial, sans-serif;
                line-height: 1.6;
            ">

                <h2>Resident Account Verification</h2>

                <p>
                    Here is your new verification code:
                </p>

                <div style="
                    font-size: 30px;
                    font-weight: bold;
                    letter-spacing: 8px;
                    margin: 24px 0;
                ">
                    ' . esc($verificationCode) . '
                </div>

                <p>
                    This code will expire in
                    <strong>10 minutes</strong>.
                </p>

             <p>
    Enter this code on the resident
    verification page to activate
    your account.
</p>

<hr>

<small>
    Barangay Saguing Community Visibility System
</small>

<p style="
    font-size: 12px;
    color: #6c757d;
    margin-top: 20px;
">
    This is an automated message from the Community Visibility System.
    Please do not reply to this email.
</p>

</div>
';

            $emailService->setMessage($message);

            if (!$emailService->send()) {
                throw new \RuntimeException(
                    'Unable to send verification email.'
                );
            }
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Resident OTP resend failed: {message}',
                [
                    'message' => $e->getMessage()
                ]
            );

            return redirect()->back()
                ->with(
                    'error',
                    'Unable to send the verification code. Please try again.'
                );
        }

        return redirect()->back()
            ->with(
                'success',
                'A new verification code was sent to your email.'
            );
    }

    public function logout()
    {
        $rawToken = (string) $this->request->getCookie('remember_token');

        // Remove Remember Me token from database
        if ($rawToken !== '') {

            $db = \Config\Database::connect();

            $tokenHash = hash('sha256', $rawToken);

            $db->table('remember_tokens')
                ->where('token_hash', $tokenHash)
                ->delete();
        }

        // Destroy normal login session
        session()->destroy();

        // Delete Remember Me browser cookie
        $response = redirect()->to('/login');
        $response->deleteCookie('remember_token');

        return $response;
    }
}
