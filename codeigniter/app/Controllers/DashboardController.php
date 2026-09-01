<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    // =========================
    // ADMIN DASHBOARD
    // =========================
    public function admin()
    {
        $db = \Config\Database::connect();

        $settings = $this->getSystemSettings();

        $userId = (int) session()->get('user_id');

        $admin = $db->table('users')
            ->select('
        user_id,
        full_name,
        profile_image,
        role,
        is_active
    ')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        $totalReports = $db->table('reports')
            ->countAllResults();

        $pendingReports = $db->table('reports')
            ->where('status', 'Pending')
            ->countAllResults();

        $progressReports = $db->table('reports')
            ->where('status', 'In Progress')
            ->countAllResults();

        $resolvedReports = $db->table('reports')
            ->where('status', 'Resolved')
            ->countAllResults();

        $rejectedReports = $db->table('reports')
            ->where('status', 'Rejected')
            ->countAllResults();

        $recentReports = $db->table('reports r')
            ->select('
        r.report_id,
        r.is_anonymous,
        r.address,
        r.status,
        r.date_reported,
        u.full_name,
        c.category_name
    ')
            ->join('users u', 'u.user_id = r.user_id', 'left')
            ->join('categories c', 'c.category_id = r.category_id', 'left')
            ->orderBy('r.date_reported', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();


        foreach ($recentReports as &$report) {

            $isAnonymous =
                (int) ($report['is_anonymous'] ?? 0) === 1;

            $realResidentName = trim(
                (string) ($report['full_name'] ?? 'Unknown Resident')
            );

            $displayResidentName = $realResidentName;

            if (
                $isAnonymous &&
                $realResidentName !== 'Unknown Resident'
            ) {

                $nameParts = preg_split(
                    '/\s+/',
                    $realResidentName
                );

                $maskedParts = array_map(
                    function ($part) {

                        if ($part === '') {
                            return '';
                        }

                        return mb_strtoupper(
                            mb_substr($part, 0, 1)
                        ) . '***';
                    },
                    $nameParts
                );

                $displayResidentName = implode(
                    ' ',
                    $maskedParts
                );
            }

            $report['display_resident_name'] =
                $displayResidentName;
        }

        unset($report);

        $mapReports = $db->table('reports r')
            ->select('
        r.report_id,
        r.title,
        r.latitude,
        r.longtitude,
        r.address,
        r.status,
        c.category_name
    ')
            ->join(
                'categories c',
                'c.category_id = r.category_id',
                'left'
            )
            ->where('r.latitude IS NOT NULL', null, false)
            ->where('r.longtitude IS NOT NULL', null, false)
            ->get()
            ->getResultArray();



        return view('admin/dashboard', [
            'settings' => $settings,
            'admin' => $admin,
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'progressReports' => $progressReports,
            'resolvedReports' => $resolvedReports,
            'rejectedReports' => $rejectedReports,
            'recentReports' => $recentReports,
            'mapReports' => $mapReports
        ]);
    }



    // =========================
    // RESIDENT DASHBOARD
    // =========================




    public function residentDetails($userId = null)
    {
        $db = \Config\Database::connect();

        $userId = (int) $userId;

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid resident ID.'
                ]);
        }

        // =========================
        // Resident Information
        // =========================

        $resident = $db->table('users')
            ->select('
    user_id,
    full_name,
    email,
    mobile_number,
    username,
    address,
    profile_image,
    is_active,
    email_verified_at,
    created_at
')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Resident not found.'
                ]);
        }

        // =========================
        // Report Statistics
        // =========================

        $totalReports = $db->table('reports')
            ->where('user_id', $userId)
            ->countAllResults();

        $pendingReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->countAllResults();

        $progressReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'In Progress')
            ->countAllResults();

        $resolvedReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'Resolved')
            ->countAllResults();

        // =========================
        // Recent Reports
        // =========================

        $recentReports = $db->table('reports r')
            ->select('
            r.report_id,
            r.title,
            r.status,
            r.date_reported,
            c.category_name
        ')
            ->join(
                'categories c',
                'c.category_id = r.category_id',
                'left'
            )
            ->where('r.user_id', $userId)
            ->orderBy('r.date_reported', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // =========================
        // Profile Picture
        // =========================

        $resident['image_url'] = !empty($resident['profile_image'])
            ? base_url(ltrim($resident['profile_image'], '/\\'))
            : base_url('assets/images/resident picture.png');

        return $this->response->setJSON([
            'success' => true,
            'date_format' => $this->getSystemSettings()['date_format'] ?? 'MM/DD/YYYY',

            'resident' => [
                'user_id' => $resident['user_id'],
                'resident_id' => 'R-' . str_pad(
                    (string) $resident['user_id'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ),
                'full_name' => $resident['full_name'],
                'username' => $resident['username'],
                'email' => $resident['email'],
                'mobile_number' => $resident['mobile_number'],
                'address' => $resident['address'],
                'image_url' => $resident['image_url'],
                'is_active' => (int) $resident['is_active'],
                'email_verified_at' => $resident['email_verified_at'],
                'created_at' => $resident['created_at']
            ],

            'statistics' => [
                'total' => $totalReports,
                'pending' => $pendingReports,
                'in_progress' => $progressReports,
                'resolved' => $resolvedReports
            ],

            'recent_reports' => $recentReports
        ]);
    }
    public function resident()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Resident not found.'
                ]);
        }

        $totalReports = $db->table('reports')
            ->where('user_id', $userId)
            ->countAllResults();

        $pendingReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->countAllResults();

        $progressReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'In Progress')
            ->countAllResults();

        $resolvedReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'Resolved')
            ->countAllResults();

        $rejectedReports = $db->table('reports')
            ->where('user_id', $userId)
            ->where('status', 'Rejected')
            ->countAllResults();

        $recentReports = $db->table('reports r')
            ->select('
            r.report_id,
            r.title,
            r.status,
            r.date_reported,
            c.category_name
        ')
            ->join(
                'categories c',
                'c.category_id = r.category_id',
                'left'
            )
            ->where('r.user_id', $userId)
            ->orderBy('r.date_reported', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $imageUrl = !empty($resident['profile_image'])
            ? base_url(ltrim($resident['profile_image'], '/\\'))
            : base_url('assets/images/resident picture.png');

        return view('resident/dashboard', [
            'resident' => [
                'user_id'       => $resident['user_id'],
                'resident_id'   => 'R-' . str_pad(
                    (string) $resident['user_id'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ),
                'full_name'     => $resident['full_name'] ?? '',
                'username'      => $resident['username'] ?? '',
                'email'         => $resident['email'] ?? '',
                'mobile_number' => $resident['mobile_number'] ?? '',
                'address'       => $resident['address'] ?? '',
                'image_url'     => $imageUrl,
                'is_active'     => (int) $resident['is_active'],
                'created_at'    => $resident['created_at'] ?? null,
            ],

            'statistics' => [
                'total'       => $totalReports,
                'pending'     => $pendingReports,
                'in_progress' => $progressReports,
                'resolved'    => $resolvedReports,
                'rejected'    => $rejectedReports,
            ],

            'recent_reports' => $recentReports,
        ]);
    }
    public function createResident()
    {
        $db = \Config\Database::connect();
        $userModel = new \App\Models\UserModel();

        $fullName = trim(
            (string) $this->request->getPost('full_name')
        );

        $email = trim(
            (string) $this->request->getPost('email')
        );

        $mobileNumber = trim(
            (string) $this->request->getPost('mobile_number')
        );

        $username = 'resident_' . bin2hex(random_bytes(4));

        $purokId = (int) $this->request->getPost('purok_id');

        $address = trim(
            (string) $this->request->getPost('address')
        );

        $password = (string) $this->request->getPost('password');

        $confirmPassword =
            (string) $this->request->getPost('confirm_password');

        // =========================
        // REQUIRED FIELDS
        // =========================
        if (
            $fullName === '' ||
            $email === '' ||

            $purokId <= 0 ||
            $address === '' ||
            $password === '' ||
            $confirmPassword === ''
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please complete all required resident information.'
                );
        }

        // =========================
        // VALID EMAIL
        // =========================
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter a valid email address.'
                );
        }

        // =========================
        // VALID PUROK
        // =========================
        $purok = $db->table('puroks')
            ->select('purok_id')
            ->where('purok_id', $purokId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please select a valid active Purok.'
                );
        }

        // =========================
        // PASSWORD
        // =========================
        if (strlen($password) < 8) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Password must be at least 8 characters.'
                );
        }

        if ($password !== $confirmPassword) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Passwords do not match.'
                );
        }

        // =========================
        // DUPLICATE EMAIL
        // =========================
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Email address is already registered.'
                );
        }




        // =========================
        // OPTIONAL PROFILE PICTURE
        // =========================
        $profileImage = $this->request->getFile('profile_image');
        $profileImagePath = null;

        $hasProfileImage =
            $profileImage !== null &&
            $profileImage->getError() !== UPLOAD_ERR_NO_FILE;

        if ($hasProfileImage) {

            if (!$profileImage->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to upload the profile picture.');
            }

            // Maximum 2 MB
            if ($profileImage->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Profile picture must not exceed 2 MB.');
            }

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!in_array($profileImage->getMimeType(), $allowedMimeTypes, true)) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Profile picture must be a JPG, PNG, or WebP image.'
                    );
            }

            $uploadDirectory = FCPATH . 'uploads/profiles';

            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
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
                    ->with('error', 'Unable to save the profile picture.');
            }

            $profileImagePath = 'uploads/profiles/' . $newName;
        }


        // =========================
        // CREATE RESIDENT
        // =========================
        $normalizedEmail = strtolower($email);

        $residentId = $userModel->insert([
            'full_name'         => $fullName,
            'email'             => $normalizedEmail,
            'mobile_number'     => $mobileNumber !== ''
                ? $mobileNumber
                : null,
            'username'          => $username,
            'address'           => $address,
            'purok_id'          => $purokId,
            'profile_image'     => $profileImagePath,
            'password'          => password_hash(
                $password,
                PASSWORD_DEFAULT
            ),
            'role'              => 'resident',

            // Admin-created resident must verify email first
            'is_active'         => 0,
            'email_verified_at' => null,
        ]);

        if (!$residentId) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create the resident account.'
                );
        }


        // =========================
        // CREATE EMAIL OTP
        // =========================
        $existingVerification = $db->table('email_verifications')
            ->where('email', $normalizedEmail)
            ->get()
            ->getRowArray();

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

            $verificationSaved =
                $db->table('email_verifications')
                ->where('email', $normalizedEmail)
                ->update($verificationData);
        } else {

            $verificationData['email'] =
                $normalizedEmail;

            $verificationData['created_at'] =
                $now;

            $verificationSaved =
                $db->table('email_verifications')
                ->insert($verificationData);
        }


        // =========================
        // OTP SAVE FAILED
        // =========================
        if (!$verificationSaved) {

            $userModel->delete($residentId);

            if (
                $profileImagePath !== null &&
                is_file(FCPATH . $profileImagePath)
            ) {
                @unlink(FCPATH . $profileImagePath);
            }

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create the email verification code. Please try again.'
                );
        }


        // =========================
        // SEND VERIFICATION EMAIL
        // =========================
        try {

            $emailService = service('email');

            $emailService->clear(true);

            $emailConfig = config('Email');

            $emailService->setFrom(
                $emailConfig->fromEmail,
                $emailConfig->fromName
            );

            $emailService->setTo(
                $normalizedEmail
            );

            $emailService->setSubject(
                'Verify Your Resident Account - Community Visibility System'
            );

            $message = '
        <div style="
            font-family: Arial, sans-serif;
            line-height: 1.6;
        ">

            <h2>Resident Account Verification</h2>

            <p>
                An administrator of Barangay Saguing
                created a resident account for you in the
                Community Visibility System.
            </p>

            <p>
                Your verification code is:
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
                This verification code will expire in
                <strong>10 minutes</strong>.
            </p>

            <p>
                To activate your account, open the
                Community Visibility System and log in
                using the account credentials provided
                by the administrator.
            </p>

            <p>
                After entering the correct username/email
                and password, you will be asked to enter
                this verification code.
            </p>

           <p>
    If you were not expecting this account,
    please contact the Barangay administrator.
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

            // Delete unused OTP
            $db->table('email_verifications')
                ->where('email', $normalizedEmail)
                ->delete();

            // Delete incomplete resident account
            $userModel->delete($residentId);

            // Delete uploaded profile picture
            if (
                $profileImagePath !== null &&
                is_file(FCPATH . $profileImagePath)
            ) {
                @unlink(FCPATH . $profileImagePath);
            }

            log_message(
                'error',
                'Admin-created resident verification email failed: {message}',
                [
                    'message' => $e->getMessage()
                ]
            );

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Resident account was not created because the verification email could not be sent. Please try again.'
                );
        }


        // =========================
        // SUCCESS
        // =========================
        return redirect()->to('/admin/residents')
            ->with(
                'success',
                'Resident account created successfully. A verification code was sent to the resident email. The account will remain inactive until email verification is completed.'
            );
    }

    public function resendResidentVerificationCode($userId = null)
    {
        $db = \Config\Database::connect();

        $userId = (int) $userId;

        if ($userId <= 0) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Invalid resident account.'
                );
        }

        // ==========================================
        // FIND RESIDENT
        // ==========================================

        $resident = $db->table('users')
            ->select(
                'user_id, full_name, email, role, is_active, email_verified_at'
            )
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Resident account not found.'
                );
        }

        // Only pending verification residents
        if (
            (int) ($resident['is_active'] ?? 0) === 1 ||
            !empty($resident['email_verified_at'])
        ) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'A verification code can only be resent to a resident who is still pending verification.'
                );
        }

        $email = strtolower(
            trim((string) ($resident['email'] ?? ''))
        );

        if ($email === '') {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'This resident does not have a valid email address.'
                );
        }

        // ==========================================
        // EXISTING OTP
        // ==========================================

        $existingVerification = $db->table('email_verifications')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        // 60-second cooldown
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

                return redirect()->to('/admin/residents')
                    ->with(
                        'error',
                        'Please wait ' . $remaining .
                            ' seconds before resending another verification code.'
                    );
            }
        }

        // ==========================================
        // CREATE NEW OTP
        // ==========================================

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
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Unable to create a new verification code.'
                );
        }

        // ==========================================
        // SEND OTP EMAIL
        // ==========================================

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

                <p>Hello ' .
                esc((string) ($resident['full_name'] ?? 'Resident')) .
                ',</p>

                <p>
                    The administrator requested a new
                    verification code for your resident account.
                </p>

                <p>
                    Your new verification code is:
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
                    Log in using your resident credentials
                    and enter this code on the verification
                    page to activate your account.
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
                'Admin resident OTP resend failed: {message}',
                [
                    'message' => $e->getMessage()
                ]
            );

            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Unable to send the verification code. Please try again.'
                );
        }

        return redirect()->to('/admin/residents')
            ->with(
                'success',
                'A new verification code was sent to ' .
                    $email . '.'
            );
    }

    public function deletePendingResident($userId = null)
    {
        $db = \Config\Database::connect();

        $userId = (int) $userId;

        if ($userId <= 0) {
            return redirect()->to('/admin/residents')
                ->with('error', 'Invalid resident account.');
        }

        $resident = $db->table('users')
            ->select(
                'user_id, full_name, email, profile_image, is_active, email_verified_at'
            )
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/admin/residents')
                ->with('error', 'Resident account not found.');
        }

        $isPendingVerification =
            (int) ($resident['is_active'] ?? 0) === 0 &&
            empty($resident['email_verified_at']);

        if (!$isPendingVerification) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Only residents with Pending Verification status can be deleted here.'
                );
        }

        $db->transStart();

        // Remove unused verification code
        $db->table('email_verifications')
            ->where('email', $resident['email'])
            ->delete();

        // Remove notifications connected to this pending resident
        $db->table('notifications')
            ->groupStart()
            ->where('related_user_id', $userId)
            ->orWhere('user_id', $userId)
            ->groupEnd()
            ->delete();

        // Delete pending resident account
        $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'Unable to delete the pending resident account.'
                );
        }

        // Remove uploaded profile picture if one exists
        $profileImage = trim(
            (string) ($resident['profile_image'] ?? '')
        );

        $profileImage = ltrim($profileImage, '/\\');

        if (
            $profileImage !== '' &&
            strpos($profileImage, 'uploads/profiles/') === 0
        ) {
            $profilePath = FCPATH . $profileImage;

            if (is_file($profilePath)) {
                @unlink($profilePath);
            }
        }

        return redirect()->to('/admin/residents')
            ->with(
                'success',
                'Pending resident account deleted successfully.'
            );
    }

    public function updateResidentStatus($userId)
    {
        $db = \Config\Database::connect();

        $resident = $db->table('users')
            ->select('user_id, full_name, is_active, email_verified_at')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/admin/residents')
                ->with('error', 'Resident account not found.');
        }

        // Prevent activating an unverified resident account
        if (
            (int) $resident['is_active'] !== 1 &&
            empty($resident['email_verified_at'])
        ) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'This resident cannot be activated yet because the email address has not been verified.'
                );
        }

        $newStatus = (int) $resident['is_active'] === 1 ? 0 : 1;

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->update([
                'is_active' => $newStatus,
            ]);

        if (!$updated) {
            return redirect()->to('/admin/residents')
                ->with('error', 'Unable to update resident account status.');
        }

        // Remove Remember Me tokens when account is deactivated
        if ($newStatus === 0) {
            $db->table('remember_tokens')
                ->where('user_id', $userId)
                ->delete();
        }

        $message = $newStatus === 1
            ? 'Resident account activated successfully.'
            : 'Resident account deactivated successfully.';

        return redirect()->to('/admin/residents')
            ->with('success', $message);
    }


    // =========================
    // SUBMIT REPORT PAGE
    // =========================
    public function report()
    {
        $db = \Config\Database::connect();

        $categories = $db->table('categories')
            ->select('category_id, category_name')
            ->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('resident/report', [
            'categories' => $categories
        ]);
    }

    // =========================
    // RESIDENT - MY REPORTS
    // =========================
    public function myReports()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $reports = $db->table('reports')
            ->select('
            reports.*,
            categories.category_name,
            (
                SELECT images.image_path
                FROM images
                WHERE images.report_id = reports.report_id
                ORDER BY images.image_id ASC
                LIMIT 1
            ) AS image_path
        ')
            ->join(
                'categories',
                'categories.category_id = reports.category_id',
                'left'
            )
            ->where('reports.user_id', $userId)
            ->orderBy('reports.report_id', 'DESC')
            ->get()
            ->getResultArray();

        return view('resident/myreports', [
            'reports' => $reports
        ]);
    }

    // =========================
    // RESIDENT NOTIFICATIONS
    // =========================
    public function notifications()
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        $notifications = $db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('notification_id', 'DESC')
            ->get()
            ->getResultArray();

        $totalCount = count($notifications);

        $unreadCount = $db->table('notifications')
            ->where('user_id', $userId)
            ->where('status', 'Unread')
            ->countAllResults();

        $readCount = $db->table('notifications')
            ->where('user_id', $userId)
            ->where('status', 'Read')
            ->countAllResults();

        return view('resident/notifications', [
            'notifications' => $notifications,
            'totalCount' => $totalCount,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount
        ]);
    }


    public function openResidentNotification($notificationId)
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        // Get notification owned by logged-in resident
        $notification = $db->table('notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$notification) {
            return redirect()->to('/resident/notifications')
                ->with('error', 'Notification not found.');
        }

        // Mark notification as Read
        $db->table('notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->update([
                'status' => 'Read'
            ]);

        // Open the exact report
        if (!empty($notification['report_id'])) {
            return redirect()->to(
                '/resident/report-details/' . $notification['report_id']
            );
        }

        return redirect()->to('/resident/notifications');
    }



    public function openAdminNotification(int $notificationId)
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        $notification = $db->table('notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$notification) {
            return redirect()->to('/admin/notifications')
                ->with('error', 'Notification not found.');
        }

        // Mark as Read
        $db->table('notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->update([
                'status' => 'Read'
            ]);

        // If notification is connected to a report
        // If notification is connected to a report
        if (!empty($notification['report_id'])) {
            return redirect()->to(
                '/admin/reports?report_id=' . $notification['report_id']
            );
        }

        // If notification is connected to a resident
        $relatedUserId = (int) ($notification['related_user_id'] ?? 0);

        if ($relatedUserId > 0) {
            return redirect()->to('/admin/residents?resident_id=' . $relatedUserId);
        }

        // Fallback for old registration notifications without related_user_id
        $message = (string) ($notification['message'] ?? '');

        if (str_starts_with($message, 'New resident registered:')) {
            return redirect()->to('/admin/residents');
        }

        return redirect()->to('/admin/notifications');

        // Registration notification
        $relatedUserId = (int) ($notification['related_user_id'] ?? 0);

        if ($relatedUserId > 0) {
            return redirect()->to(
                '/admin/residents?resident_id=' . $relatedUserId
            );
        }
        return redirect()->to('/admin/notifications');
    }



    // =========================
    // RESIDENT PROFILE
    // =========================
    public function profile()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/resident/dashboard')
                ->with('error', 'Resident account not found.');
        }

        $settings = $db->table('settings')
            ->orderBy('setting_id', 'ASC')
            ->get()
            ->getRowArray();

        $puroks = $db->table('puroks')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('resident/profile', [
            'resident' => $resident,
            'settings' => $settings,
            'puroks'   => $puroks
        ]);
    }

    public function sendProfileEmailCode()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Resident account not found.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $newEmail = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        if (
            $newEmail === '' ||
            !filter_var($newEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $currentEmail = strtolower(trim(
            (string) ($resident['email'] ?? '')
        ));

        if ($newEmail === $currentEmail) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'This is already your current email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        // Prevent using another user's email
        $existingUser = $db->table('users')
            ->where('email', $newEmail)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingUser) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already being used.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingVerification = $db->table('email_verifications')
            ->where('email', $newEmail)
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

                return $this->response
                    ->setStatusCode(429)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Please wait ' . $remaining .
                            ' seconds before requesting another code.',
                        'csrfHash' => csrf_hash(),
                    ]);
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
                ->where('email', $newEmail)
                ->update($verificationData);
        } else {
            $verificationData['email'] =
                $newEmail;

            $verificationData['created_at'] =
                $now;

            $saved = $db->table('email_verifications')
                ->insert($verificationData);
        }

        if (!$saved) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create verification code.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        try {
            $emailService =
                service('email');

            $emailService->clear(true);

            $emailConfig =
                config('Email');

            $emailService->setFrom(
                $emailConfig->fromEmail,
                $emailConfig->fromName
            );

            $emailService->setTo(
                $newEmail
            );

            $emailService->setSubject(
                'Verify Your New Email - Community Visibility System'
            );

            $message = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                <h2>Email Change Verification</h2>

                <p>
                    You requested to change the email address
                    connected to your Community Visibility System account.
                </p>

                <p>Your verification code is:</p>

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
    If you did not request this change,
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
            $emailService->setMessage(
                $message
            );

            if (!$emailService->send()) {
                $db->table('email_verifications')
                    ->where('email', $newEmail)
                    ->delete();

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Unable to send the verification email. Please try again.',
                        'csrfHash' => csrf_hash(),
                    ]);
            }
        } catch (\Throwable $e) {

            $db->table('email_verifications')
                ->where('email', $newEmail)
                ->delete();

            log_message(
                'error',
                'Profile email verification error: {message}',
                ['message' => $e->getMessage()]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to send the verification email. Please try again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        // New code means any previous profile verification is no longer valid
        session()->remove(
            'verified_profile_email'
        );

        return $this->response
            ->setJSON([
                'success' => true,
                'message' =>
                'Verification code sent to your new email address.',
                'csrfHash' => csrf_hash(),
            ]);
    }

    public function verifyProfileEmailCode()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Resident account not found.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $newEmail = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        $code = trim(
            (string) $this->request->getPost('code')
        );

        if (
            $newEmail === '' ||
            !filter_var($newEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter the 6-digit verification code.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $currentEmail = strtolower(trim(
            (string) ($resident['email'] ?? '')
        ));

        if ($newEmail === $currentEmail) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'This is already your current email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingUser = $db->table('users')
            ->where('email', $newEmail)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingUser) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already being used.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $verification = $db->table('email_verifications')
            ->where('email', $newEmail)
            ->get()
            ->getRowArray();

        if (!$verification) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'No verification request was found for this email.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        if ((int) $verification['attempts'] >= 5) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON([
                    'success' => false,
                    'message' => 'Too many incorrect attempts. Please request a new code.',
                    'csrfHash' => csrf_hash(),
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
                    'csrfHash' => csrf_hash(),
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
                    'csrfHash' => csrf_hash(),
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

        session()->set(
            'verified_profile_email',
            $newEmail
        );

        return $this->response
            ->setJSON([
                'success'  => true,
                'message'  => 'New email verified successfully.',
                'csrfHash' => csrf_hash(),
            ]);
    }

    public function updateProfile()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/resident/profile')
                ->with('error', 'Resident account not found.');
        }

        $fullName = trim((string) $this->request->getPost('full_name'));
        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $address = trim((string) $this->request->getPost('address'));
        $purokId = (int) $this->request->getPost('purok_id');

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        if ($fullName === '' || $email === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Full name and email are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid email address.');
        }

        if ($username === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username is required.');
        }

        if (
            strlen($username) < 4 ||
            strlen($username) > 30 ||
            preg_match('/\s/', $username)
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Username must be 4 to 30 characters and must not contain spaces.'
                );
        }

        // Check if username is already used by another account
        $existingUsername = $db->table('users')
            ->where('username', $username)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingUsername) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Username is already being used.'
                );
        }

        // Check if email is already used by another account
        $existingEmail = $db->table('users')
            ->where('email', $email)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingEmail) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email address is already being used.');
        }

        $currentEmail = strtolower(trim(
            (string) ($resident['email'] ?? '')
        ));

        $normalizedEmail = strtolower(trim($email));

        $emailChanged = $normalizedEmail !== $currentEmail;

        if ($emailChanged) {
            $verifiedProfileEmail = strtolower(trim(
                (string) session()->get('verified_profile_email')
            ));

            if (
                $verifiedProfileEmail === '' ||
                !hash_equals($verifiedProfileEmail, $normalizedEmail)
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Please verify your new email address before saving your profile.'
                    );
            }

            $verificationRecord = $db->table('email_verifications')
                ->where('email', $normalizedEmail)
                ->where('verified_at IS NOT NULL', null, false)
                ->get()
                ->getRowArray();

            if (!$verificationRecord) {
                session()->remove('verified_profile_email');

                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Your email verification is no longer valid. Please verify your new email again.'
                    );
            }
        }

        if ($purokId > 0) {
            $validPurok = $db->table('puroks')
                ->where('purok_id', $purokId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (! $validPurok) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please select a valid Purok.');
            }
        }

        $updateData = [
            'full_name'     => $fullName,
            'username'      => $username,
            'email'         => $email,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'address'       => $address !== '' ? $address : null,
            'purok_id'      => $purokId > 0 ? $purokId : null,
        ];


        // =====================================
        // Optional Password Change
        // =====================================

        if ($newPassword !== '' || $confirmPassword !== '') {

            if ($currentPassword === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Enter your current password to change your password.');
            }

            if (!password_verify($currentPassword, $resident['password'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Current password is incorrect.');
            }

            if (strlen($newPassword) < 8) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'New password must be at least 8 characters.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'New password and confirmation do not match.');
            }

            $updateData['password'] = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );
        }

        // =====================================
        // Optional Profile Picture
        // =====================================

        $profileImage = $this->request->getFile('profile_image');

        $hasNewImage =
            $profileImage !== null &&
            $profileImage->getError() !== UPLOAD_ERR_NO_FILE;

        $newImagePath = null;
        $newPhysicalPath = null;

        if ($hasNewImage) {

            if (!$profileImage->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to upload the profile picture.');
            }

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array(
                $profileImage->getMimeType(),
                $allowedMimeTypes,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Profile picture must be JPG, PNG, or WebP.');
            }

            if ($profileImage->getSize() > (5 * 1024 * 1024)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Profile picture must not exceed 5 MB.');
            }

            $uploadDirectory = FCPATH . 'uploads/profiles';

            if (!is_dir($uploadDirectory)) {
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
                    ->with('error', 'Unable to save the profile picture.');
            }

            $newImagePath = 'uploads/profiles/' . $newName;
            $newPhysicalPath = FCPATH . $newImagePath;

            $updateData['profile_image'] = $newImagePath;
        }

        // =====================================
        // Update Database
        // =====================================

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->update($updateData);

        if (!$updated) {

            if (
                $newPhysicalPath &&
                is_file($newPhysicalPath)
            ) {
                unlink($newPhysicalPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to update profile.');
        }

        // Delete old uploaded profile picture
        if (
            $hasNewImage &&
            !empty($resident['profile_image'])
        ) {
            $oldImagePath =
                FCPATH . ltrim(
                    $resident['profile_image'],
                    '/\\'
                );

            if (is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Keep session name updated
        session()->set('full_name', $fullName);

        return redirect()->to('/resident/profile')
            ->with('success', 'Profile updated successfully.');
    }



    // =========================
    // REPORT DETAILS
    // =========================
    public function reportDetails($id = null)
    {
        if (!$id) {
            return redirect()->to('/resident/my-reports');
        }

        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');
        $reportId = (int) $id;

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        // Get resident's report
        $report = $db->table('reports')
            ->select('
            reports.*,
            categories.category_name
        ')
            ->join(
                'categories',
                'categories.category_id = reports.category_id',
                'left'
            )
            ->where('reports.report_id', $reportId)
            ->where('reports.user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$report) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Report not found.');
        }

        // Get all photos belonging to this report
        $images = $db->table('images')
            ->select('image_id, image_path')
            ->where('report_id', $reportId)
            ->orderBy('image_id', 'ASC')
            ->get()
            ->getResultArray();

        return view('resident/report-details', [
            'report' => $report,
            'images' => $images
        ]);
    }
    // =========================
    // ADMIN - REPORTS
    // =========================
    public function reports()
    {
        $db = \Config\Database::connect();

        // =========================
        // GET FILTER VALUES
        // =========================
        $search = trim((string) $this->request->getGet('search'));
        $categoryId = (int) $this->request->getGet('category');
        $status = trim((string) $this->request->getGet('status'));
        $fromDate = trim((string) $this->request->getGet('from_date'));
        $toDate = trim((string) $this->request->getGet('to_date'));
        $sort = strtolower(trim((string) $this->request->getGet('sort')));

        if (!in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'newest';
        }

        // =========================
        // PAGINATION
        // =========================
        $settings = $this->getSystemSettings();
        $perPage = max(5, min(100, (int) ($settings['items_per_page'] ?? 10)));
        $page = max(1, (int) $this->request->getGet('page'));
        $offset = ($page - 1) * $perPage;

        // =========================
        // REUSABLE FILTER FUNCTION
        // =========================
        $applyFilters = function ($builder) use (
            $search,
            $categoryId,
            $status,
            $fromDate,
            $toDate
        ) {
            if ($search !== '') {
                $builder->groupStart()
                    ->like('reports.title', $search)
                    ->orLike('reports.description', $search)
                    ->orLike('reports.address', $search)
                    ->orLike('category.category_name', $search)

                    // Resident name is searchable ONLY for non-anonymous reports
                    ->orGroupStart()
                    ->where('reports.is_anonymous', 0)
                    ->like('users.full_name', $search)
                    ->groupEnd();

                // Allow searching exact report ID
                if (preg_match('/^(?:RPT-)?0*(\d+)$/i', $search, $matches)) {
                    $builder->orWhere(
                        'reports.report_id',
                        (int) $matches[1]
                    );
                }

                $builder->groupEnd();
            }

            if ($categoryId > 0) {
                $builder->where(
                    'reports.category_id',
                    $categoryId
                );
            }

            if ($status !== '' && $status !== 'all') {
                $builder->where(
                    'reports.status',
                    $status
                );
            }

            if ($fromDate !== '') {
                $builder->where(
                    'reports.date_reported >=',
                    $fromDate . ' 00:00:00'
                );
            }

            if ($toDate !== '') {
                $builder->where(
                    'reports.date_reported <=',
                    $toDate . ' 23:59:59'
                );
            }

            return $builder;
        };

        // =========================
        // COUNT FILTERED REPORTS
        // =========================
        $countBuilder = $db->table('reports')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'users',
                'users.user_id = reports.user_id',
                'left'
            );

        $applyFilters($countBuilder);

        $totalReports = $countBuilder->countAllResults();

        $totalPages = max(
            1,
            (int) ceil($totalReports / $perPage)
        );

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        // =========================
        // GET REPORTS
        // =========================
        $builder = $db->table('reports')
            ->select('
    reports.*,
    category.category_name,
    (
        SELECT images.image_path
        FROM images
        WHERE images.report_id = reports.report_id
        ORDER BY images.image_id ASC
        LIMIT 1
    ) AS image_path,
    users.full_name
')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )

            ->join(
                'users',
                'users.user_id = reports.user_id',
                'left'
            );

        $applyFilters($builder);

        $builder->orderBy(
            'reports.date_reported',
            $sort === 'oldest' ? 'ASC' : 'DESC'
        );

        $reports = $builder
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // =========================
        // CATEGORY FILTER OPTIONS
        // Include inactive categories because
        // old reports may still use them.
        // =========================
        $categories = $db->table('categories')
            ->select('category_id, category_name, is_active')
            ->orderBy('category_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/reports', [
            'reports' => $reports,
            'categories' => $categories,

            'filters' => [
                'search' => $search,
                'category' => $categoryId,
                'status' => $status,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'sort' => $sort,
            ],

            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_reports' => $totalReports,
                'per_page' => $perPage,
            ],
        ]);
    }



    // =========================
    // ADMIN - RESIDENTS
    // =========================
    public function residents()
    {
        $db = \Config\Database::connect();

        $settings = $this->getSystemSettings();

        $perPage = max(
            5,
            min(
                100,
                (int) ($settings['items_per_page'] ?? 10)
            )
        );

        // ==========================================
        // FILTER VALUES
        // ==========================================

        $search = trim(
            (string) $this->request->getGet('search')
        );

        $status = strtolower(trim(
            (string) $this->request->getGet('status')
        ));

        $purok = trim(
            (string) $this->request->getGet('purok')
        );

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'all';
        }

        // ==========================================
        // FILTER HELPER
        // Apply SAME filters to count + actual rows
        // ==========================================

        $applyFilters = static function (
            $builder
        ) use (
            $search,
            $status,
            $purok
        ) {
            $builder->where('u.role', 'resident');

            // SEARCH ALL RESIDENTS
            if ($search !== '') {
                $builder
                    ->groupStart()
                    ->like('u.full_name', $search)
                    ->orLike('u.email', $search)
                    ->orLike('u.mobile_number', $search)
                    ->orLike('u.username', $search)
                    ->orLike('u.address', $search)
                    ->orLike('p.purok_name', $search)
                    ->groupEnd();
            }

            // STATUS
            if ($status === 'active') {
                $builder->where('u.is_active', 1);
            } elseif ($status === 'inactive') {
                $builder->where('u.is_active', 0);
            }

            // PUROK
            if ($purok === 'unassigned') {
                $builder
                    ->groupStart()
                    ->where('u.purok_id IS NULL', null, false)
                    ->orWhere('u.purok_id', 0)
                    ->groupEnd();
            } elseif (
                $purok !== '' &&
                ctype_digit($purok)
            ) {
                $builder->where(
                    'u.purok_id',
                    (int) $purok
                );
            }

            return $builder;
        };

        // ==========================================
        // COUNT FILTERED RESIDENTS
        // ==========================================

        $countBuilder = $db->table('users u')
            ->join(
                'puroks p',
                'p.purok_id = u.purok_id',
                'left'
            );

        $applyFilters($countBuilder);

        $totalResidents =
            $countBuilder->countAllResults();

        // ==========================================
        // PAGINATION
        // ==========================================

        $totalPages = max(
            1,
            (int) ceil(
                $totalResidents / $perPage
            )
        );

        $page = max(
            1,
            (int) $this->request->getGet('page')
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset =
            ($page - 1) * $perPage;

        // ==========================================
        // GET FILTERED RESIDENTS
        // ==========================================

        $residentBuilder =
            $db->table('users u')
            ->select('
                u.user_id,
                u.full_name,
                u.email,
                u.mobile_number,
                u.username,
                u.address,
                u.purok_id,
                u.profile_image,
                u.is_active,
                u.created_at,
                p.purok_name
            ')
            ->join(
                'puroks p',
                'p.purok_id = u.purok_id',
                'left'
            );

        $applyFilters($residentBuilder);

        $residents = $residentBuilder
            ->orderBy(
                'u.created_at',
                'DESC'
            )
            ->limit(
                $perPage,
                $offset
            )
            ->get()
            ->getResultArray();

        // ==========================================
        // PUROK OPTIONS
        // ==========================================

        $puroks = $db->table('puroks')
            ->select(
                'purok_id, purok_name'
            )
            ->where('is_active', 1)
            ->orderBy(
                'purok_name',
                'ASC'
            )
            ->get()
            ->getResultArray();

        return view(
            'admin/residents',
            [
                'residents' => $residents,

                'puroks' => $puroks,

                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'purok'  => $purok,
                ],

                'pagination' => [
                    'current_page' =>
                    $page,

                    'total_pages' =>
                    $totalPages,

                    'total_residents' =>
                    $totalResidents,

                    'per_page' =>
                    $perPage,
                ],
            ]
        );
    }


    // =========================
    // ADMIN - CATEGORIES
    // =========================
    public function categories()
    {
        $db = \Config\Database::connect();

        $categories = $db->table('categories c')
            ->select('
            c.category_id,
            c.category_name,
            c.description,
            c.is_active,
            COUNT(r.report_id) AS report_count
        ')
            ->join(
                'reports r',
                'r.category_id = c.category_id',
                'left'
            )
            ->groupBy([
                'c.category_id',
                'c.category_name',
                'c.description',
                'c.is_active'
            ])
            ->orderBy('c.category_id', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/categories', [
            'categories' => $categories
        ]);
    }


    public function createCategory()
    {
        $db = \Config\Database::connect();
        log_message('error', 'CREATE CATEGORY METHOD HIT');

        $categoryName = trim(
            (string) $this->request->getPost('category_name')
        );

        $description = trim(
            (string) $this->request->getPost('description')
        );

        $isActive = $this->request->getPost('is_active') === '0'
            ? 0
            : 1;

        if ($categoryName === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category name is required.');
        }

        if (mb_strlen($categoryName) > 50) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category name must not exceed 50 characters.');
        }

        $existingCategory = $db->table('categories')
            ->where('category_name', $categoryName)
            ->get()
            ->getRowArray();

        if ($existingCategory) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category already exists.');
        }

        $db->table('categories')->insert([
            'category_name' => $categoryName,
            'description'   => $description !== '' ? $description : null,
            'is_active'     => $isActive,
        ]);

        return redirect()->to('/admin/categories')
            ->with('success', 'Category added successfully.');
    }

    public function updateCategory($categoryId = null)
    {
        $db = \Config\Database::connect();

        $categoryId = (int) $categoryId;

        $category = $db->table('categories')
            ->where('category_id', $categoryId)
            ->get()
            ->getRowArray();

        if (!$category) {
            return redirect()->to('/admin/categories')
                ->with('error', 'Category not found.');
        }

        $categoryName = trim(
            (string) $this->request->getPost('category_name')
        );

        $description = trim(
            (string) $this->request->getPost('description')
        );

        $isActive = $this->request->getPost('is_active') === '0'
            ? 0
            : 1;

        if ($categoryName === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category name is required.');
        }

        if (mb_strlen($categoryName) > 50) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category name must not exceed 50 characters.');
        }

        $duplicate = $db->table('categories')
            ->where('category_name', $categoryName)
            ->where('category_id !=', $categoryId)
            ->get()
            ->getRowArray();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Category name already exists.');
        }

        $db->table('categories')
            ->where('category_id', $categoryId)
            ->update([
                'category_name' => $categoryName,
                'description'   => $description !== '' ? $description : null,
                'is_active'     => $isActive,
            ]);

        return redirect()->to('/admin/categories')
            ->with('success', 'Category updated successfully.');
    }

    public function toggleCategory($categoryId = null)
    {
        $db = \Config\Database::connect();

        $categoryId = (int) $categoryId;

        $category = $db->table('categories')
            ->where('category_id', $categoryId)
            ->get()
            ->getRowArray();

        if (!$category) {
            return redirect()->to('/admin/categories')
                ->with('error', 'Category not found.');
        }

        $newStatus = (int) $category['is_active'] === 1
            ? 0
            : 1;

        $db->table('categories')
            ->where('category_id', $categoryId)
            ->update([
                'is_active' => $newStatus
            ]);

        return redirect()->to('/admin/categories')
            ->with(
                'success',
                $newStatus === 1
                    ? 'Category activated successfully.'
                    : 'Category deactivated successfully.'
            );
    }

    public function deleteCategory($categoryId = null)
    {
        $db = \Config\Database::connect();

        $categoryId = (int) $categoryId;

        $category = $db->table('categories')
            ->where('category_id', $categoryId)
            ->get()
            ->getRowArray();

        if (!$category) {
            return redirect()->to('/admin/categories')
                ->with('error', 'Category not found.');
        }

        $reportCount = $db->table('reports')
            ->where('category_id', $categoryId)
            ->countAllResults();

        if ($reportCount > 0) {
            return redirect()->to('/admin/categories')
                ->with(
                    'error',
                    'This category cannot be deleted because it is already used by existing reports. Deactivate it instead.'
                );
        }

        $db->table('categories')
            ->where('category_id', $categoryId)
            ->delete();

        return redirect()->to('/admin/categories')
            ->with('success', 'Category deleted successfully.');
    }


    // =========================
    // ADMIN NOTIFICATIONS
    // =========================
    public function notificationsAdmin()
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        $notifications = $db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('notification_id', 'DESC')
            ->get()
            ->getResultArray();

        $unreadCount = $db->table('notifications')
            ->where('user_id', $userId)
            ->where('status', 'Unread')
            ->countAllResults();

        return view('admin/notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }
    // =========================
    // ADMIN SETTINGS
    // =========================
    public function settings()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        $settings = $db->table('settings')
            ->orderBy('setting_id', 'ASC')
            ->get()
            ->getRowArray();

        $admin = $db->table('users')
            ->select('user_id, full_name, profile_image')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        return view('admin/settings', [
            'settings' => $settings,
            'admin'    => $admin
        ]);
    }

    public function account()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $admin = $db->table('users')
            ->select('
            user_id,
            full_name,
            email,
            mobile_number,
            username,
            address,
            profile_image,
            role,
            is_active,
            created_at,
            updated_at
        ')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (! $admin) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Administrator account not found.');
        }

        return view('admin/account', [
            'admin' => $admin
        ]);
    }


    public function sendAdminEmailCode()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $admin = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Administrator account not found.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $newEmail = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        if (
            $newEmail === '' ||
            !filter_var($newEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $currentEmail = strtolower(trim(
            (string) ($admin['email'] ?? '')
        ));

        if ($newEmail === $currentEmail) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'This is already your current email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingUser = $db->table('users')
            ->where('email', $newEmail)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingUser) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already being used.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingVerification = $db->table('email_verifications')
            ->where('email', $newEmail)
            ->get()
            ->getRowArray();

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

                return $this->response
                    ->setStatusCode(429)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Please wait ' . $remaining .
                            ' seconds before requesting another code.',
                        'csrfHash' => csrf_hash(),
                    ]);
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
                ->where('email', $newEmail)
                ->update($verificationData);
        } else {
            $verificationData['email'] =
                $newEmail;

            $verificationData['created_at'] =
                $now;

            $saved = $db->table('email_verifications')
                ->insert($verificationData);
        }

        if (!$saved) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create verification code.',
                    'csrfHash' => csrf_hash(),
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

            $emailService->setTo($newEmail);

            $emailService->setSubject(
                'Verify Administrator Email - Community Visibility System'
            );

            $message = '
        <div style="font-family: Arial, sans-serif; line-height: 1.6;">
            <h2>Administrator Email Verification</h2>

            <p>
                You requested to change the email address
                connected to the administrator account.
            </p>

            <p>Your verification code is:</p>

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
                If you did not request this change,
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
                $db->table('email_verifications')
                    ->where('email', $newEmail)
                    ->delete();

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Unable to send the verification email. Please try again.',
                        'csrfHash' => csrf_hash(),
                    ]);
            }
        } catch (\Throwable $e) {
            $db->table('email_verifications')
                ->where('email', $newEmail)
                ->delete();

            log_message(
                'error',
                'Admin email verification error: {message}',
                ['message' => $e->getMessage()]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to send the verification email. Please try again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        session()->remove('verified_admin_email');

        return $this->response
            ->setJSON([
                'success' => true,
                'message' =>
                'Verification code sent to your new email address.',
                'csrfHash' => csrf_hash(),
            ]);
    }


    public function verifyAdminEmailCode()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $admin = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (!$admin) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Administrator account not found.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $newEmail = strtolower(trim(
            (string) $this->request->getPost('email')
        ));

        $code = trim(
            (string) $this->request->getPost('code')
        );

        if (
            $newEmail === '' ||
            !filter_var($newEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter the 6-digit verification code.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $currentEmail = strtolower(trim(
            (string) ($admin['email'] ?? '')
        ));

        if ($newEmail === $currentEmail) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'This is already your current email address.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingUser = $db->table('users')
            ->where('email', $newEmail)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($existingUser) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already being used.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $verification = $db->table('email_verifications')
            ->where('email', $newEmail)
            ->get()
            ->getRowArray();

        if (!$verification) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'No verification request was found for this email.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        if ((int) $verification['attempts'] >= 5) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON([
                    'success' => false,
                    'message' => 'Too many incorrect attempts. Please request a new code.',
                    'csrfHash' => csrf_hash(),
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
                    'csrfHash' => csrf_hash(),
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
                    'csrfHash' => csrf_hash(),
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

        session()->set(
            'verified_admin_email',
            $newEmail
        );

        return $this->response
            ->setJSON([
                'success'  => true,
                'message'  => 'New administrator email verified successfully.',
                'csrfHash' => csrf_hash(),
            ]);
    }

    // =========================
    // ADMIN ACCOUNT
    // =========================
    public function updateAdminAccount()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.'
                ]);
        }

        // Confirm that the logged-in user is an active admin account
        $admin = $db->table('users')
            ->select('user_id, role')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (! $admin) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Administrator account not found.'
                ]);
        }

        // Get submitted values
        $fullName = trim((string) $this->request->getPost('full_name'));
        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $normalizedEmail = strtolower($email);
        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $address = trim((string) $this->request->getPost('address'));

        $currentAdmin = $db->table('users')
            ->select('email')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        $emailChanged = $currentAdmin && strtolower((string) $currentAdmin['email']) !== $normalizedEmail;

        if ($emailChanged) {
            $verifiedAdminEmail = strtolower(trim(
                (string) session()->get('verified_admin_email')
            ));

            if (
                $verifiedAdminEmail === '' ||
                !hash_equals($verifiedAdminEmail, $normalizedEmail)
            ) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Please verify your new administrator email before saving changes.',
                        'csrfHash' => csrf_hash(),
                    ]);
            }

            $verificationRecord = $db->table('email_verifications')
                ->where('email', $normalizedEmail)
                ->where('verified_at IS NOT NULL', null, false)
                ->get()
                ->getRowArray();

            if (!$verificationRecord) {
                session()->remove('verified_admin_email');

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Your email verification is no longer valid. Please verify your new email again.',
                        'csrfHash' => csrf_hash(),
                    ]);
            }
        }

        // =====================================
        // BASIC VALIDATION
        // =====================================

        if ($fullName === '') {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Full name is required.'
                ]);
        }

        if ($username === '') {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Username is required.'
                ]);
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please enter a valid email address.'
                ]);
        }


        // =====================================
        // CHECK DUPLICATE USERNAME
        // =====================================

        $usernameExists = $db->table('users')
            ->select('user_id')
            ->where('username', $username)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($usernameExists) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Username is already being used by another account.'
                ]);
        }


        // =====================================
        // CHECK DUPLICATE EMAIL
        // =====================================

        $emailExists = $db->table('users')
            ->select('user_id')
            ->where('email', $email)
            ->where('user_id !=', $userId)
            ->get()
            ->getRowArray();

        if ($emailExists) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email address is already being used by another account.'
                ]);
        }


        // =====================================
        // UPDATE ADMIN ACCOUNT
        // =====================================

        $updateData = [
            'full_name'     => $fullName,
            'username'      => $username,
            'email'         => $normalizedEmail,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'address'       => $address !== '' ? $address : null,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->update($updateData);

        if (! $updated) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to update the administrator account.'
                ]);
        }


        // Keep displayed admin name synchronized
        session()->set('full_name', $fullName);
        session()->set('email', $normalizedEmail);

        if ($emailChanged) {
            $db->table('email_verifications')
                ->where('email', $normalizedEmail)
                ->delete();

            session()->remove('verified_admin_email');
        }


        // Return latest account data
        $updatedAdmin = $db->table('users')
            ->select('
            user_id,
            full_name,
            email,
            mobile_number,
            username,
            address,
            profile_image,
            role,
            is_active,
            created_at,
            updated_at
        ')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile information updated successfully.',
            'data'    => $updatedAdmin
        ]);
    }


    public function changeAdminPassword()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.'
                ]);
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        // Check required fields
        if (
            $currentPassword === '' ||
            $newPassword === '' ||
            $confirmPassword === ''
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please fill in all password fields.'
                ]);
        }

        // Confirm new passwords match
        if ($newPassword !== $confirmPassword) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'New passwords do not match.'
                ]);
        }

        // Password strength
        if (
            strlen($newPassword) < 8 ||
            ! preg_match('/[A-Za-z]/', $newPassword) ||
            ! preg_match('/[0-9]/', $newPassword) ||
            ! preg_match('/[^A-Za-z0-9]/', $newPassword)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'New password must be at least 8 characters and contain letters, numbers, and symbols.'
                ]);
        }

        // Get actual admin password hash
        $admin = $db->table('users')
            ->select('user_id, password, role, is_active')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (! $admin) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Administrator account not found.'
                ]);
        }

        // Verify current password
        if (! password_verify($currentPassword, $admin['password'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ]);
        }

        // Prevent reusing the same password
        if (password_verify($newPassword, $admin['password'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'New password must be different from your current password.'
                ]);
        }

        // Hash and save new password
        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->update([
                'password'   => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if (! $updated) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to change password.'
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function uploadAdminPhoto()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.'
                ]);
        }

        // Confirm logged-in admin
        $admin = $db->table('users')
            ->select('user_id, profile_image, role')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        if (! $admin) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Administrator account not found.'
                ]);
        }

        $file = $this->request->getFile('profile_image');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please choose a valid profile image.'
                ]);
        }

        // Maximum 2 MB
        if ($file->getSize() > (2 * 1024 * 1024)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Profile image must not exceed 2 MB.'
                ]);
        }

        // Accept only JPG, PNG, WebP
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (! in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Only JPG, PNG, and WebP images are allowed.'
                ]);
        }

        // Create upload folder if it does not exist
        $uploadPath = FCPATH . 'uploads/admin';

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();

        $file->move($uploadPath, $newName);

        $relativePath = 'uploads/admin/' . $newName;

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->update([
                'profile_image' => $relativePath,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

        if (! $updated) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to save the profile image.'
                ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Profile photo updated successfully.',
            'image_url' => base_url($relativePath)
        ]);
    }


    // =========================
    // MAP
    // =========================
    public function map()
    {
        $db = \Config\Database::connect();

        $reports = $db->table('reports')
            ->select('
    reports.report_id,
    reports.title,
    reports.description,
    reports.category_id,
    reports.latitude,
    reports.longtitude AS longitude,
    reports.address,
    reports.status,
    reports.resolved_at,
    category.category_name,
    image.image_path
')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'images image',
                'image.report_id = reports.report_id',
                'left'
            )
            ->where('reports.latitude IS NOT NULL')
            ->where('reports.longtitude IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($reports as &$report) {
            $report['image_url'] = !empty($report['image_path'])
                ? base_url($report['image_path'])
                : null;
        }

        unset($report);

        // =========================================================
        // MAP DATA
        // =========================================================
        $puroks = $db->table('puroks')
            ->select('purok_id, purok_name, latitude, longitude')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        $saguingLocations = $db->table('saguing_locations')
            ->select('location_id, location_name, location_type, latitude, longitude, address, search_keywords')
            ->where('is_active', 1)
            ->orderBy('location_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('map/index', [
            'reports'          => $reports,
            'puroks'           => $puroks,
            'saguingLocations' => $saguingLocations,
        ]);
    }

    // =========================================================
    // ADMIN - PUROK MAP SETUP
    // =========================================================

    public function purokMapSetup()
    {
        $db = \Config\Database::connect();

        $puroks = $db->table('puroks')
            ->select('
            purok_id,
            purok_name,
            latitude,
            longitude
        ')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        $completed = 0;

        foreach ($puroks as $purok) {
            if (
                $purok['latitude'] !== null &&
                $purok['latitude'] !== '' &&
                $purok['longitude'] !== null &&
                $purok['longitude'] !== ''
            ) {
                $completed++;
            }
        }

        return view('admin/purok-map-setup', [
            'puroks'   => $puroks,
            'completed' => $completed,
            'total'     => count($puroks),
        ]);
    }


    // =========================================================
    // ADMIN - SAVE PUROK MAP COORDINATES
    // =========================================================

    public function savePurokMapCoordinates()
    {
        $db = \Config\Database::connect();

        $purokId = (int) $this->request->getPost('purok_id');

        $latitudeRaw = trim(
            (string) $this->request->getPost('latitude')
        );

        $longitudeRaw = trim(
            (string) $this->request->getPost('longitude')
        );


        // Validate required values
        if (
            $purokId <= 0 ||
            $latitudeRaw === '' ||
            $longitudeRaw === ''
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Purok and coordinates are required.',
                ]);
        }


        // Validate numeric coordinates
        if (
            !is_numeric($latitudeRaw) ||
            !is_numeric($longitudeRaw)
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid latitude or longitude.',
                ]);
        }


        $latitude = (float) $latitudeRaw;
        $longitude = (float) $longitudeRaw;


        // Valid worldwide coordinate ranges
        if (
            $latitude < -90 ||
            $latitude > 90 ||
            $longitude < -180 ||
            $longitude > 180
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Coordinates are outside the valid range.',
                ]);
        }


        // Check if Purok exists
        $purok = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Purok not found.',
                ]);
        }


        // Save coordinates
        $updated = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->update([
                'latitude'   => number_format(
                    $latitude,
                    8,
                    '.',
                    ''
                ),
                'longitude'  => number_format(
                    $longitude,
                    8,
                    '.',
                    ''
                ),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);


        if (!$updated) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to save Purok coordinates.',
                ]);
        }


        return $this->response->setJSON([
            'success' => true,

            'message' =>
            $purok['purok_name'] .
                ' map location saved successfully.',

            'data' => [
                'purok_id'   => $purokId,
                'purok_name' => $purok['purok_name'],
                'latitude'   => $latitude,
                'longitude'  => $longitude,
            ],
        ]);
    }


    // ===== Integrated backend functions =====

    public function deleteResidentAccount()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            session()->destroy();

            return redirect()->to('/login')
                ->with('error', 'Your session has expired. Please log in again.');
        }

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            session()->destroy();

            return redirect()->to('/login')
                ->with('error', 'Resident account not found.');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $confirmation = trim((string) $this->request->getPost('delete_confirmation'));

        if ($currentPassword === '') {
            return redirect()->to('/resident/profile')
                ->with('error', 'Please enter your current password before deleting your account.');
        }

        if (!password_verify($currentPassword, $resident['password'])) {
            return redirect()->to('/resident/profile')
                ->with('error', 'The password you entered is incorrect.');
        }

        if ($confirmation !== 'DELETE') {
            return redirect()->to('/resident/profile')
                ->with('error', 'Please type DELETE exactly to confirm account deletion.');
        }

        $db->transStart();

        $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->delete();

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/resident/profile')
                ->with('error', 'We could not delete your account. Please try again.');
        }

        session()->destroy();

        return redirect()->to('/login')
            ->with('success', 'Your account has been permanently deleted.');
    }

    public function saveSettings()
    {
        $db = \Config\Database::connect();

        $systemName = trim((string) $this->request->getPost('system_name'));
        $barangayName = trim((string) $this->request->getPost('barangay_name'));
        $contactEmail = trim((string) $this->request->getPost('contact_email'));
        $contactNumber = trim((string) $this->request->getPost('contact_number'));
        $systemDescription = trim((string) $this->request->getPost('system_description'));

        $sessionTimeout = (int) $this->request->getPost('session_timeout');
        $dateFormat = trim((string) $this->request->getPost('date_format'));
        $itemsPerPage = (int) $this->request->getPost('items_per_page');
        $themePreference = trim((string) $this->request->getPost('theme_preference'));

        if ($systemName === '' || $barangayName === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'System Name and Barangay Name are required.');
        }

        if (
            $contactEmail !== '' &&
            !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid contact email.');
        }

        if ($sessionTimeout < 5 || $sessionTimeout > 240) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Session timeout must be between 5 and 240 minutes.');
        }

        if ($itemsPerPage < 5 || $itemsPerPage > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Items per page must be between 5 and 100.');
        }

        $allowedDateFormats = [
            'MM/DD/YYYY',
            'DD/MM/YYYY',
            'YYYY/MM/DD'
        ];

        if (!in_array($dateFormat, $allowedDateFormats, true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid date format selected.');
        }

        $allowedThemes = [
            'Light',
            'Dark'
        ];

        if (!in_array($themePreference, $allowedThemes, true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid theme preference selected.');
        }

        $data = [
            'system_name' => $systemName,
            'barangay_name' => $barangayName,
            'contact_email' => $contactEmail !== '' ? $contactEmail : null,
            'contact_number' => $contactNumber !== '' ? $contactNumber : null,
            'system_description' => $systemDescription !== ''
                ? $systemDescription
                : null,

            'email_notifications' =>
            $this->request->getPost('email_notifications') ? 1 : 0,

            'report_notifications' =>
            $this->request->getPost('report_notifications') ? 1 : 0,

            'registration_notifications' =>
            $this->request->getPost('registration_notifications') ? 1 : 0,

            'session_timeout' => $sessionTimeout,
            'date_format' => $dateFormat,
            'items_per_page' => $itemsPerPage,
            'theme_preference' => $themePreference,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $settings = $db->table('settings')
            ->orderBy('setting_id', 'ASC')
            ->get()
            ->getRowArray();

        if ($settings) {
            $db->table('settings')
                ->where('setting_id', $settings['setting_id'])
                ->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');

            $db->table('settings')->insert($data);
        }

        return redirect()->to('/admin/settings')
            ->with('success', 'Settings saved successfully.');
    }
}
