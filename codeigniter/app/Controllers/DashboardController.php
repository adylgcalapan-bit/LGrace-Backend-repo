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
        return view('resident/myreports');
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
