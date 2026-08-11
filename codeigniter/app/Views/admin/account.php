<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <i class="bi bi-geo-alt-fill"></i>
            <h4>CPVS</h4>
        </div>
        <ul class="menu">
            <li><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
            <li><a href="<?= base_url('admin/reports') ?>"><i class="bi bi-file-earmark-text"></i>Reports</a></li>
            <li><a href="#"><i class="bi bi-map"></i>Map View</a></li>
            <li><a href="<?= base_url('admin/residents') ?>"><i class="bi bi-people"></i>Residents</a></li>
            <li><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
           <?= view('admin/notification_menu') ?>
            <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
            <li class="active"><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
            <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <h2>Account Management</h2>
                <p>Manage your administrator account details and security.</p>
            </div>
            <div class="top-actions">
                <div class="profile">
                    <img id="topProfileImage" src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                    <div><strong>Admin</strong><br><small>Administrator</small></div>
                </div>
            </div>
        </header>

        <section class="card p-4 mb-4">
            <h4 class="mb-4">Admin Profile Summary</h4>
            <div class="row g-3">
                <div class="col-md-6"><p class="mb-2"><strong>Name:</strong> John Dela Cruz</p><p class="mb-2"><strong>Email:</strong> admin@cpvs.com</p><p class="mb-2"><strong>Contact:</strong> 09123456789</p></div>
                <div class="col-md-6"><p class="mb-2"><strong>Role:</strong> Administrator</p><p class="mb-2"><strong>Account Status:</strong> Active</p><p class="mb-2"><strong>Last Login:</strong> Jul 30, 2026</p></div>
            </div>
        </section>

        <section class="card p-4 mb-4">
            <h4 class="mb-4">Profile Picture</h4>
            <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                <img id="profilePreview" class="profile-preview" src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin Profile">
                <div class="w-100">
                    <p class="text-muted mb-2">Upload a new profile photo to update your account image.</p>
                    <input class="form-control" type="file" id="profileImageInput" accept="image/*">
                    <button class="btn btn-outline-success mt-3" id="uploadPhotoBtn" type="button">Upload Photo</button>
                </div>
            </div>
        </section>

        <section class="card p-4 mb-4">
            <h4 class="mb-4">Personal Information</h4>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">First Name</label><input type="text" class="form-control" value="John"></div>
                <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" class="form-control" value="Dela Cruz"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="admin@cpvs.com"></div>
                <div class="col-md-6"><label class="form-label">Contact Number</label><input type="text" class="form-control" value="09123456789"></div>
            </div>
        </section>

        <section class="card p-4 mb-4">
            <h4 class="mb-4">Account Security</h4>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Current Password</label><input type="password" class="form-control" id="currentPassword"></div>
                <div class="col-md-4"><label class="form-label">New Password</label><input type="password" class="form-control" id="newPassword"></div>
                <div class="col-md-4"><label class="form-label">Confirm Password</label><input type="password" class="form-control" id="confirmPassword"></div>
            </div>
            <div class="mt-3">
                <button class="btn btn-success me-2" id="saveChangesBtn">Save Changes</button>
                <button class="btn btn-outline-secondary" id="changePasswordBtn">Change Password</button>
            </div>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/account.js') ?>
"></script>
</body>
</html>
