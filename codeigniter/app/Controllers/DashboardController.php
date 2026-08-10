<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function admin()
    {
        return view('admin/dashboard');
    }

    public function resident()
    {
        return view('resident/dashboard');
    }

    public function report()
    {
        return view('resident/report');
    }

    public function myReports()
{
    $db = \Config\Database::connect();

    $userId = session()->get('user_id');

    $reports = $db->table('reports')
        ->select('reports.*, category.category_name')
        ->join('category', 'category.category_id = reports.category_id', 'left')
        ->where('reports.user_id', $userId)
        ->orderBy('reports.report_id', 'DESC')
        ->get()
        ->getResultArray();

    return view('resident/myreports', [
        'reports' => $reports
    ]);
}

    public function notifications()
    {
        return view('resident/notifications');
    }

    public function profile()
    {
        return view('resident/profile');
    }

    public function reportDetails()
    {
        return view('resident/report-details');
    }

    public function reports()
    {
        return view('admin/reports');
    }

    public function residents()
    {
        return view('admin/residents');
    }

    public function categories()
    {
        return view('admin/categories');
    }

    public function notificationsAdmin()
    {
        return view('admin/notifications');
    }

    public function settings()
    {
        return view('admin/settings');
    }

    public function account()
    {
        return view('admin/account');
    }

    public function announcements()
    {
        return view('admin/announcements');
    }

    public function map()
    {
        return view('map/index');
    }
}
