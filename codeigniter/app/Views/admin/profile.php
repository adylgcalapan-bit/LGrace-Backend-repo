<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/profile.css') ?>">
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
            <li><a href="<?= base_url('admin/notifications') ?>"><i class="bi bi-bell"></i>Notifications</a></li>
            <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
            <li class="active"><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
            <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <h2>Admin Profile</h2>
                <p>Update your personal and contact information.</p>
            </div>
            <div class="top-actions">
                <button class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
                <button class="btn btn-success" id="saveProfileBtn">Save Changes</button>
            </div>
        </header>

        <section class="card p-4 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-3 text-center">
                    <img id="profilePreview" src="https://i.pravatar.cc/180?img=12" class="profile-img" alt="Profile">
                    <input type="file" id="photoInput" accept="image/*" class="form-control mt-3">
                    <div class="mt-2">
                        <button class="btn btn-outline-success btn-sm" id="uploadBtn">Upload Photo</button>
                        <button class="btn btn-outline-danger btn-sm" id="removeBtn">Remove</button>
                    </div>
                </div>
                <div class="col-md-9">
                    <h4>John Dela Cruz</h4>
                    <p class="mb-1"><strong>Role:</strong> Administrator</p>
                    <p class="mb-1"><strong>Email:</strong> admin@cpvs.com</p>
                    <p class="mb-1"><strong>Contact:</strong> 09123456789</p>
                    <p class="mb-1"><strong>Address:</strong> Purok 1, Barangay Saguing</p>
                    <p class="mb-1"><strong>Status:</strong> Active</p>
                    <p class="mb-0"><strong>Date Joined:</strong> Jan 15, 2025</p>
                </div>
            </div>
        </section>

        <section class="card p-4">
            <h4 class="mb-4">Profile Information</h4>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">First Name</label><input type="text" class="form-control" value="John"></div>
                <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" value="Ramos"></div>
                <div class="col-md-4"><label class="form-label">Last Name</label><input type="text" class="form-control" value="Dela Cruz"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="admin@cpvs.com"></div>
                <div class="col-md-6"><label class="form-label">Contact</label><input type="text" class="form-control" value="09123456789"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" rows="3">Purok 1, Barangay Saguing</textarea></div>
            </div>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/profile.js') ?>"></script>
</body>
</html>
