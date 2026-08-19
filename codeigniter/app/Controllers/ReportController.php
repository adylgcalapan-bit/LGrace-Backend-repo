<?php

namespace App\Controllers;

use App\Models\LocationModel;
use App\Models\ImageModel;
use App\Models\NotificationModel;

class ReportController extends BaseController
{
    public function create()
    {
        $reportModel = new LocationModel();
        $imageModel = new ImageModel();

        $title = trim((string) $this->request->getPost('title'));
        $categoryId = (int) $this->request->getPost('category_id');
        $description = trim((string) $this->request->getPost('description'));
        $isAnonymous = $this->request->getPost('is_anonymous') ? 1 : 0;

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $address = trim((string) $this->request->getPost('address'));


        // =====================================
        // Validate report fields
        // =====================================

        if (
            $title === '' ||
            $categoryId <= 0 ||
            $description === '' ||
            $latitude === null ||
            $latitude === '' ||
            $longitude === null ||
            $longitude === ''
        )


            // =====================================
            // Notify all admins about new report
            // =====================================

            $notificationModel = new \App\Models\NotificationModel();



        // =====================================
        // Get logged-in resident
        // =====================================

        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // =====================================
        // Get uploaded photo
        // =====================================

        $photo = $this->request->getFile('photo');

        $hasPhoto = (
            $photo !== null &&
            $photo->getError() !== UPLOAD_ERR_NO_FILE
        );

        // =====================================
        // Validate photo if uploaded
        // =====================================

        if ($hasPhoto) {

            if (!$photo->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'The uploaded photo is invalid.'
                    );
            }

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($photo->getMimeType(), $allowedTypes, true)) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Only JPG, PNG, and WebP images are allowed.'
                    );
            }

            // Maximum 5 MB
            if ($photo->getSize() > (5 * 1024 * 1024)) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Photo must not exceed 5 MB.'
                    );
            }
        }

        // =====================================
        // Save report
        // =====================================

        $reportId = $reportModel->insert([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'latitude' => $latitude,
            'is_anonymous' => $isAnonymous,

            // Actual DB column name
            'longtitude' => $longitude,

            'address' => $address !== '' ? $address : null,
            'status' => 'Pending',
        ]);

        if ($reportId === false) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to save the report.'
                );
        }

        // =====================================
        // Save uploaded photo
        // =====================================

        if ($hasPhoto) {

            $newName = $photo->getRandomName();

            $uploadPath =
                FCPATH . 'uploads/reports';

            try {

                $photo->move(
                    $uploadPath,
                    $newName
                );
            } catch (\Throwable $e) {

                // Remove report if image saving fails
                $reportModel->delete($reportId);

                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Unable to save the uploaded photo.'
                    );
            }

            // Save photo path in image table
            $imageSaved = $imageModel->insert([
                'report_id' => $reportId,
                'image_path' => 'uploads/reports/' . $newName,
            ]);

            if ($imageSaved === false) {

                $savedFile =
                    FCPATH . 'uploads/reports/' . $newName;

                if (is_file($savedFile)) {
                    unlink($savedFile);
                }

                $reportModel->delete($reportId);

                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Unable to save the photo information.'
                    );
            }
        }

        // =====================================
        // Notify all admins about new report
        // =====================================

        $notificationModel = new \App\Models\NotificationModel();

        $db = \Config\Database::connect();

        $admins = $db->table('users')
            ->select('user_id')
            ->where('role', 'admin')
            ->get()
            ->getResultArray();

        foreach ($admins as $admin) {
            $notificationModel->insert([
                'user_id' => $admin['user_id'],
                'report_id' => $reportId,
                'message' => 'New report submitted: ' . $title,
                'status' => 'Unread',
                'date' => gmdate('Y-m-d H:i:s')
            ]);
        }






        // =====================================
        // Successful report submission
        // =====================================

        return redirect()->to('/resident/my-reports')
            ->with(
                'success',
                'Report submitted successfully.'
            );
    }



    public function updateStatus()
    {
        $reportModel = new \App\Models\LocationModel();

        $reportId = (int) $this->request->getPost('report_id');
        $status = trim((string) $this->request->getPost('status'));
        $priority = trim((string) $this->request->getPost('priority'));

        $allowedStatuses = [
            'Pending',
            'In Progress',
            'Resolved',
            'Rejected'
        ];

        $allowedPriorities = [
            'Low',
            'Medium',
            'High'
        ];

        // Validate report ID
        if ($reportId <= 0) {
            return redirect()->back()
                ->with('error', 'Invalid report ID.');
        }

        // Validate status
        if (!in_array($status, $allowedStatuses, true)) {
            return redirect()->back()
                ->with('error', 'Invalid report status.');
        }

        // Validate priority
        if (
            $priority !== '' &&
            !in_array($priority, $allowedPriorities, true)
        ) {
            return redirect()->back()
                ->with('error', 'Invalid report priority.');
        }

        // Get the report first
        $report = $reportModel->find($reportId);

        if (!$report) {
            return redirect()->back()
                ->with('error', 'Report not found.');
        }

        // Update report
        $updateData = [
            'status' => $status
        ];

        if ($priority !== '') {
            $updateData['priority'] = $priority;
        }

        $updated = $reportModel->update(
            $reportId,
            $updateData
        );

        if ($updated === false) {
            return redirect()->back()
                ->with('error', 'Unable to update report.');
        }

        // =====================================
        // Notify resident about report update
        // =====================================

        $notificationModel =
            new \App\Models\NotificationModel();

        $notificationModel->insert([
            'user_id' => $report['user_id'],
            'report_id' => $reportId,
            'message' =>
            'Your report "' .
                $report['title'] .
                '" has been updated to ' .
                $status .
                '.',
            'status' => 'Unread',
            'date' => gmdate('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin/reports')
            ->with(
                'success',
                'Report status and priority updated successfully.'
            );
    }

    public function delete($reportId = null)
    {
        $db = \Config\Database::connect();

        $reportId = (int) $reportId;
        $userId = (int) session()->get('user_id');

        // Validate report ID
        if ($reportId <= 0) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Invalid report ID.');
        }

        // Get report and make sure it belongs to logged-in resident
        $report = $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$report) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Report not found or you are not allowed to delete it.');
        }

        // Only Pending reports can be deleted
        if (($report['status'] ?? '') !== 'Pending') {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Only Pending reports can be deleted.');
        }

        // Get associated photos before deleting database records
        $images = $db->table('image')
            ->where('report_id', $reportId)
            ->get()
            ->getResultArray();

        // Start database transaction
        $db->transStart();

        // Delete related notifications
        $db->table('notifications')
            ->where('report_id', $reportId)
            ->delete();

        // Delete image database records
        $db->table('image')
            ->where('report_id', $reportId)
            ->delete();

        // Delete report
        $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Unable to delete the report.');
        }

        // Delete physical uploaded photos after DB deletion succeeds
        foreach ($images as $image) {
            if (empty($image['image_path'])) {
                continue;
            }

            $filePath = FCPATH . ltrim($image['image_path'], '/\\');

            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        return redirect()->to('/resident/my-reports')
            ->with('success', 'Report deleted successfully.');
    }
    public function edit($reportId = null)
    {
        $db = \Config\Database::connect();

        $reportId = (int) $reportId;
        $userId = (int) session()->get('user_id');

        // Validate report ID
        if ($reportId <= 0) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Invalid report ID.');
        }

        // Get report owned by logged-in resident
        $report = $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$report) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Report not found or you are not allowed to edit it.');
        }

        // Only Pending reports can be edited
        if (($report['status'] ?? '') !== 'Pending') {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Only Pending reports can be edited.');
        }

        // Get categories
        $categories = $db->table('category')
            ->select('category_id, category_name')
            ->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->get()
            ->getResultArray();

        // Get current photo
        $image = $db->table('image')
            ->where('report_id', $reportId)
            ->orderBy('image_id', 'ASC')
            ->get()
            ->getRowArray();

        $report['image_path'] = $image['image_path'] ?? null;

        return view('resident/edit-report', [
            'report' => $report,
            'categories' => $categories
        ]);
    }
    public function updateResidentReport($reportId = null)
    {
        $db = \Config\Database::connect();

        $reportId = (int) $reportId;
        $userId = (int) session()->get('user_id');

        // Validate report ID
        if ($reportId <= 0) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Invalid report ID.');
        }

        // Get resident's own report
        $report = $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$report) {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Report not found or you are not allowed to edit it.');
        }

        // Pending reports only
        if (($report['status'] ?? '') !== 'Pending') {
            return redirect()->to('/resident/my-reports')
                ->with('error', 'Only Pending reports can be edited.');
        }

        // Get submitted values
        $title = trim((string) $this->request->getPost('title'));
        $description = trim((string) $this->request->getPost('description'));
        $categoryId = (int) $this->request->getPost('category_id');
        $latitude = trim((string) $this->request->getPost('latitude'));
        $longitude = trim((string) $this->request->getPost('longitude'));
        $address = trim((string) $this->request->getPost('address'));

        // Basic validation
        if (
            $title === '' ||
            $description === '' ||
            $categoryId <= 0 ||
            !is_numeric($latitude) ||
            !is_numeric($longitude)
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please complete all required report information.');
        }

        // Make sure category exists
        $category = $db->table('category')
            ->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$category) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid category selected.');
        }

        // Optional replacement photo
        $photo = $this->request->getFile('photo');

        $hasNewPhoto =
            $photo !== null &&
            $photo->getError() !== UPLOAD_ERR_NO_FILE;

        $newImagePath = null;
        $newPhysicalPath = null;

        if ($hasNewPhoto) {

            if (!$photo->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to upload the selected photo.');
            }

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($photo->getMimeType(), $allowedMimeTypes, true)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Photo must be JPG, PNG, or WebP.');
            }

            if ($photo->getSize() > (5 * 1024 * 1024)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Photo must not exceed 5 MB.');
            }

            $newName = $photo->getRandomName();

            try {
                $photo->move(
                    FCPATH . 'uploads/reports',
                    $newName
                );
            } catch (\Throwable $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to save the new photo.');
            }

            $newImagePath = 'uploads/reports/' . $newName;
            $newPhysicalPath = FCPATH . $newImagePath;
        }

        // Get existing image before replacing it
        $existingImage = $db->table('image')
            ->where('report_id', $reportId)
            ->orderBy('image_id', 'ASC')
            ->get()
            ->getRowArray();

        // Start transaction
        $db->transStart();

        // Update report information
        $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->update([
                'title' => $title,
                'description' => $description,
                'category_id' => $categoryId,
                'latitude' => $latitude,

                // Actual column name in your database
                'longtitude' => $longitude,

                'address' => $address !== '' ? $address : null
            ]);

        // Replace photo only if resident selected a new one
        if ($hasNewPhoto) {

            if ($existingImage) {

                $db->table('image')
                    ->where('image_id', $existingImage['image_id'])
                    ->update([
                        'image_path' => $newImagePath
                    ]);
            } else {

                $db->table('image')->insert([
                    'report_id' => $reportId,
                    'image_path' => $newImagePath
                ]);
            }
        }

        $db->transComplete();

        // Rollback cleanup if DB update failed
        if ($db->transStatus() === false) {

            if (
                $newPhysicalPath &&
                is_file($newPhysicalPath)
            ) {
                unlink($newPhysicalPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to update the report.');
        }

        // Remove old physical photo after successful replacement
        if (
            $hasNewPhoto &&
            !empty($existingImage['image_path'])
        ) {
            $oldPhysicalPath =
                FCPATH . ltrim(
                    $existingImage['image_path'],
                    '/\\'
                );

            if (is_file($oldPhysicalPath)) {
                unlink($oldPhysicalPath);
            }
        }

        return redirect()->to('/resident/my-reports')
            ->with('success', 'Report updated successfully.');
    }
}
