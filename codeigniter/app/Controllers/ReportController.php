<?php

namespace App\Controllers;

use App\Models\LocationModel;

class ReportController extends BaseController
{
    public function create()
    {
        $reportModel = new LocationModel();

        $title = trim((string) $this->request->getPost('title'));
        $categoryId = (int) $this->request->getPost('category_id');
        $description = trim((string) $this->request->getPost('description'));

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        // Validate required fields
        if (
            $title === '' ||
            $categoryId <= 0 ||
            $description === '' ||
            $latitude === null ||
            $latitude === '' ||
            $longitude === null ||
            $longitude === ''
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please complete all required report fields and select a location.'
                );
        }

        // Logged-in resident
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // Save report
        $reportModel->insert([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'latitude' => $latitude,

            // Actual column name sa inyong database
            'longtitude' => $longitude,

            'address' => null,
            'status' => 'Pending',
        ]);

        return redirect()->to('/resident/my-reports')
            ->with(
                'success',
                'Report submitted successfully.'
            );
    }
}