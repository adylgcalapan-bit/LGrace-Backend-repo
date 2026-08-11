<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/announcements.css') ?>">
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
            <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
            <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <h2>Announcements</h2>
                <p>Create and manage announcements for residents.</p>
            </div>
            <div class="top-actions">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal"><i class="bi bi-plus-circle"></i> Add Announcement</button>
                <div class="profile">
                    <img src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                    <div><strong>Admin</strong><br><small>Administrator</small></div>
                </div>
            </div>
        </header>

        <section class="card p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="summary-pill">
                        <small class="text-muted">Total</small>
                        <h4 id="totalAnnouncements">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-pill">
                        <small class="text-muted">Published</small>
                        <h4 id="publishedAnnouncements">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-pill">
                        <small class="text-muted">Draft</small>
                        <h4 id="draftAnnouncements">0</h4>
                    </div>
                </div>
            </div>
        </section>

        <section class="card p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6"><label class="form-label">Search Announcements</label><input type="text" class="form-control" id="searchAnnouncement" placeholder="Search announcement..."></div>
                <div class="col-lg-3"><label class="form-label">Filter</label><select class="form-select" id="filterAnnouncement"><option value="all">All</option><option value="published">Published</option><option value="draft">Draft</option><option value="archived">Archived</option></select></div>
                <div class="col-lg-3"><button class="btn btn-outline-success w-100" id="filterBtn" type="button">Filter</button></div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h4 class="mb-0">Announcement List</h4></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-success">
                        <tr><th>Title</th><th>Description</th><th>Created</th><th>Published</th><th>Status</th><th>Author</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="announcementTableBody">
                        <tr>
                            <td>Barangay Cleanup Drive</td>
                            <td>Join the community cleanup this weekend.</td>
                            <td>Jul 25, 2026</td>
                            <td>Jul 26, 2026</td>
                            <td><span class="badge bg-success">Published</span></td>
                            <td>Admin</td>
                            <td><button class="btn btn-sm btn-outline-primary view-btn"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-warning edit-btn"><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-secondary publish-btn"><i class="bi bi-send"></i></button><button class="btn btn-sm btn-outline-danger delete-btn"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="modal fade" id="addAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="addAnnouncementForm">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title" required></div>
                    <div class="mb-3"><label class="form-label">Content</label><textarea class="form-control" rows="4" name="content" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Category/Type</label><input type="text" class="form-control" value="General" name="category"></div>
                    <div class="mb-3"><label class="form-label">Publish Date</label><input type="date" class="form-control" name="publishDate"></div>
                    <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="Published">Published</option><option value="Draft">Draft</option><option value="Archived">Archived</option></select></div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success" type="submit">Save</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editAnnouncementForm">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title" required></div>
                    <div class="mb-3"><label class="form-label">Content</label><textarea class="form-control" rows="4" name="content" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Category/Type</label><input type="text" class="form-control" name="category"></div>
                    <div class="mb-3"><label class="form-label">Publish Date</label><input type="date" class="form-control" name="publishDate"></div>
                    <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="Published">Published</option><option value="Draft">Draft</option><option value="Archived">Archived</option></select></div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success" type="submit">Save Changes</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">View Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="viewAnnouncementContent"></div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white"><h5 class="modal-title">Delete Announcement</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center"><i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px;"></i><h5 class="mt-3">Are you sure?</h5><p>This announcement will be removed.</p></div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" id="confirmDeleteBtn" type="button">Delete</button></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/announcements.js') ?>"></script>
</body>
</html>
