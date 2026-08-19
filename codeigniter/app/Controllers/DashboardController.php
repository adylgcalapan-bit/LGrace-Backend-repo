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

        $recentReports = $db->table('reports r')
            ->select('
        r.report_id,
        r.address,
        r.status,
        r.report_date,
        u.full_name,
        c.category_name
    ')
            ->join('users u', 'u.user_id = r.user_id', 'left')
            ->join('categories c', 'c.category_id = r.category_id', 'left')
            ->orderBy('r.report_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        foreach ($recentReports as &$report) {
            $report['display_resident_name'] = trim(
                (string) ($report['full_name'] ?? 'Unknown Resident')
            );
        }
        unset($report);

        $mapReports = $db->table('reports r')
            ->select('
        r.report_id,
        r.title,
        r.latitude,
        r.longitude,
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
            ->where('r.longitude IS NOT NULL', null, false)
            ->get()
            ->getResultArray();

        $dashboardAnnouncements = $db->table('announcements')
            ->select('announcement_id, title, content, category, publish_date, status, created_at')
            ->where('status', 'Published')
            ->orderBy('publish_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'dashboardAnnouncements' => $dashboardAnnouncements,
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'progressReports' => $progressReports,
            'resolvedReports' => $resolvedReports,
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
            r.report_date,
            c.category_name
        ')
            ->join(
                'categories c',
                'c.category_id = r.category_id',
                'left'
            )
            ->where('r.user_id', $userId)
            ->orderBy('r.report_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // =========================
        // Profile Picture
        // =========================

        $resident['image_url'] = !empty($resident['profile_image'])
            ? base_url(ltrim($resident['profile_image'], '/\\'))
            : base_url('assets/images/resident picture.jpg');

        return $this->response->setJSON([
            'success' => true,

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

        $recentReports = $db->table('reports r')
            ->select('
            r.report_id,
            r.title,
            r.status,
            r.report_date,
            c.category_name
        ')
            ->join(
                'categories c',
                'c.category_id = r.category_id',
                'left'
            )
            ->where('r.user_id', $userId)
            ->orderBy('r.report_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $imageUrl = !empty($resident['profile_image'])
            ? base_url(ltrim($resident['profile_image'], '/\\'))
            : base_url('assets/images/resident picture.jpg');

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
                'is_active' => (int) $resident['is_active'],
                'created_at'    => $resident['created_at'] ?? null,
            ],

            'statistics' => [
                'total'       => $totalReports,
                'pending'     => $pendingReports,
                'in_progress' => $progressReports,
                'resolved'    => $resolvedReports,
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

        $username = trim(
            (string) $this->request->getPost('username')
        );

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
            $username === '' ||
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
        // DUPLICATE USERNAME
        // =========================
        if ($userModel->where('username', $username)->first()) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Username is already taken.'
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
        $userModel->insert([
            'full_name'     => $fullName,
            'email'         => $email,
            'mobile_number' => $mobileNumber !== ''
                ? $mobileNumber
                : null,
            'username'      => $username,
            'address'       => $address,
            'purok_id'      => $purokId,
            'profile_image' => $profileImagePath,
            'password'      => password_hash(
                $password,
                PASSWORD_DEFAULT
            ),
            'role'          => 'resident',
            'is_active'     => 1,
        ]);

        return redirect()->to('/admin/residents')
            ->with(
                'success',
                'Resident account created successfully.'
            );
    }

    public function updateResidentStatus($userId)
    {
        $db = \Config\Database::connect();

        $resident = $db->table('users')
            ->select('user_id, full_name, is_active')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/admin/residents')
                ->with('error', 'Resident account not found.');
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
        if (!empty($notification['report_id'])) {
            return redirect()->to(
                '/admin/reports?report_id=' . $notification['report_id']
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

    return view('resident/profile', [
        'resident' => $resident,
        'settings' => $settings
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
        $email = trim((string) $this->request->getPost('email'));
        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $address = trim((string) $this->request->getPost('address'));

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

        $updateData = [
            'full_name' => $fullName,
            'email' => $email,
            'mobile_number' => $mobileNumber !== '' ? $mobileNumber : null,
            'address' => $address !== '' ? $address : null
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
        $perPage = 20;
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
                    ->orLike('users.full_name', $search);

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
                    'reports.report_date >=',
                    $fromDate . ' 00:00:00'
                );
            }

            if ($toDate !== '') {
                $builder->where(
                    'reports.report_date <=',
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
            image.image_path,
            users.full_name
        ')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'image',
                'image.report_id = reports.report_id',
                'left'
            )
            ->join(
                'users',
                'users.user_id = reports.user_id',
                'left'
            );

        $applyFilters($builder);

        $builder->orderBy(
            'reports.report_date',
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

        $residents = $db->table('users u')
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
            )
            ->where('u.role', 'resident')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $puroks = $db->table('puroks')
            ->select('purok_id, purok_name')
            ->where('is_active', 1)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/residents', [
            'residents' => $residents,
            'puroks'    => $puroks,
        ]);
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

    $settings = $db->table('settings')
        ->orderBy('setting_id', 'ASC')
        ->get()
        ->getRowArray();

    return view('admin/settings', [
        'settings' => $settings
    ]);
}

    // =========================
    // ADMIN ACCOUNT
    // =========================
    public function account()
    {
        return view('admin/account');
    }



    // =========================
    // ADMIN ANNOUNCEMENTS
    // =========================
    public function announcements()
    {
        $db = \Config\Database::connect();

        $announcements = $db->table('announcements a')
            ->select('
            a.announcement_id,
            a.user_id,
            a.title,
            a.content,
            a.category,
            a.publish_date,
            a.status,
            a.created_at,
            a.updated_at,
            u.full_name AS author_name
        ')
            ->join('users u', 'u.user_id = a.user_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalAnnouncements = $db->table('announcements')
            ->countAllResults();

        $publishedAnnouncements = $db->table('announcements')
            ->where('status', 'Published')
            ->countAllResults();

        $draftAnnouncements = $db->table('announcements')
            ->where('status', 'Draft')
            ->countAllResults();

        return view('admin/announcements', [
            'announcements' => $announcements,
            'totalAnnouncements' => $totalAnnouncements,
            'publishedAnnouncements' => $publishedAnnouncements,
            'draftAnnouncements' => $draftAnnouncements
        ]);
    }




    public function createAnnouncement()
    {
        $announcementModel = new \App\Models\AnnouncementModel();

        $userId = (int) session()->get('user_id');

        $title = trim((string) $this->request->getPost('title'));
        $content = trim((string) $this->request->getPost('content'));
        $category = trim((string) $this->request->getPost('category'));
        $publishDate = trim((string) $this->request->getPost('publishDate'));
        $status = trim((string) $this->request->getPost('status'));

        // Required fields
        if ($title === '' || $content === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Title and content are required.');
        }

        // Allowed statuses
        $allowedStatuses = [
            'Published',
            'Draft',
            'Archived'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'Draft';
        }

        // Default category
        if ($category === '') {
            $category = 'General';
        }

        $data = [
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'publish_date' => $publishDate !== ''
                ? $publishDate
                : null,
            'status' => $status
        ];

        $announcementId = $announcementModel->insert($data);

        if ($announcementId === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to create announcement.');
        }

        return redirect()->to('/admin/announcements')
            ->with('success', 'Announcement created successfully.');
    }

    public function deleteAnnouncement($announcementId)
    {
        $announcementModel = new \App\Models\AnnouncementModel();

        $announcement = $announcementModel->find($announcementId);

        if (!$announcement) {
            return redirect()->to('/admin/announcements')
                ->with('error', 'Announcement not found.');
        }

        $announcementModel->delete($announcementId);

        return redirect()->to('/admin/announcements')
            ->with('success', 'Announcement deleted successfully.');
    }

    public function updateAnnouncement($announcementId)
    {
        $announcementModel = new \App\Models\AnnouncementModel();

        $announcement = $announcementModel->find($announcementId);

        if (!$announcement) {
            return redirect()->to('/admin/announcements')
                ->with('error', 'Announcement not found.');
        }

        $title = trim((string) $this->request->getPost('title'));
        $content = trim((string) $this->request->getPost('content'));
        $category = trim((string) $this->request->getPost('category'));
        $publishDate = trim((string) $this->request->getPost('publishDate'));

        if ($title === '' || $content === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Title and content are required.');
        }

        if ($category === '') {
            $category = 'General';
        }

        $announcementModel->update($announcementId, [
            'title'        => $title,
            'content'      => $content,
            'category'     => $category,
            'publish_date' => $publishDate !== '' ? $publishDate : null,
            'status'       => 'Published'
        ]);

        return redirect()->to('/admin/announcements')
            ->with('success', 'Announcement updated successfully.');
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
                reports.latitude,
                reports.longitude,
                reports.address,
                reports.status,
                category.category_name,
                image.image_path
            ')
            ->join(
                'categories category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'image',
                'image.report_id = reports.report_id',
                'left'
            )
            ->where('reports.latitude IS NOT NULL')
            ->where('reports.longitude IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($reports as &$report) {
            $report['image_url'] = !empty($report['image_path'])
                ? base_url($report['image_path'])
                : null;
        }

        unset($report);

        return view('map/index', [
            'reports' => $reports
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
        'DD/MM/YYYY'
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

        'announcement_notifications' =>
            $this->request->getPost('announcement_notifications') ? 1 : 0,

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
