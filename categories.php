<?php
// session_start();
require_once "config/database.php";

// Untuk demo/testing tanpa session, gunakan user_id = 1 sebagai default
// Jika ada session, gunakan session, jika tidak gunakan default
// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;

// Pastikan user dengan id = 1 ada (untuk demo/testing)
$check_user = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$check_user->execute([$user_id]);
if (!$check_user->fetch()) {
    // Buat user dummy jika belum ada
    $create_user = $pdo->prepare("INSERT INTO users (id, name, phone_number, password, created_at, updated_at) VALUES (1, 'Demo User', '081234567890', ?, NOW(), NOW())");
    $hashed_password = password_hash('demo123', PASSWORD_DEFAULT);
    $create_user->execute([$hashed_password]);
}

// Ambil semua kategori milik user
$query = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY created_at DESC");
$query->execute([$user_id]);
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// Hitung jumlah tugas per kategori
$category_task_counts = [];
foreach ($categories as $category) {
    $count_query = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE category_id = ?");
    $count_query->execute([$category['id']]);
    $result = $count_query->fetch(PDO::FETCH_ASSOC);
    $category_task_counts[$category['id']] = $result['count'];
}

include "layout/header.php";
include "layout/sidebar.php";
?>

<!-- CSS khusus untuk halaman Categories -->
<link rel="stylesheet" href="assets/css/categories.css">

<div class="container-fluid categories-page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Category Management</h2>
            <p class="text-muted mb-0">Organize your tasks with categories</p>
        </div>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddModal()">
            <i class="bi bi-plus-lg me-2"></i>Add Category
        </button>
    </div>

    <!-- Alert untuk feedback -->
    <?php // if (isset($_SESSION['success_message'])): ?>
        <?php /* <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div> */ ?>
    <?php // endif; ?>

    <?php // if (isset($_SESSION['error_message'])): ?>
        <?php /* <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div> */ ?>
    <?php // endif; ?>

    <!-- Tabel Kategori -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-folder-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">No categories yet. Add your first category!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Task Count</th>
                                <th>Created Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #e3f2fd; color: #1976d2;">
                                            <?php echo $category_task_counts[$category['id']] ?? 0; ?> tasks
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $date = new DateTime($category['created_at']);
                                        echo $date->format('Y-m-d');
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-link text-primary p-1 me-2" 
                                                onclick="openEditModal(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')"
                                                title="Edit">
                                            <i class="bi bi-pencil fs-5"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-1" 
                                                onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')"
                                                title="Delete">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kategori -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" action="process/category_crud.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="category_id" id="categoryId">
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required 
                               placeholder="Enter category name" maxlength="255">
                        <div class="form-text">Category name will be used to organize your tasks.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi untuk membuka modal tambah
function openAddModal() {
    document.getElementById('categoryModalLabel').textContent = 'Add Category';
    document.getElementById('formAction').value = 'create';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryForm').reset();
}

// Fungsi untuk membuka modal edit
function openEditModal(id, name) {
    document.getElementById('categoryModalLabel').textContent = 'Edit Category';
    document.getElementById('formAction').value = 'update';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
}

// Fungsi konfirmasi hapus dengan alert bahasa Indonesia
function confirmDelete(id, name) {
    if (confirm('Apakah Anda yakin ingin menghapus kategori "' + name + '"?\n\nTugas yang menggunakan kategori ini akan menjadi "Uncategorized" dan tidak akan ikut terhapus.')) {
        // Buat form untuk submit delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'process/category_crud.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'category_id';
        idInput.value = id;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php 
include "layout/footer.php";
?>

