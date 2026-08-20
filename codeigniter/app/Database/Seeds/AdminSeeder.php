<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $fullName = env('BOOTSTRAP_ADMIN_NAME', 'System Admin');
        $username = env('BOOTSTRAP_ADMIN_USERNAME', 'admin');
        $email    = env('BOOTSTRAP_ADMIN_EMAIL', 'admin@gmail.com');
        $password = env('BOOTSTRAP_ADMIN_PASSWORD');

        if (empty($password)) {
            echo "AdminSeeder stopped: BOOTSTRAP_ADMIN_PASSWORD is missing in .env." . PHP_EOL;
            return;
        }

        $users = $this->db->table('users');

        // Check existing email.
        $existingByEmail = $users
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if ($existingByEmail) {
            if (($existingByEmail['role'] ?? '') === 'admin') {
                echo "Admin already exists. No duplicate created." . PHP_EOL;
                return;
            }

            echo "AdminSeeder stopped: the email already belongs to a non-admin account." . PHP_EOL;
            return;
        }

        // Check existing username.
        $existingByUsername = $users
            ->where('username', $username)
            ->get()
            ->getRowArray();

        if ($existingByUsername) {
            echo "AdminSeeder stopped: username already exists." . PHP_EOL;
            return;
        }

        $users->insert([
            'full_name'      => $fullName,
            'email'          => $email,
            'mobile_number'  => null,
            'username'       => $username,
            'address'        => null,
            'purok_id'       => null,
            'profile_image'  => null,
            'password'       => password_hash($password, PASSWORD_DEFAULT),
            'role'           => 'admin',
            'is_active'      => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        echo "Admin account created successfully." . PHP_EOL;
    }
}
