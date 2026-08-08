<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function showLogin()
    {
        return view('auth/login');
    }

   public function login()
{
    $userModel = new UserModel();

    $email = trim((string) $this->request->getPost('email'));
    $password = (string) $this->request->getPost('password');

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

    // Save logged-in user information sa session
    session()->set([
        'user_id'   => $user['user_id'],
        'full_name' => $user['full_name'],
        'email'     => $user['email'],
        'username'  => $user['username'],
        'role'      => $user['role'],
        'logged_in' => true,
    ]);

    // Redirect depende sa role
    if ($user['role'] === 'admin') {
        return redirect()->to('/admin/dashboard');
    }

    return redirect()->to('/resident/dashboard');
}

    public function register()
    {
        return view('auth/register');
    }

    public function registerSubmit()
    {
        $userModel = new UserModel();

        $fullName = trim((string) $this->request->getPost('fullName'));
        $email = trim((string) $this->request->getPost('email'));
        $mobileNumber = trim((string) $this->request->getPost('mobileNumber'));
        $username = trim((string) $this->request->getPost('registerUsername'));
        $address = trim((string) $this->request->getPost('address'));
        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('confirmPassword');

        if (
            $fullName === '' ||
            $email === '' ||
            $username === '' ||
            $password === '' ||
            $confirmPassword === ''
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please complete all required fields.');
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
            'profile_image' => null,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'resident',
        ]);

        return redirect()->to('/login')
            ->with('success', 'Account created successfully. You can now log in.');

            
    }



    public function logout()
{
    session()->destroy();

    return redirect()->to('/login');
}
}






