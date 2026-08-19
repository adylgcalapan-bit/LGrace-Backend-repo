<?php

namespace App\Controllers;

use App\Models\UserModel;

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

        return view('auth/login');
    }
    public function login()
    {
        $userModel = new UserModel();

        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $remember = $this->request->getPost('remember') === '1';

        // Check if email/password are empty
        if ($email === '' || $password === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter your email and password.');
        }

        // Find user pinaagi sa email
        $user = $userModel->where('email', $email)->first();

        // Check user and password
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }


        // Block inactive resident accounts
        if (
            ($user['role'] ?? '') === 'resident' &&
            (int) ($user['is_active'] ?? 1) !== 1
        ) {
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

        $userModel->insert([
            'full_name' => $fullName,
            'email' => $email,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'username' => $username,
            'address' => $address !== '' ? $address : null,
            'purok_id' => $purokId,
            'profile_image' => null,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'resident',
        ]);

        return redirect()->to('/login')
            ->with('success', 'Account created successfully. You can now log in.');
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
