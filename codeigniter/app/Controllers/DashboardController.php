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
        r.user_id,
        r.reporter_name_snapshot,
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


        $reportNumberMap = $this->getReportNumberMap($db);

        foreach ($recentReports as &$report) {

            $reportId = (int) ($report['report_id'] ?? 0);

            $report['report_no'] =
                $reportNumberMap[$reportId] ?? null;

            $isAnonymous =
                (int) ($report['is_anonymous'] ?? 0) === 1;

            $activeResidentName =
                trim((string) ($report['full_name'] ?? ''));

            $historicalResidentName =
                trim(
                    (string) (
                        $report['reporter_name_snapshot'] ?? ''
                    )
                );

            $userId =
                (int) ($report['user_id'] ?? 0);

            $isDeletedAccount =
                $userId <= 0 &&
                $historicalResidentName !== '';

            $realResidentName =
                $activeResidentName !== ''
                ? $activeResidentName
                : (
                    $historicalResidentName !== ''
                    ? $historicalResidentName
                    : 'Unknown Resident'
                );

            $displayResidentName =
                $realResidentName;

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

                $displayResidentName =
                    implode(' ', $maskedParts);
            }

            if ($isDeletedAccount) {
                $displayResidentName .=
                    ' (Deleted Account)';
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

        foreach ($mapReports as &$mapReport) {

            $reportId = (int) ($mapReport['report_id'] ?? 0);

            $mapReport['report_no'] =
                $reportNumberMap[$reportId] ?? null;
        }

        unset($mapReport);

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
        resident_no,
        full_name,
        first_name,
        middle_name,
        last_name,
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

        $reportNumberMap = $this->getReportNumberMap($db);

        foreach ($recentReports as &$recentReport) {
            $recentReportId =
                (int) ($recentReport['report_id'] ?? 0);

            $recentReport['report_no'] =
                $reportNumberMap[$recentReportId] ?? null;
        }

        unset($recentReport);

        // =========================
        // Profile Picture
        // =========================

        $resident['image_url'] = !empty($resident['profile_image'])
            ? base_url(ltrim($resident['profile_image'], '/\\'))
            : base_url('assets/images/resident picture.png');

        $residentNumberMap = $this->getResidentNumberMap($db);

        $residentNumber =
            $residentNumberMap[(int) $resident['user_id']] ?? null;

        $residentNo = $residentNumber !== null
            ? 'R-' . str_pad(
                (string) $residentNumber,
                3,
                '0',
                STR_PAD_LEFT
            )
            : 'N/A';

        // Visible Resident No. only.
        // The real user_id remains the internal database ID.
        return $this->response->setJSON([
            'success' => true,
            'date_format' => $this->getSystemSettings()['date_format'] ?? 'MM/DD/YYYY',

            'resident' => [
                'user_id' => $resident['user_id'],
                'resident_no' => $residentNo,
                'full_name' => $resident['full_name'],

                'first_name' => $resident['first_name'] ?? null,
                'middle_name' => $resident['middle_name'] ?? null,
                'last_name' => $resident['last_name'] ?? null,

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

        $residentNumberMap = $this->getResidentNumberMap($db);

        $residentNumber =
            $residentNumberMap[(int) $resident['user_id']] ?? null;

        $residentNo =
            $residentNumber !== null
            ? 'R-' . str_pad(
                (string) $residentNumber,
                3,
                '0',
                STR_PAD_LEFT
            )
            : 'N/A';

        return view('resident/dashboard', [
            'resident' => [
                'user_id' => $resident['user_id'],
                'resident_no' => $residentNo,
                'resident_id' => $residentNo,
                'full_name' => $resident['full_name'],
                'username' => $resident['username'],
                'email' => $resident['email'],
                'mobile_number' => $resident['mobile_number'],
                'address' => $resident['address'],
                'image_url' => $imageUrl,
                'is_active' => (int) $resident['is_active'],
                'email_verified_at' => $resident['email_verified_at'],
                'created_at' => $resident['created_at']
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

        $firstName = trim(
            (string) $this->request->getPost('first_name')
        );

        $middleName = trim(
            (string) $this->request->getPost('middle_name')
        );

        $lastName = trim(
            (string) $this->request->getPost('last_name')
        );

        $fullName = trim(
            $firstName
                . ($middleName !== '' ? ' ' . $middleName : '')
                . ' ' . $lastName
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
            $firstName === '' ||
            $lastName === '' ||
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

            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ];

            $clientExtension = strtolower(
                $profileImage->getClientExtension()
            );

            if (!in_array(
                $clientExtension,
                $allowedExtensions,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Profile picture must have a JPG, JPEG, PNG, or WebP file extension.'
                    );
            }

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

        $residentNo = $this->getNextAvailableResidentNo($db);

        $residentId = $userModel->insert([
            'resident_no'       => $residentNo,
            'full_name'         => $fullName,
            'first_name'        => $firstName,
            'middle_name'       => $middleName !== '' ? $middleName : null,
            'last_name'         => $lastName,
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

        $this->logAdminAudit(
            'CREATE_RESIDENT',
            'resident',
            (int) $residentId,
            'Created resident account for ' . $fullName . ' pending email verification.'
        );
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

        $this->logAdminAudit(
            'DELETE_PENDING_RESIDENT',
            'resident',
            $userId,
            'Deleted pending verification resident account: '
                . (string) ($resident['full_name'] ?? 'Resident')
                . '.'
        );

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
        $currentStatus =
            (int) ($resident['is_active'] ?? 0);

        // Truly unverified residents cannot be manually activated.
        if (
            $currentStatus === 0 &&
            empty($resident['email_verified_at'])
        ) {
            return redirect()->to('/admin/residents')
                ->with(
                    'error',
                    'This resident cannot be activated yet because the email address has not been verified.'
                );
        }

        $newStatus =
            $currentStatus === 1 ? 0 : 1;

        $now = date('Y-m-d H:i:s');

        $updateData = [
            'is_active'  => $newStatus,
            'updated_at' => $now,
        ];

        /*
 * Compatibility for older ACTIVE residents
 * whose verification timestamp was not saved.
 *
 * If an already-active resident is being
 * deactivated, they are NOT Pending Verification.
 */
        if (
            $currentStatus === 1 &&
            $newStatus === 0 &&
            empty($resident['email_verified_at'])
        ) {
            $updateData['email_verified_at'] = $now;
        }

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->update($updateData);

        $currentStatus =
            (int) ($resident['is_active'] ?? 0);

        $newStatus =
            $currentStatus === 1 ? 0 : 1;

        $updateData = [
            'is_active' => $newStatus,
        ];

        /*
 * Compatibility fix for older residents:
 * if this account was already ACTIVE before
 * but email_verified_at was never stored,
 * preserve it as a verified account before
 * deactivating it.
 */
        if (
            $currentStatus === 1 &&
            empty($resident['email_verified_at'])
        ) {
            $updateData['email_verified_at'] =
                date('Y-m-d H:i:s');
        }

        $updated = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->update($updateData);

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

        $this->logAdminAudit(
            $newStatus === 1
                ? 'ACTIVATE_RESIDENT'
                : 'DEACTIVATE_RESIDENT',
            'resident',
            (int) $userId,
            ($newStatus === 1
                ? 'Activated resident account: '
                : 'Deactivated resident account: ')
                . (string) ($resident['full_name'] ?? 'Resident')
                . '.'
        );

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

        // Puroks with configured map locations.
        // These are used to identify the Purok of the REPORT LOCATION,
        // not the resident's home Purok.
        $puroks = $db->table('puroks')
            ->select('purok_id, purok_name, latitude, longitude')
            ->where('is_active', 1)
            ->where('latitude IS NOT NULL', null, false)
            ->where('longitude IS NOT NULL', null, false)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('resident/report', [
            'categories' => $categories,
            'puroks'     => $puroks,
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

        // Add the same visible Report No. used throughout the system
        $reportNumberMap = $this->getReportNumberMap($db);

        foreach ($reports as &$report) {
            $reportId = (int) ($report['report_id'] ?? 0);

            $report['report_no'] =
                $reportNumberMap[$reportId] ?? null;
        }

        unset($report);

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

        $currentPassword = (string) $this->request->getPost(
            'current_password'
        );

        if (
            $currentPassword === '' ||
            !password_verify(
                $currentPassword,
                (string) ($resident['password'] ?? '')
            )
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'The current password you entered is incorrect. Please check your Current Password and try again.',
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

        $firstName = trim((string) $this->request->getPost('first_name'));
        $middleName = trim((string) $this->request->getPost('middle_name'));
        $lastName = trim((string) $this->request->getPost('last_name'));

        $fullName = trim(
            $firstName
                . ($middleName !== '' ? ' ' . $middleName : '')
                . ' ' . $lastName
        );

        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $address = trim((string) $this->request->getPost('address'));
        $purokId = (int) $this->request->getPost('purok_id');

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        if ($firstName === '' || $lastName === '' || $email === '') {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'First name, last name, and email are required.'
                );
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
            'first_name'    => $firstName,
            'middle_name'   => $middleName !== '' ? $middleName : null,
            'last_name'     => $lastName,
            'username'      => $username,
            'email'         => $email,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'address'       => $address !== '' ? $address : null,
            'purok_id'      => $purokId > 0 ? $purokId : null,
        ];


        // =====================================
        // Optional Password Change
        // =====================================

        $passwordChanged = false;

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

            $passwordChanged = true;
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

            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ];

            $clientExtension = strtolower(
                $profileImage->getClientExtension()
            );

            if (!in_array(
                $clientExtension,
                $allowedExtensions,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Profile picture must have a JPG, JPEG, PNG, or WebP file extension.'
                    );
            }



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

        // =====================================
        // Security Notifications
        // =====================================

        // Notify the OLD email if the account email was changed.
        if ($emailChanged && $currentEmail !== '') {
            $this->sendSecurityNotificationEmail(
                $currentEmail,
                'Security Alert: Email Address Changed',
                'The email address connected to your Community Visibility System account was changed.'
            );
        }

        // Notify the account email after a successful password change.
        if ($passwordChanged) {
            $this->sendSecurityNotificationEmail(
                $normalizedEmail,
                'Security Alert: Password Changed',
                'The password for your Community Visibility System account was changed successfully.'
            );
        }

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
    categories.category_name,
    puroks.purok_name AS report_purok_name
')
            ->join(
                'categories',
                'categories.category_id = reports.category_id',
                'left'
            )

            ->join(
                'puroks',
                'puroks.purok_id = reports.purok_id',
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

        // Add the same visible Report No. used throughout the system
        $reportNumberMap = $this->getReportNumberMap($db);

        $report['report_no'] =
            $reportNumberMap[$reportId] ?? null;


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
        $priority = trim((string) $this->request->getGet('priority'));
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
            $priority,
            $fromDate,
            $toDate
        ) {
            if ($search !== '') {

                // Numeric search = visible Report No. only
                if (
                    preg_match(
                        '/^(?:RPT-)?0*(\d+)$/i',
                        $search,
                        $matches
                    )
                ) {
                    $builder->where(
                        'reports.report_no',
                        (int) $matches[1]
                    );
                } else {

                    // Text search
                    $builder->groupStart()
                        ->like('reports.title', $search)
                        ->orLike('reports.description', $search)
                        ->orLike('reports.address', $search)
                        ->orLike('category.category_name', $search)

                        // Resident name searchable only for non-anonymous reports
                        ->orGroupStart()
                        ->where('reports.is_anonymous', 0)
                        ->groupStart()
                        ->like('users.full_name', $search)
                        ->orLike('reports.reporter_name_snapshot', $search)
                        ->groupEnd()
                        ->groupEnd();
                }
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

            if ($priority !== '' && $priority !== 'all') {
                $builder->where(
                    'reports.priority',
                    $priority
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
puroks.purok_name AS report_purok_name,
    (
        SELECT images.image_path
        FROM images
        WHERE images.report_id = reports.report_id
        ORDER BY images.image_id ASC
        LIMIT 1
    ) AS image_path,
  users.full_name,
users.resident_no
')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )

            ->join(
                'puroks',
                'puroks.purok_id = reports.purok_id',
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

        // Add consistent visible Report No.
        $reportNumberMap = $this->getReportNumberMap($db);

        foreach ($reports as &$report) {

            $reportId = (int) ($report['report_id'] ?? 0);

            $report['report_no'] =
                $reportNumberMap[$reportId] ?? null;

            // Get all report photos for Admin View Report modal
            $reportImages = $db->table('images')
                ->select('image_path')
                ->where('report_id', $reportId)
                ->orderBy('image_id', 'ASC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $report['image_urls'] = [];

            foreach ($reportImages as $reportImage) {

                $imagePath = trim(
                    (string) ($reportImage['image_path'] ?? '')
                );

                if ($imagePath === '') {
                    continue;
                }

                $report['image_urls'][] =
                    base_url(
                        ltrim($imagePath, '/\\')
                    );
            }
        }

        unset($report);
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
                'priority' => $priority,
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
                u.first_name,
                u.middle_name,
                u.last_name,
                u.email,
                u.mobile_number,
                u.username,
                u.address,
                u.purok_id,
                u.profile_image,
                u.is_active,
                u.email_verified_at,
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

        // Add consistent visible Resident No.
        $residentNumberMap = $this->getResidentNumberMap($db);

        foreach ($residents as &$resident) {
            $residentUserId =
                (int) ($resident['user_id'] ?? 0);

            $residentNumber =
                $residentNumberMap[$residentUserId] ?? null;

            $resident['resident_no'] =
                $residentNumber !== null
                ? 'R-' . str_pad(
                    (string) $residentNumber,
                    3,
                    '0',
                    STR_PAD_LEFT
                )
                : 'N/A';
        }

        unset($resident);

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
    c.category_no,
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
                'c.category_no',
                'c.category_name',
                'c.description',
                'c.is_active'
            ])
            ->orderBy('c.category_id', 'ASC')
            ->get()
            ->getResultArray();

        $categoryNumber = 1;

        foreach ($categories as &$category) {

            $storedCategoryNo =
                (int) ($category['category_no'] ?? 0);

            $category['category_no'] =
                $storedCategoryNo > 0
                ? 'CAT-' . str_pad(
                    (string) $storedCategoryNo,
                    3,
                    '0',
                    STR_PAD_LEFT
                )
                : 'N/A';
        }

        unset($category);

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

        $normalizedCategoryName = preg_replace(
            '/[^\p{L}\p{N}]+/u',
            '',
            mb_strtolower(trim($categoryName))
        );

        $existingCategories = $db->table('categories')
            ->select('category_id, category_name')
            ->get()
            ->getResultArray();

        foreach ($existingCategories as $existingCategory) {

            $existingName = preg_replace(
                '/[^\p{L}\p{N}]+/u',
                '',
                mb_strtolower(
                    trim((string) ($existingCategory['category_name'] ?? ''))
                )
            );

            if ($existingName === $normalizedCategoryName) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Category already exists.');
            }
        }

        $categoryNo =
            $this->getNextAvailableCategoryNo($db);

        $db->table('categories')->insert([
            'category_no'   => $categoryNo,
            'category_name' => $categoryName,
            'description'   => $description !== ''
                ? $description
                : null,
            'is_active'     => $isActive,
        ]);

        $categoryId = (int) $db->insertID();

        $this->logAdminAudit(
            'CREATE_CATEGORY',
            'category',
            $categoryId > 0 ? $categoryId : null,
            'Created category: ' . $categoryName . '.'
        );

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

        $normalizedCategoryName = mb_strtolower(
            preg_replace('/\s+/', ' ', trim($categoryName))
        );

        $existingCategories = $db->table('categories')
            ->select('category_id, category_name')
            ->where('category_id !=', $categoryId)
            ->get()
            ->getResultArray();

        foreach ($existingCategories as $existingCategory) {

            $existingName = preg_replace(
                '/[^\p{L}\p{N}]+/u',
                '',
                mb_strtolower(
                    trim((string) ($existingCategory['category_name'] ?? ''))
                )
            );

            if ($existingName === $normalizedCategoryName) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Category already exists.');
            }
        }

        $db->table('categories')
            ->where('category_id', $categoryId)
            ->update([
                'category_name' => $categoryName,
                'description'   => $description !== '' ? $description : null,
                'is_active'     => $isActive,
            ]);

        $this->logAdminAudit(
            'UPDATE_CATEGORY',
            'category',
            $categoryId,
            'Updated category from "'
                . (string) ($category['category_name'] ?? 'Unknown')
                . '" to "'
                . $categoryName
                . '".'
        );

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

        $this->logAdminAudit(
            $newStatus === 1
                ? 'ACTIVATE_CATEGORY'
                : 'DEACTIVATE_CATEGORY',
            'category',
            $categoryId,
            ($newStatus === 1
                ? 'Activated category: '
                : 'Deactivated category: ')
                . (string) ($category['category_name'] ?? 'Unknown')
                . '.'
        );

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

        $this->logAdminAudit(
            'DELETE_CATEGORY',
            'category',
            $categoryId,
            'Deleted category: '
                . (string) ($category['category_name'] ?? 'Unknown')
                . '.'
        );

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

        // =====================================
        // CONFIRM CURRENT PASSWORD
        // before allowing an email change
        // =====================================

        $currentPassword = (string) $this->request->getPost(
            'current_password'
        );

        if (
            $currentPassword === '' ||
            !password_verify(
                $currentPassword,
                (string) ($admin['password'] ?? '')
            )
        ) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'The current password you entered is incorrect. Please check your Current Password and try again.',
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

            $oldAdminEmail = strtolower(trim(
                (string) ($currentAdmin['email'] ?? '')
            ));

            if ($oldAdminEmail !== '') {
                $this->sendSecurityNotificationEmail(
                    $oldAdminEmail,
                    'Security Alert: Email Address Changed',
                    'The email address connected to your administrator account was changed.'
                );
            }
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

        $residentNumberMap = $this->getResidentNumberMap($db);

        $residentNumber =
            $residentNumberMap[$userId] ?? null;

        $residentNo =
            $residentNumber !== null
            ? 'R-' . str_pad(
                (string) $residentNumber,
                3,
                '0',
                STR_PAD_LEFT
            )
            : 'N/A';

        $this->logAdminAudit(
            $emailChanged
                ? 'CHANGE_ADMIN_EMAIL'
                : 'UPDATE_ADMIN_PROFILE',
            'admin',
            $userId,
            $emailChanged
                ? 'Administrator account profile was updated and the email address was changed.'
                : 'Administrator account profile information was updated.'
        );



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
            ->select('user_id, email, password, role, is_active')
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

        $this->sendSecurityNotificationEmail(
            (string) ($admin['email'] ?? ''),
            'Security Alert: Password Changed',
            'The password for your administrator account was changed successfully.'
        );

        $this->logAdminAudit(
            'CHANGE_ADMIN_PASSWORD',
            'admin',
            $userId,
            'Administrator account password was changed.'
        );

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

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ];

        $clientExtension = strtolower(
            $file->getClientExtension()
        );

        if (!in_array(
            $clientExtension,
            $allowedExtensions,
            true
        )) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Profile image must have a JPG, JPEG, PNG, or WebP file extension.'
                ]);
        }

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

        // Reports resolved more than 7 days ago
        // must no longer be sent to the map.
        $resolvedCutoff =
            date(
                'Y-m-d H:i:s',
                strtotime('-7 days')
            );

        $reports = $db->table('reports')
            ->select('
        reports.report_id,
        reports.title,
        reports.description,
        reports.category_id,
        reports.latitude,
        reports.longtitude AS longitude,
       reports.address,
puroks.purok_name AS report_purok_name,
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
                'puroks',
                'puroks.purok_id = reports.purok_id',
                'left'
            )
            ->join(
                'images image',
                'image.report_id = reports.report_id',
                'left'
            )
            ->where('reports.latitude IS NOT NULL')
            ->where('reports.longtitude IS NOT NULL')
            ->groupStart()
            ->where('reports.status !=', 'Resolved')
            ->orWhere(
                'reports.resolved_at IS NULL',
                null,
                false
            )
            ->orWhere(
                'reports.resolved_at >=',
                $resolvedCutoff
            )
            ->groupEnd()
            ->get()
            ->getResultArray();

        $reportNumberMap = $this->getReportNumberMap($db);

        foreach ($reports as &$report) {

            $report['image_url'] = !empty($report['image_path'])
                ? base_url($report['image_path'])
                : null;

            $reportId = (int) ($report['report_id'] ?? 0);

            $report['report_no'] =
                $reportNumberMap[$reportId] ?? null;
        }

        unset($report);

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
            purok_no,
            purok_name,
            latitude,
            longitude
        ')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($puroks as &$purok) {

            $storedPurokNo =
                (int) ($purok['purok_no'] ?? 0);

            $purok['purok_no'] =
                $storedPurokNo > 0
                ? 'P-' . str_pad(
                    (string) $storedPurokNo,
                    3,
                    '0',
                    STR_PAD_LEFT
                )
                : 'N/A';
        }

        unset($purok);
        unset($purok);

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
    // ADMIN - CREATE PUROK
    // =========================================================

    public function createPurok()
    {
        $db = \Config\Database::connect();

        $purokName = trim(
            (string) $this->request->getPost('purok_name')
        );

        // Remove extra spaces
        $purokName = preg_replace('/\s+/', ' ', $purokName);

        if ($purokName === '') {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'Purok name is required.'
                );
        }

        if (mb_strlen($purokName) > 100) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'Purok name must not exceed 100 characters.'
                );
        }

        // Prevent duplicate Purok names
        $existing = $db->table('puroks')
            ->where('purok_name', $purokName)
            ->get()
            ->getRowArray();

        if ($existing) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'That Purok already exists.'
                );
        }
        $purokNo =
            $this->getNextAvailablePurokNo($db);

        $inserted = $db->table('puroks')->insert([
            'purok_no'       => $purokNo,
            'purok_name'     => $purokName,
            'leader_name'    => null,
            'contact_number' => null,
            'is_active'      => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        if (!$inserted) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'Unable to add Purok.'
                );
        }

        $purokId = (int) $db->insertID();

        $this->logAdminAudit(
            'CREATE_PUROK',
            'purok',
            $purokId > 0 ? $purokId : null,
            'Created Purok: ' . $purokName . '.'
        );

        return redirect()
            ->to('/admin/purok-map-setup')
            ->with(
                'success',
                'Purok added successfully. You can now set its map location.'
            );
    }


    // =========================================================
    // ADMIN - UPDATE PUROK NAME
    // =========================================================

    public function updatePurok($id)
    {
        $db = \Config\Database::connect();

        $purokId = (int) $id;

        $purokName = trim(
            (string) $this->request->getPost('purok_name')
        );

        $purokName = preg_replace('/\s+/', ' ', $purokName);

        if ($purokId <= 0 || $purokName === '') {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Valid Purok name is required.');
        }

        if (mb_strlen($purokName) > 100) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'Purok name must not exceed 100 characters.'
                );
        }

        $purok = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Purok not found.');
        }

        // Check duplicate name except current Purok
        $duplicate = $db->table('puroks')
            ->where('purok_name', $purokName)
            ->where('purok_id !=', $purokId)
            ->get()
            ->getRowArray();

        if ($duplicate) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'Another Purok already uses that name.'
                );
        }

        $updated = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->update([
                'purok_name' => $purokName,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (!$updated) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Unable to update Purok.');
        }

        $this->logAdminAudit(
            'UPDATE_PUROK',
            'purok',
            $purokId,
            'Updated Purok name from "'
                . (string) ($purok['purok_name'] ?? 'Unknown')
                . '" to "'
                . $purokName
                . '".'
        );

        return redirect()
            ->to('/admin/purok-map-setup')
            ->with('success', 'Purok name updated successfully.');
    }

    // =========================================================
    // ADMIN - DELETE PUROK
    // =========================================================

    public function deletePurok($id)
    {
        $db = \Config\Database::connect();

        $purokId = (int) $id;

        if ($purokId <= 0) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Invalid Purok.');
        }

        $purok = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Purok not found.');
        }

        // SAFETY:
        // Do not delete a Purok already assigned to residents
        $residentCount = $db->table('users')
            ->where('purok_id', $purokId)
            ->countAllResults();

        if ($residentCount > 0) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with(
                    'error',
                    'This Purok is already assigned to residents. Edit the name instead.'
                );
        }

        $deleted = $db->table('puroks')
            ->where('purok_id', $purokId)
            ->delete();

        if (!$deleted) {
            return redirect()
                ->to('/admin/purok-map-setup')
                ->with('error', 'Unable to delete Purok.');
        }

        $this->logAdminAudit(
            'DELETE_PUROK',
            'purok',
            $purokId,
            'Deleted Purok: '
                . (string) ($purok['purok_name'] ?? 'Unknown')
                . '.'
        );

        return redirect()
            ->to('/admin/purok-map-setup')
            ->with('success', 'Purok deleted successfully.');
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

        $this->logAdminAudit(
            'UPDATE_PUROK_MAP_LOCATION',
            'purok',
            $purokId,
            'Updated map location for '
                . (string) ($purok['purok_name'] ?? 'Unknown')
                . ' to latitude '
                . $latitude
                . ' and longitude '
                . $longitude
                . '.'
        );

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
                ->with(
                    'error',
                    'Your session has expired. Please log in again.'
                );
        }

        // =========================================
        // GET RESIDENT
        // =========================================

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            session()->destroy();

            return redirect()->to('/login')
                ->with(
                    'error',
                    'Resident account not found.'
                );
        }

        // =========================================
        // VALIDATE PASSWORD + CONFIRMATION
        // =========================================

        $currentPassword =
            (string) $this->request->getPost(
                'current_password'
            );

        $confirmation =
            trim(
                (string) $this->request->getPost(
                    'delete_confirmation'
                )
            );

        if ($currentPassword === '') {
            return redirect()->to('/resident/profile')
                ->with(
                    'error',
                    'Please enter your current password before deleting your account.'
                );
        }

        if (
            !password_verify(
                $currentPassword,
                $resident['password']
            )
        ) {
            return redirect()->to('/resident/profile')
                ->with(
                    'error',
                    'The password you entered is incorrect.'
                );
        }

        if ($confirmation !== 'DELETE') {
            return redirect()->to('/resident/profile')
                ->with(
                    'error',
                    'Please type DELETE exactly to confirm account deletion.'
                );
        }

        // =========================================
        // PRESERVE REPORT HISTORY
        // =========================================

        $firstName =
            trim((string) ($resident['first_name'] ?? ''));

        $middleName =
            trim((string) ($resident['middle_name'] ?? ''));

        $lastName =
            trim((string) ($resident['last_name'] ?? ''));

        $middleInitial = '';

        if ($middleName !== '') {
            $middleInitial =
                mb_strtoupper(
                    mb_substr($middleName, 0, 1)
                ) . '.';
        }

        $reporterNameSnapshot =
            trim(
                implode(
                    ' ',
                    array_filter([
                        $firstName,
                        $middleInitial,
                        $lastName
                    ])
                )
            );

        // Fallback for older resident records
        // that may not have separated name fields.
        if ($reporterNameSnapshot === '') {
            $reporterNameSnapshot =
                trim(
                    (string) (
                        $resident['full_name'] ??
                        'Former Resident'
                    )
                );
        }

        // =========================================
        // DELETE ACCOUNT WHILE KEEPING REPORTS
        // =========================================

        $db->transStart();

        // Keep all reports and report photos.
        // Save the resident's name for historical records,
        // then detach the reports from the deleted account.
        $db->table('reports')
            ->where('user_id', $userId)
            ->update([
                'reporter_name_snapshot' => $reporterNameSnapshot,
                'user_id' => null,
            ]);

        /*
         * Delete records directly connected
         * to the resident account.
         */

        if ($db->tableExists('notifications')) {

            $db->table('notifications')
                ->where('user_id', $userId)
                ->delete();

            // Some registration notifications
            // may reference the resident here.
            if (
                $db->fieldExists(
                    'related_user_id',
                    'notifications'
                )
            ) {
                $db->table('notifications')
                    ->where(
                        'related_user_id',
                        $userId
                    )
                    ->delete();
            }
        }

        if ($db->tableExists('action')) {
            $db->table('action')
                ->where('user_id', $userId)
                ->delete();
        }

        if ($db->tableExists('remember_tokens')) {
            $db->table('remember_tokens')
                ->where('user_id', $userId)
                ->delete();
        }

        if ($db->tableExists('password_reset_tokens')) {
            $db->table('password_reset_tokens')
                ->where('user_id', $userId)
                ->delete();
        }

        if (
            $db->tableExists('email_verifications') &&
            !empty($resident['email'])
        ) {
            $db->table('email_verifications')
                ->where(
                    'email',
                    $resident['email']
                )
                ->delete();
        }

        // =========================================
        // FINALLY DELETE THE RESIDENT ACCOUNT
        // =========================================

        $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->delete();

        $db->transComplete();

        // =========================================
        // CHECK TRANSACTION
        // =========================================

        if ($db->transStatus() === false) {

            log_message(
                'error',
                'Resident account deletion failed for user ID: {userId}',
                [
                    'userId' => $userId
                ]
            );

            return redirect()->to('/resident/profile')
                ->with(
                    'error',
                    'We could not delete your account. Please try again.'
                );
        }



        // =========================================
        // DELETE PROFILE IMAGE
        // =========================================

        $profileImage =
            trim(
                (string) (
                    $resident['profile_image'] ?? ''
                )
            );

        if ($profileImage !== '') {

            $physicalProfilePath =
                FCPATH .
                ltrim(
                    $profileImage,
                    '/\\'
                );

            if (is_file($physicalProfilePath)) {
                @unlink($physicalProfilePath);
            }
        }

        // Account is gone. End session.
        session()->destroy();
        return redirect()->to('/login')
            ->with(
                'success',
                'Your account has been permanently deleted. Your submitted reports remain as historical records.'
            );
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

    // =========================================================
    // DISPLAY RESIDENT NUMBER
    // Keeps visible Resident No. continuous without changing
    // the real database user_id.
    // =========================================================

    private function getNextAvailableResidentNo($db): int
    {
        $rows = $db->table('users')
            ->select('resident_no')
            ->where('role', 'resident')
            ->where(
                'resident_no IS NOT NULL',
                null,
                false
            )
            ->orderBy('resident_no', 'ASC')
            ->get()
            ->getResultArray();

        $nextNumber = 1;

        foreach ($rows as $row) {

            $currentNumber =
                (int) ($row['resident_no'] ?? 0);

            if ($currentNumber < $nextNumber) {
                continue;
            }

            if ($currentNumber > $nextNumber) {
                break;
            }

            $nextNumber++;
        }

        return $nextNumber;
    }


    private function getResidentNumberMap($db): array
    {
        $rows = $db->table('users')
            ->select('user_id, resident_no')
            ->where('role', 'resident')
            ->get()
            ->getResultArray();

        $residentNumberMap = [];

        foreach ($rows as $row) {

            $userId =
                (int) ($row['user_id'] ?? 0);

            $residentNo =
                (int) ($row['resident_no'] ?? 0);

            if (
                $userId <= 0 ||
                $residentNo <= 0
            ) {
                continue;
            }

            $residentNumberMap[$userId] =
                $residentNo;
        }

        return $residentNumberMap;
    }
    // =========================================================
    // DISPLAY REPORT NUMBER
    // Keeps visible Report No. continuous without changing
    // the real database report_id.
    // =========================================================
    private function getReportNumberMap($db): array
    {
        $rows = $db->table('reports')
            ->select('report_id, report_no')
            ->get()
            ->getResultArray();

        $reportNumberMap = [];

        foreach ($rows as $row) {

            $reportId =
                (int) ($row['report_id'] ?? 0);

            $reportNo =
                (int) ($row['report_no'] ?? 0);

            if (
                $reportId <= 0 ||
                $reportNo <= 0
            ) {
                continue;
            }

            $reportNumberMap[$reportId] =
                $reportNo;
        }

        return $reportNumberMap;
    }

    // =========================================================
    // DISPLAY PUROK NUMBER
    // Keeps visible Purok No. continuous without changing
    // the real database purok_id.
    // =========================================================

    private function getNextAvailablePurokNo($db): int
    {
        $rows = $db->table('puroks')
            ->select('purok_no')
            ->where(
                'purok_no IS NOT NULL',
                null,
                false
            )
            ->orderBy('purok_no', 'ASC')
            ->get()
            ->getResultArray();

        $nextNumber = 1;

        foreach ($rows as $row) {

            $currentNumber =
                (int) ($row['purok_no'] ?? 0);

            if ($currentNumber < $nextNumber) {
                continue;
            }

            if ($currentNumber > $nextNumber) {
                break;
            }

            $nextNumber++;
        }

        return $nextNumber;
    }

    private function getNextAvailableCategoryNo($db): int
    {
        $rows = $db->table('categories')
            ->select('category_no')
            ->where(
                'category_no IS NOT NULL',
                null,
                false
            )
            ->orderBy('category_no', 'ASC')
            ->get()
            ->getResultArray();

        $nextNumber = 1;

        foreach ($rows as $row) {

            $currentNumber =
                (int) ($row['category_no'] ?? 0);

            if ($currentNumber < $nextNumber) {
                continue;
            }

            if ($currentNumber > $nextNumber) {
                break;
            }

            $nextNumber++;
        }

        return $nextNumber;
    }

    private function logAdminAudit(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $details = null
    ): void {
        $adminUserId = (int) session()->get('user_id');

        if ($adminUserId <= 0) {
            return;
        }

        try {
            $db = \Config\Database::connect();

            $db->table('admin_audit_logs')->insert([
                'admin_user_id' => $adminUserId,
                'action'        => $action,
                'target_type'   => $targetType,
                'target_id'     => $targetId,
                'details'       => $details,
                'ip_address'    => $this->request->getIPAddress(),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Admin audit log failed: {message}',
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    private function sendSecurityNotificationEmail(
        string $toEmail,
        string $subject,
        string $message
    ): void {
        if (
            $toEmail === '' ||
            !filter_var($toEmail, FILTER_VALIDATE_EMAIL)
        ) {
            return;
        }

        try {
            $emailService = service('email');
            $emailService->clear(true);

            $emailConfig = config('Email');

            $emailService->setFrom(
                $emailConfig->fromEmail,
                $emailConfig->fromName
            );

            $emailService->setTo($toEmail);
            $emailService->setSubject($subject);

            $emailService->setMessage(
                '
            <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                <h2>Security Notification</h2>

                <p>' . esc($message) . '</p>

                <p>
                    If you made this change, no action is required.
                </p>

                <p>
                    If you did not make this change,
                    please secure your account immediately.
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
                    This is an automated security notification.
                    Please do not reply to this email.
                </p>
            </div>
            '
            );

            if (!$emailService->send()) {
                log_message(
                    'error',
                    'Security notification email failed for: '
                        . $toEmail
                );
            }
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Security notification email error: {message}',
                [
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}
