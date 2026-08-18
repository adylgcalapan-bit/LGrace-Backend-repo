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
    public function resident()
    {
        $db = \Config\Database::connect();

        $userId = (int) session()->get('user_id');

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

        return view('resident/dashboard', [
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'progressReports' => $progressReports,
            'resolvedReports' => $resolvedReports,
            'recentReports' => $recentReports
        ]);
    }
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

    // =========================
    // SUBMIT REPORT PAGE
    // =========================
    public function report()
    {
        return view('resident/report');
    }

    // =========================
    // RESIDENT - MY REPORTS
    // =========================
    public function myReports()
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        $reports = $db->table('reports')
            ->select('
                reports.*,
                category.category_name,
                image.image_path
            ')
            ->join(
                'category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'image',
                'image.report_id = reports.report_id',
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



    public function openAdminNotification($notificationId)
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

        $resident = $db->table('users')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        if (!$resident) {
            return redirect()->to('/resident/dashboard')
                ->with('error', 'Resident account not found.');
        }

        return view('resident/profile', [
            'resident' => $resident
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

        $userId = session()->get('user_id');

        $report = $db->table('reports')
            ->select('
            reports.*,
            category.category_name,
            image.image_path
        ')
            ->join(
                'category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'image',
                'image.report_id = reports.report_id',
                'left'
            )
            ->where('reports.report_id', $id)
            ->where('reports.user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$report) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Report not found.');
        }

        return view('resident/report-details', [
            'report' => $report
        ]);
    }
    // =========================
    // ADMIN - REPORTS
    // =========================
    public function reports()
    {
        $db = \Config\Database::connect();

        $reports = $db->table('reports')
            ->select('
                reports.*,
                category.category_name,
                image.image_path,
                users.full_name
            ')
            ->join(
                'category',
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
            )
            ->orderBy('reports.report_id', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/reports', [
            'reports' => $reports
        ]);
    }

    // =========================
    // ADMIN - RESIDENTS
    // =========================
    public function residents()
    {
        $db = \Config\Database::connect();

        $residents = $db->table('users')
            ->select('
            user_id,
            full_name,
            email,
            mobile_number,
            username,
            address,
            profile_image,
            created_at
        ')
            ->where('role', 'resident')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/residents', [
            'residents' => $residents
        ]);
    }



    // =========================
    // ADMIN - CATEGORIES
    // =========================
    public function categories()
    {
        return view('admin/categories');
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
        return view('admin/settings');
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
                reports.longtitude,
                reports.address,
                reports.status,
                category.category_name,
                image.image_path
            ')
            ->join(
                'category',
                'category.category_id = reports.category_id',
                'left'
            )
            ->join(
                'image',
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

        return view('map/index', [
            'reports' => $reports
        ]);
    }
}
