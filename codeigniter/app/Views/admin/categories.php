<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management | CPVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/categories.css') ?>">
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
            <li class="active"><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
           <?= view('admin/notification_menu') ?>
            <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
            <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
            <li class="logout"><a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <h2>Categories Management</h2>
                <p>Manage categories used for community problem reports.</p>
            </div>
            <div class="top-actions">
                <button class="btn btn-success" type="button" onclick="openCategoryModal('addCategoryModal')">
                    <i class="bi bi-plus-circle"></i> Add Category
                </button>
                <div class="profile">
                    <img src="<?= base_url('assets/images/admin picture.jpg') ?>" alt="Admin">
                    <div><strong>Admin</strong><br><small>Administrator</small></div>
                </div>
            </div>
        </header>

        <section class="card page-card p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label">Search Category</label>
                    <input type="text" class="form-control" id="searchCategory" placeholder="Search category...">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-outline-success w-100" id="filterBtn">Filter</button>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Category List</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Reports</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <tr>
                            <td>CAT-001</td>
                            <td>Waste Management Problems</td>
                            <td>Issues related to garbage, waste disposal, and sanitation.</td>
                            <td>32</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-002</td>
                            <td>Infrastructure and Public Works Issues</td>
                            <td>Problems involving roads, drainage, lighting, and public facilities.</td>
                            <td>24</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-003</td>
                            <td>Environmental and Natural Issues</td>
                            <td>Concerns involving floods, pollution, trees, and natural hazards.</td>
                            <td>19</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-004</td>
                            <td>Public Safety and Security Issues</td>
                            <td>Reports involving unsafe areas, accidents, and security concerns.</td>
                            <td>18</td>
                            <td><span class="badge bg-secondary">Inactive</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-005</td>
                            <td>Utilities and Public Services</td>
                            <td>Service concerns such as water, electricity, and public utilities.</td>
                            <td>15</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-006</td>
                            <td>Health and Sanitation Issues</td>
                            <td>Concerns about hygiene, public health, and sanitation standards.</td>
                            <td>13</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-007</td>
                            <td>Social and Community Conflicts</td>
                            <td>Neighborhood disputes and social conflict reports.</td>
                            <td>11</td>
                            <td><span class="badge bg-secondary">Inactive</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-008</td>
                            <td>Transportation and Road Safety Issues</td>
                            <td>Road safety, traffic, and transportation-related concerns.</td>
                            <td>10</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-009</td>
                            <td>Public Facility Issues</td>
                            <td>Problems concerning barangay facilities and community amenities.</td>
                            <td>9</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>CAT-010</td>
                            <td>Animal Control Issues</td>
                            <td>Reports involving stray animals and animal control concerns.</td>
                            <td>7</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-btn" type="button"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-warning edit-btn" type="button" onclick="openCategoryModal('editCategoryModal')"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" type="button" onclick="openCategoryModal('deleteCategoryModal')"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Category Name</label><input type="text" class="form-control" placeholder="Enter category name"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" rows="3" placeholder="Brief description"></textarea></div>
                <div class="mb-3"><label class="form-label">Status</label><select class="form-select"><option>Active</option><option>Inactive</option></select></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="closeCategoryModal('addCategoryModal')">Cancel</button>
                <button class="btn btn-success" type="button" id="saveCategoryBtn" onclick="saveCategory()">Save Category</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Category Name</label><input type="text" class="form-control" value="Waste Management"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" rows="3">Garbage, waste disposal, and sanitation concerns.</textarea></div>
                <div class="mb-3"><label class="form-label">Status</label><select class="form-select"><option>Active</option><option>Inactive</option></select></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="closeCategoryModal('editCategoryModal')">Cancel</button>
                <button class="btn btn-success" type="button" onclick="saveCategory()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 60px;"></i>
                <h5 class="mt-3">Are you sure?</h5>
                <p>This category will be removed from the list.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="closeCategoryModal('deleteCategoryModal')">Cancel</button>
                <button class="btn btn-danger" type="button" onclick="closeCategoryModal('deleteCategoryModal')">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/categories.js') ?>"></script>
</body>
</html>
