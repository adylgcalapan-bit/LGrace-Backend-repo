<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    // =========================
    // ADMIN DASHBOARD
    // =========================
    public function admin()
    {
        return view('admin/dashboard');
    }

    // =========================
    // RESIDENT DASHBOARD
    // =========================
    public function resident()
    {
        return view('resident/dashboard');
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
        return view('resident/profile');
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
        return view('admin/residents');
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
        return view('admin/announcements');
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