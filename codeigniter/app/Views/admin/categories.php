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
                <li><a href="<?= base_url('admin/map') ?>"><i class="bi bi-map"></i>Map View</a></li>
                <li><a href="<?= base_url('admin/residents') ?>"><i class="bi bi-people"></i>Residents</a></li>
                <li class="active"><a href="<?= base_url('admin/categories') ?>"><i class="bi bi-tags"></i>Categories</a></li>
                <?= view('admin/notification_menu') ?>
                <li><a href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
                <li><a href="<?= base_url('admin/account') ?>"><i class="bi bi-person-circle"></i>Account / Profile</a></li>
                <li class="logout"><a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
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

                            <?php if (!empty($categories)): ?>

                                <?php foreach ($categories as $category): ?>

                                    <?php
                                    $categoryId = (int) $category['category_id'];
                                    $isActive = (int) $category['is_active'] === 1;
                                    ?>

                                    <tr
                                        data-category-id="<?= $categoryId ?>"
                                        data-status="<?= $isActive ? 'active' : 'inactive' ?>">

                                        <td>
                                            CAT-<?= str_pad(
                                                    (string) $categoryId,
                                                    3,
                                                    '0',
                                                    STR_PAD_LEFT
                                                ) ?>
                                        </td>

                                        <td>
                                            <?= esc($category['category_name']) ?>
                                        </td>

                                        <td>
                                            <?= esc(
                                                !empty($category['description'])
                                                    ? $category['description']
                                                    : 'No description provided'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= (int) $category['report_count'] ?>
                                        </td>

                                        <td>
                                            <?php if ($isActive): ?>
                                                <span class="badge bg-success">
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    Inactive
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="category-actions">

                                                <!-- EDIT -->
                                                <button
                                                    class="btn btn-sm btn-outline-primary edit-btn category-action-btn"
                                                    type="button"
                                                    title="Edit Category"
                                                    data-category-id="<?= $categoryId ?>"
                                                    data-category-name="<?= esc($category['category_name']) ?>"
                                                    data-description="<?= esc($category['description'] ?? '') ?>"
                                                    data-status="<?= $isActive ? '1' : '0' ?>">

                                                    <i class="bi bi-pencil-square"></i>

                                                </button>

                                                <!-- ACTIVE / INACTIVE -->
                                                <form
                                                    method="post"
                                                    action="<?= site_url('admin/categories/toggle/' . $categoryId) ?>"
                                                    class="d-inline">

                                                    <?= csrf_field() ?>

                                                    <button
                                                        class="btn btn-sm btn-outline-secondary category-action-btn"
                                                        type="submit"
                                                        title="<?= $isActive ? 'Deactivate Category' : 'Activate Category' ?>">

                                                        <i class="bi <?= $isActive ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>

                                                    </button>

                                                </form>

                                                <!-- DELETE -->
                                                <button
                                                    class="btn btn-sm btn-outline-danger delete-btn category-action-btn"
                                                    type="button"
                                                    title="Delete Category"
                                                    data-category-id="<?= $categoryId ?>"
                                                    data-category-name="<?= esc($category['category_name']) ?>">

                                                    <i class="bi bi-trash3"></i>

                                                </button>

                                            </div>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No categories found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form
                    method="post"
                    action="<?= site_url('admin/categories/create') ?>">

                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add Category
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">
                                Category Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="category_name"
                                maxlength="50"
                                placeholder="Enter category name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                name="description"
                                rows="3"
                                placeholder="Brief description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select"
                                name="is_active">

                                <option value="1">
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            class="btn btn-secondary"
                            type="button"
                            onclick="closeCategoryModal('addCategoryModal')">
                            Cancel
                        </button>

                        <button
                            class="btn btn-success"
                            type="submit">
                            Save Category
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form
                    method="post"
                    id="editCategoryForm">

                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Category
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">
                                Category Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="editCategoryName"
                                name="category_name"
                                maxlength="50"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                id="editCategoryDescription"
                                name="description"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select"
                                id="editCategoryStatus"
                                name="is_active">

                                <option value="1">
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            class="btn btn-secondary"
                            type="button"
                            onclick="closeCategoryModal('editCategoryModal')">
                            Cancel
                        </button>

                        <button
                            class="btn btn-success"
                            type="submit">
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post" id="deleteCategoryForm">

                    <?= csrf_field() ?>

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Delete Category</h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body text-center">

                        <i
                            class="bi bi-exclamation-triangle-fill text-danger"
                            style="font-size: 60px;">
                        </i>

                        <h5 class="mt-3">Are you sure?</h5>

                        <p>
                            Do you want to delete
                            <strong id="deleteCategoryName"></strong>?
                        </p>

                        <small class="text-muted">
                            Categories already used by existing reports cannot be deleted.
                            Deactivate them instead.
                        </small>

                    </div>

                    <div class="modal-footer">

                        <button
                            class="btn btn-secondary"
                            type="button"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button
                            class="btn btn-danger"
                            type="submit">
                            Delete
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/categories.js') ?>"></script>
</body>

</html>