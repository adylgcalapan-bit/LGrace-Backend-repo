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
        $purokId = (int) $this->request->getPost('purok_id');
        $description = trim((string) $this->request->getPost('description'));
        $incidentDate = trim(
            (string) $this->request->getPost('incident_date')
        );
        $latitude = trim((string) $this->request->getPost('latitude'));
        $longitude = trim((string) $this->request->getPost('longitude'));
        $address = trim((string) $this->request->getPost('address'));
        $isAnonymous = $this->request->getPost('is_anonymous') ? 1 : 0;

        // =====================================
        // Validate required report fields
        // =====================================

        if (
            $title === '' ||
            $categoryId <= 0 ||
            $purokId <= 0 ||
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
        // Validate Date of Incident
        // =====================================

        $manilaTimezone = new \DateTimeZone('Asia/Manila');

        $incidentDateObject = \DateTime::createFromFormat(
            '!Y-m-d',
            $incidentDate,
            $manilaTimezone
        );

        $validIncidentDate =
            $incidentDateObject &&
            $incidentDateObject->format('Y-m-d') === $incidentDate;

        if (!$validIncidentDate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid incident date.');
        }

        $today = new \DateTime(
            'today',
            $manilaTimezone
        );

        if ($incidentDateObject > $today) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'The date of incident cannot be in the future.'
                );
        }


        $latitudeValue = (float) $latitude;
        $longitudeValue = (float) $longitude;

        $isInsideSaguing =
            $latitudeValue >= 6.955 &&
            $latitudeValue <= 7.005 &&
            $longitudeValue >= 125.055 &&
            $longitudeValue <= 125.105;

        if (!$isInsideSaguing) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Report location must be within Barangay Saguing, Makilala, Cotabato.'
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
        // Validate Purok of report location
        // =====================================

        $purok = $db->table('puroks')
            ->select('purok_id')
            ->where('purok_id', $purokId)
            ->where('is_active', 1)
            ->where('latitude IS NOT NULL', null, false)
            ->where('longitude IS NOT NULL', null, false)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please select a valid Purok for the report location.'
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

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
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

            $clientExtension = strtolower(
                $photo->getClientExtension()
            );

            if (!in_array(
                $clientExtension,
                $allowedExtensions,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Each photo must have a JPG, JPEG, PNG, or WebP file extension.'
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
        // Prevent rapid duplicate submissions
        // =====================================

        $submissionFingerprint = hash(
            'sha256',
            (string) json_encode(
                [
                    'user_id'      => $userId,
                    'title'        => $title,
                    'category_id'  => $categoryId,
                    'purok_id'     => $purokId,
                    'description'  => $description,
                    'incident_date' => $incidentDate,
                    'latitude'     => number_format(
                        $latitudeValue,
                        6,
                        '.',
                        ''
                    ),
                    'longitude'    => number_format(
                        $longitudeValue,
                        6,
                        '.',
                        ''
                    ),
                    'address'      => $address,
                    'is_anonymous' => $isAnonymous,
                ],
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            )
        );

        $lastSubmissionFingerprint =
            (string) session()->get(
                'last_report_submission_fingerprint'
            );

        $lastSubmissionTime =
            (int) session()->get(
                'last_report_submission_time'
            );

        // Same resident + same report details submitted again
        // within 10 seconds = treat as accidental duplicate.
        if (
            $lastSubmissionFingerprint !== '' &&
            hash_equals(
                $lastSubmissionFingerprint,
                $submissionFingerprint
            ) &&
            $lastSubmissionTime > 0 &&
            (time() - $lastSubmissionTime) <= 10
        ) {
            return redirect()
                ->to('/resident/my-reports')
                ->with(
                    'success',
                    'Your report was already submitted successfully.'
                );
        }

        // =====================================
        // Save report
        // =====================================

        $reportNo = $this->getNextAvailableReportNo($db);

        $reportId = $reportModel->insert([
            'report_no' => $reportNo,
            'user_id' => $userId,
            'is_anonymous' => $isAnonymous,
            'title' => $title,
            'description' => $description,
            'incident_date' => $incidentDate,
            'category_id' => $categoryId,
            'purok_id' => $purokId,
            'latitude' => $latitude,
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

        // Remember this successful submission.
        // This prevents the same request from being saved twice.
        session()->set([
            'last_report_submission_fingerprint'
            => $submissionFingerprint,

            'last_report_submission_time'
            => time(),
        ]);

        // =====================================
        // Notify all admins about new report
        // ONLY if Report Notifications is ON
        // =====================================

        $settings = $this->getSystemSettings();

        if ((int) ($settings['report_notifications'] ?? 1) === 1) {

            try {

                $notificationModel = new NotificationModel();

                $admins = $db->table('users')
                    ->select('user_id, email, full_name')
                    ->where('role', 'admin')
                    ->get()
                    ->getResultArray();

                foreach ($admins as $admin) {

                    $adminId = (int) ($admin['user_id'] ?? 0);

                    if ($adminId <= 0) {
                        continue;
                    }

                    $notificationModel->insert([
                        'user_id'   => $adminId,
                        'report_id' => $reportId,
                        'message'   => 'New report submitted: ' . $title,
                        'status'    => 'Unread',
                        'date'      => gmdate('Y-m-d H:i:s')
                    ]);

                    // Send Gmail alert only if Email Notifications is ON
                    if ((int) ($settings['email_notifications'] ?? 1) === 1) {

                        $adminEmail = trim((string) ($admin['email'] ?? ''));

                        if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {

                            try {
                                $emailService = service('email');
                                $emailService->clear(true);

                                $emailConfig = config('Email');

                                $emailService->setFrom(
                                    $emailConfig->fromEmail,
                                    $emailConfig->fromName
                                );

                                $emailService->setTo($adminEmail);

                                $emailService->setSubject(
                                    'New Community Report Submitted'
                                );

                                $emailService->setMessage(
                                    '<div style="font-family: Arial, sans-serif; line-height: 1.6;">
                    <h2>New Community Report</h2>

                    <p>Hello ' .
                                        esc($admin['full_name'] ?? 'Administrator') .
                                        ',</p>

                    <p>A new community report has been submitted.</p>

                    <p>
                        <strong>Report:</strong> ' .
                                        esc($title) .
                                        '</p>

                   <p>
    Please log in to the Community Visibility System
    to review the report.
</p>

<hr>

<small>
    Barangay Saguing Community Visibility System
</small>

<p style="
    font-size: 12px;
    color: #6c757d;
    margin-top: 20px;
">
    This is an automated message from the Community Visibility System.
    Please do not reply to this email.
</p>

</div>'
                                );

                                if (! $emailService->send()) {
                                    log_message(
                                        'error',
                                        'Admin report email notification failed for admin ID: '
                                            . $adminId
                                    );
                                }
                            } catch (\Throwable $e) {

                                log_message(
                                    'error',
                                    'Unable to send admin report email notification: {message}',
                                    ['message' => $e->getMessage()]
                                );
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {

                // Report is already saved.
                // Notification failure should not delete the report.
                log_message(
                    'error',
                    'Unable to create admin notification for report {reportId}: {message}',
                    [
                        'reportId' => $reportId,
                        'message'  => $e->getMessage()
                    ]
                );
            }
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

        // =====================================
        // RESOLVED 7-DAY TIMER
        // =====================================

        $currentStatus =
            trim((string) ($report['status'] ?? ''));

        $currentPriority =
            trim((string) ($report['priority'] ?? ''));

        if ($status === 'Resolved') {

            /*
     * Start the timer ONLY when the report
     * first becomes Resolved.
     *
     * If it is already Resolved and admin
     * only changes priority, keep the
     * original resolved_at.
     */
            if (
                $currentStatus !== 'Resolved' ||
                empty($report['resolved_at'])
            ) {
                $updateData['resolved_at'] =
                    date('Y-m-d H:i:s');
            }
        } else {

            /*
     * If reopened / changed away from
     * Resolved, clear the old timer.
     */
            $updateData['resolved_at'] = null;
        }


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

        $finalPriority =
            $priority !== ''
            ? $priority
            : $currentPriority;

        $this->logAdminAudit(
            'UPDATE_REPORT_MODERATION',
            'report',
            $reportId,
            'Status changed from '
                . ($currentStatus !== '' ? $currentStatus : 'N/A')
                . ' to '
                . $status
                . '; Priority changed from '
                . ($currentPriority !== '' ? $currentPriority : 'N/A')
                . ' to '
                . ($finalPriority !== '' ? $finalPriority : 'N/A')
                . '.'
        );

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
        $images = $db->table('images')
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
        $db->table('images')
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
        $categories = $db->table('categories')
            ->select('category_id, category_name')
            ->orderBy('category_name', 'ASC')
            ->get()
            ->getResultArray();

        // Get Puroks with configured map locations.
        // This is for the REPORT location, not the resident's home Purok.
        $puroks = $db->table('puroks')
            ->select('purok_id, purok_name, latitude, longitude')
            ->where('is_active', 1)
            ->where('latitude IS NOT NULL', null, false)
            ->where('longitude IS NOT NULL', null, false)
            ->orderBy('purok_name', 'ASC')
            ->get()
            ->getResultArray();

        // Get all current photos
        $images = $db->table('images')
            ->select('image_id, image_path')
            ->where('report_id', $reportId)
            ->orderBy('image_id', 'ASC')
            ->get()
            ->getResultArray();

        return view('resident/edit-report', [
            'report'     => $report,
            'categories' => $categories,
            'puroks'     => $puroks,
            'images'     => $images,
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
        // Get submitted values
        $title = trim(
            (string) $this->request->getPost('title')
        );

        $description = trim(
            (string) $this->request->getPost('description')
        );

        $categoryId =
            (int) $this->request->getPost('category_id');

        $purokId =
            (int) $this->request->getPost('purok_id');

        $incidentDate = trim(
            (string) $this->request->getPost('incident_date')
        );

        $latitude = trim(
            (string) $this->request->getPost('latitude')
        );

        $longitude = trim(
            (string) $this->request->getPost('longitude')
        );

        $address = trim(
            (string) $this->request->getPost('address')
        );

        $isAnonymous =
            $this->request->getPost('is_anonymous')
            ? 1
            : 0;


        // Basic validation
        if (
            $title === '' ||
            $description === '' ||
            $categoryId <= 0 ||
            $purokId <= 0 ||
            !is_numeric($latitude) ||
            !is_numeric($longitude)
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please complete all required report information.');
        }

        // =====================================
        // Validate Date of Incident
        // =====================================

        $manilaTimezone =
            new \DateTimeZone('Asia/Manila');

        $incidentDateObject =
            \DateTime::createFromFormat(
                '!Y-m-d',
                $incidentDate,
                $manilaTimezone
            );

        $validIncidentDate =
            $incidentDateObject &&
            $incidentDateObject->format('Y-m-d')
            === $incidentDate;

        if (!$validIncidentDate) {

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid incident date.'
                );
        }

        $today = new \DateTime(
            'today',
            $manilaTimezone
        );

        if ($incidentDateObject > $today) {

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'The date of incident cannot be in the future.'
                );
        }

        $latitudeValue = (float) $latitude;
        $longitudeValue = (float) $longitude;

        $isInsideSaguing =
            $latitudeValue >= 6.955 &&
            $latitudeValue <= 7.005 &&
            $longitudeValue >= 125.055 &&
            $longitudeValue <= 125.105;

        if (!$isInsideSaguing) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Report location must be within Barangay Saguing, Makilala, Cotabato.'
                );
        }

        // Make sure category exists
        $category = $db->table('categories')
            ->where('category_id', $categoryId)
            ->get()
            ->getRowArray();

        if (!$category) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid category selected.');
        }

        // =====================================
        // Validate Purok of report location
        // =====================================

        $purok = $db->table('puroks')
            ->select('purok_id')
            ->where('purok_id', $purokId)
            ->where('is_active', 1)
            ->where('latitude IS NOT NULL', null, false)
            ->where('longitude IS NOT NULL', null, false)
            ->get()
            ->getRowArray();

        if (!$purok) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Please select a valid Purok for the report location.'
                );
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

            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ];

            $clientExtension = strtolower(
                $photo->getClientExtension()
            );

            if (!in_array(
                $clientExtension,
                $allowedExtensions,
                true
            )) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Photo must have a JPG, JPEG, PNG, or WebP file extension.'
                    );
            }

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
        $existingImage = $db->table('images')
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

                'incident_date' => $incidentDate,

                'category_id' => $categoryId,

                'purok_id' => $purokId,

                'latitude' => $latitude,

                // Actual column name in your database
                'longtitude' => $longitude,

                'address' =>
                $address !== ''
                    ? $address
                    : null,

                'is_anonymous' => $isAnonymous
            ]);

        // Replace photo only if resident selected a new one
        if ($hasNewPhoto) {

            if ($existingImage) {

                $db->table('images')
                    ->where('image_id', $existingImage['image_id'])
                    ->update([
                        'image_path' => $newImagePath
                    ]);
            } else {

                $db->table('images')->insert([
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

        // =====================================
        // Optional replacement photos
        // Maximum: 5 photos
        // =====================================

        $photos =
            $this->request->getFileMultiple('photos') ?? [];

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

        $hasNewPhotos = count($photos) > 0;

        // Maximum 5 replacement photos
        if (count($photos) > 5) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'You can upload a maximum of 5 photos only.'
                );
        }

        // =====================================
        // Validate every replacement photo
        // =====================================

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ];

        foreach ($photos as $photo) {

            if (!$photo->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'One of the selected photos is invalid.'
                    );
            }

            $clientExtension = strtolower(
                $photo->getClientExtension()
            );

            if (
                !in_array(
                    $clientExtension,
                    $allowedExtensions,
                    true
                )
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Photos must have JPG, JPEG, PNG, or WebP file extensions.'
                    );
            }

            if (
                !in_array(
                    $photo->getMimeType(),
                    $allowedMimeTypes,
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
        // Get ALL existing report photos
        // before replacement
        // =====================================

        $existingImages = $db->table('images')
            ->select('image_id, image_path')
            ->where('report_id', $reportId)
            ->orderBy('image_id', 'ASC')
            ->get()
            ->getResultArray();

        $newImagePaths = [];
        $newPhysicalPaths = [];

        // =====================================
        // Save new physical photos first
        // =====================================

        if ($hasNewPhotos) {

            $uploadDirectory =
                FCPATH . 'uploads/reports';

            if (!is_dir($uploadDirectory)) {
                mkdir(
                    $uploadDirectory,
                    0775,
                    true
                );
            }

            foreach ($photos as $photo) {

                $newName = $photo->getRandomName();

                try {

                    $photo->move(
                        $uploadDirectory,
                        $newName
                    );
                } catch (\Throwable $e) {

                    // Remove any new files already moved
                    foreach ($newPhysicalPaths as $filePath) {

                        if (is_file($filePath)) {
                            unlink($filePath);
                        }
                    }

                    return redirect()->back()
                        ->withInput()
                        ->with(
                            'error',
                            'Unable to save the selected photos.'
                        );
                }

                $newImagePath =
                    'uploads/reports/' . $newName;

                $newImagePaths[] =
                    $newImagePath;

                $newPhysicalPaths[] =
                    FCPATH . $newImagePath;
            }
        }

        // =====================================
        // Start transaction
        // =====================================

        $db->transStart();

        // Update report information
        $db->table('reports')
            ->where('report_id', $reportId)
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->update([

                'title' => $title,

                'description' => $description,

                'incident_date' => $incidentDate,

                'category_id' => $categoryId,

                'purok_id' => $purokId,

                'latitude' => $latitude,

                // Actual column name in your database
                'longtitude' => $longitude,

                'address' =>
                $address !== ''
                    ? $address
                    : null,

                'is_anonymous' => $isAnonymous,
            ]);

        // =====================================
        // Replace photos only when resident
        // selected a new photo set
        // =====================================

        if ($hasNewPhotos) {

            // Remove old image database rows
            $db->table('images')
                ->where('report_id', $reportId)
                ->delete();

            // Insert all new image rows
            foreach ($newImagePaths as $imagePath) {

                $db->table('images')->insert([
                    'report_id'  => $reportId,
                    'image_path' => $imagePath,
                ]);
            }
        }

        $db->transComplete();

        // =====================================
        // Rollback physical-file cleanup
        // =====================================

        if ($db->transStatus() === false) {

            foreach ($newPhysicalPaths as $filePath) {

                if (is_file($filePath)) {
                    unlink($filePath);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update the report.'
                );
        }

        // =====================================
        // Remove OLD physical photos only after
        // successful database replacement
        // =====================================

        if ($hasNewPhotos) {

            foreach ($existingImages as $existingImage) {

                if (empty($existingImage['image_path'])) {
                    continue;
                }

                $oldPhysicalPath =
                    FCPATH . ltrim(
                        $existingImage['image_path'],
                        '/\\'
                    );

                if (is_file($oldPhysicalPath)) {
                    unlink($oldPhysicalPath);
                }
            }
        }
        return redirect()->to('/resident/my-reports')
            ->with('success', 'Report updated successfully.');
    }

    private function logAdminAudit(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $details = null
    ): void {
        $adminUserId = (int) session()->get('user_id');

        if ($adminUserId <= 0) {
            return;
        }

        try {
            $db = \Config\Database::connect();

            $db->table('admin_audit_logs')->insert([
                'admin_user_id' => $adminUserId,
                'action'        => $action,
                'target_type'   => $targetType,
                'target_id'     => $targetId,
                'details'       => $details,
                'ip_address'    => $this->request->getIPAddress(),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Admin audit log failed: {message}',
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    private function getNextAvailableReportNo($db): int
    {
        $rows = $db->table('reports')
            ->select('report_no')
            ->where('report_no IS NOT NULL', null, false)
            ->orderBy('report_no', 'ASC')
            ->get()
            ->getResultArray();

        $nextNumber = 1;

        foreach ($rows as $row) {
            $currentNumber =
                (int) ($row['report_no'] ?? 0);

            if ($currentNumber < $nextNumber) {
                continue;
            }

            if ($currentNumber > $nextNumber) {
                break;
            }

            $nextNumber++;
        }

        return $nextNumber;
    }
}
