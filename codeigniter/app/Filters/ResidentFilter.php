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

        // =====================================
        // SESSION TIMEOUT
        // =====================================

        $session = session();
        $now = time();

        // Default: 30 minutes
        $timeoutMinutes = 30;

        try {
            $db = \Config\Database::connect();

            $settings = $db->table('settings')
                ->select('session_timeout')
                ->orderBy('setting_id', 'ASC')
                ->get()
                ->getRowArray();

            $timeoutMinutes = (int) ($settings['session_timeout'] ?? 30);

            // Safety limits
            $timeoutMinutes = max(5, min(240, $timeoutMinutes));
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Unable to load session timeout setting: {message}',
                ['message' => $e->getMessage()]
            );
        }

        $lastActivity = (int) $session->get('last_activity');

        if (
            $lastActivity > 0 &&
            ($now - $lastActivity) >= ($timeoutMinutes * 60)
        ) {
            $userId = (int) $session->get('user_id');

            // Remove Remember Me token
            if ($userId > 0) {
                try {
                    $db = \Config\Database::connect();

                    $db->table('remember_tokens')
                        ->where('user_id', $userId)
                        ->delete();
                } catch (\Throwable $e) {
                    log_message(
                        'error',
                        'Unable to remove remember token after session timeout: {message}',
                        ['message' => $e->getMessage()]
                    );
                }
            }

            $session->destroy();

            $response = redirect()->to('/login')
                ->with(
                    'error',
                    'Your session expired due to inactivity. Please log in again.'
                );

            $response->deleteCookie('remember_token');

            return $response;
        }

        // User is still active
        $session->set('last_activity', $now);

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
