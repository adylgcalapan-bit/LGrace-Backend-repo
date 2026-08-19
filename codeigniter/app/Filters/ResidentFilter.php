<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ResidentFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Must be logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')
                ->with('error', 'Please log in first.');
        }

        // Must be a resident
        if (session()->get('role') !== 'resident') {
            return redirect()->to('/admin/dashboard')
                ->with(
                    'error',
                    'You are not allowed to access the resident area.'
                );
        }

        // Check if resident account is still active
        $userId = (int) session()->get('user_id');

        $db = \Config\Database::connect();

        $resident = $db->table('users')
            ->select('user_id, is_active')
            ->where('user_id', $userId)
            ->where('role', 'resident')
            ->get()
            ->getRowArray();

        // Account deleted, missing, or deactivated
        if (
            !$resident ||
            (int) $resident['is_active'] !== 1
        ) {
            // Remove Remember Me tokens
            if ($userId > 0) {
                $db->table('remember_tokens')
                    ->where('user_id', $userId)
                    ->delete();
            }

            // Destroy current session
            session()->destroy();

            // Remove Remember Me browser cookie
            $response = redirect()->to('/login')
                ->with(
                    'error',
                    'Your account is inactive. Please contact the administrator.'
                );

            $response->deleteCookie('remember_token');

            return $response;
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No action needed.
    }
}
