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

    // =====================================
    // Get logged-in resident
    // =====================================

    $userId = (int) session()->get('user_id');

    if ($userId <= 0) {
        return redirect()->to('/login');
    }

    // =====================================
    // Get submitted report values
    // =====================================

    $title = trim((string) $this->request->getPost('title'));
    $categoryId = (int) $this->request->getPost('category_id');
    $description = trim((string) $this->request->getPost('description'));
    $latitude = trim((string) $this->request->getPost('latitude'));
    $longitude = trim((string) $this->request->getPost('longitude'));
    $address = trim((string) $this->request->getPost('address'));

    // =====================================
    // Validate required report fields
    // =====================================

    if (
        $title === '' ||
        $categoryId <= 0 ||
        $description === '' ||
        $latitude === '' ||
        $longitude === '' ||
        !is_numeric($latitude) ||
        !is_numeric($longitude)
    ) {
        return redirect()->back()
            ->withInput()
            ->with(
                'error',
                'Please complete all required report information.'
            );
    }

    // =====================================
    // Check if selected category exists
    // =====================================

    $db = \Config\Database::connect();

    $category = $db->table('categories')
        ->where('category_id', $categoryId)
        ->get()
        ->getRowArray();

    if (!$category) {
        return redirect()->back()
            ->withInput()
            ->with(
                'error',
                'Invalid category selected.'
            );
    }

    // =====================================
    // Get uploaded photos
    // Maximum: 5 photos
    // =====================================

    $photos = $this->request->getFileMultiple('photos') ?? [];

    // Remove empty upload entries
    $photos = array_values(
        array_filter(
            $photos,
            static function ($photo) {
                return $photo !== null
                    && $photo->getError() !== UPLOAD_ERR_NO_FILE;
            }
        )
    );

    // =====================================
    // Maximum 5 photos
    // =====================================

    if (count($photos) > 5) {
        return redirect()->back()
            ->withInput()
            ->with(
                'error',
                'You can upload a maximum of 5 photos only.'
            );
    }

    // =====================================
    // Validate every uploaded photo
    // =====================================

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    foreach ($photos as $photo) {

        if (!$photo->isValid()) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'One of the uploaded photos is invalid.'
                );
        }

        if (
            !in_array(
                $photo->getMimeType(),
                $allowedTypes,
                true
            )
        ) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Only JPG, PNG, and WebP images are allowed.'
                );
        }

        // Maximum 5 MB per photo
        if ($photo->getSize() > (5 * 1024 * 1024)) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Each photo must not exceed 5 MB.'
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
        'longitude' => $longitude,
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
    // Prepare report upload folder
    // =====================================

    $uploadPath = FCPATH . 'uploads/reports';

    if (!is_dir($uploadPath)) {
        if (
            !mkdir($uploadPath, 0775, true) &&
            !is_dir($uploadPath)
        ) {
            $reportModel->delete($reportId);

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to prepare the photo upload folder.'
                );
        }
    }

    // Keep track of files already saved.
    // If one photo fails, they will all be removed.
    $savedFiles = [];

    // =====================================
    // Save all uploaded photos
    // =====================================

    foreach ($photos as $photo) {

        $newName = $photo->getRandomName();

        try {

            $photo->move(
                $uploadPath,
                $newName
            );

        } catch (\Throwable $e) {

            // Remove physical files already uploaded
            foreach ($savedFiles as $savedFile) {
                if (is_file($savedFile)) {
                    @unlink($savedFile);
                }
            }

            // Delete report.
            // Related image rows are removed by DB cascade.
            $reportModel->delete($reportId);

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to save the uploaded photos.'
                );
        }

        $physicalPath =
            $uploadPath . DIRECTORY_SEPARATOR . $newName;

        $savedFiles[] = $physicalPath;

        // =====================================
        // Save one database row per photo
        // =====================================

        $imageSaved = $imageModel->insert([
            'report_id' => $reportId,
            'image_path' => 'uploads/reports/' . $newName,
        ]);

        if ($imageSaved === false) {

            // Remove every physical image uploaded
            foreach ($savedFiles as $savedFile) {
                if (is_file($savedFile)) {
                    @unlink($savedFile);
                }
            }

            // Delete report and related image rows
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

    try {

        $notificationModel = new NotificationModel();

        $admins = $db->table('users')
            ->select('user_id')
            ->where('role', 'admin')
            ->get()
            ->getResultArray();

        foreach ($admins as $admin) {

            $adminId = (int) ($admin['user_id'] ?? 0);

            if ($adminId <= 0) {
                continue;
            }

            $notificationModel->insert([
                'user_id' => $adminId,
                'report_id' => $reportId,
                'message' => 'New report submitted: ' . $title,
                'status' => 'Unread',
                'date' => gmdate('Y-m-d H:i:s')
            ]);
        }

    } catch (\Throwable $e) {

        // Report is already saved.
        // Notification failure should not delete the report.
        log_message(
            'error',
            'Unable to create admin notification for report {reportId}: {message}',
            [
                'reportId' => $reportId,
                'message' => $e->getMessage()
            ]
        );
    }

    // =====================================
    // Successful report submission
    // =====================================

    $photoCount = count($photos);

    return redirect()->to('/resident/my-reports')
        ->with(
            'success',
            $photoCount > 0
                ? 'Report submitted successfully with ' . $photoCount . ' photo(s).'
                : 'Report submitted successfully.'
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
