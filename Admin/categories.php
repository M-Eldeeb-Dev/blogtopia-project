<?php
require_once '../Config/auth.php';
requireAdmin();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once '../Config/database.php';
?>



<?php
// Initialize messages
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';

// Clear the messages after displaying
unset($_SESSION['message']);
unset($_SESSION['message_type']);

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $_SESSION['message'] = "Invalid security token!";
        $_SESSION['message_type'] = "danger";
    } else {
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));

        if (empty($name)) {
            $_SESSION['message'] = "Category name is required!";
            $_SESSION['message_type'] = "danger";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
                $stmt->execute(['name' => $name, 'description' => $description]);
                
                $_SESSION['message'] = "Category added successfully!";
                $_SESSION['message_type'] = "success";
                
                // Redirect to prevent form resubmission
                header('Location: categories.php');
                exit();
            } catch (PDOException $e) {
                $_SESSION['message'] = "Error: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
        }
    }
}

// Handle delete category
if (isset($_GET['del_cat_id'])) {
    $cat_id = (int)$_GET['del_cat_id'];
    if ($cat_id > 0) {
        try {
            // Check if category has posts
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ?");
            $stmt->execute([$cat_id]);
            $postCount = $stmt->fetchColumn();
            
            if ($postCount > 0) {
                $_SESSION['message'] = "Cannot delete category with existing posts. Please reassign or delete the posts first.";
                $_SESSION['message_type'] = "danger";
            } else {
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$cat_id]);
                
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "Category deleted successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Category not found or already deleted.";
                    $_SESSION['message_type'] = "warning";
                }
            }
            
            header('Location: categories.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header('Location: categories.php');
            exit();
        }
    }
}

// Get all categories
$categories = [];
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = "Error loading categories: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

// Get category to edit
$edit_category = null;
if (isset($_GET['edit_cat_id'])) {
    $edit_id = (int)$_GET['edit_cat_id'];
    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$edit_id]);
            $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$edit_category) {
                $_SESSION['message'] = "Category not found.";
                $_SESSION['message_type'] = "warning";
                header('Location: categories.php');
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error loading category: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header('Location: categories.php');
            exit();
        }
    }
}
?>

<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"> <i class="bi bi-tags"></i> Categories Management </h1>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <div class="card mb-4 ">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Category</h5>
            </div>
            <div class="card-body">
                <form action="categories.php" method="POST" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                    <div class="col-md-4">
                        <label for="categoryName" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="categoryName" name="name">
                    </div>

                    <div class="col-md-6">
                        <label for="categoryDescription" class="formlabel">Description</label>
                        <input type="text" class="form-control" id="categoryDescription" name="description">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100" name="add_category">
                            <i class="bi bi-plus-circle"></i> Add </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Categories</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover" id="categoriesTable">
                        <thead>
                            <tr>
                                <th colspan="2">ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-danger">No categories found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['id']); ?></td>
                                        <td><?= htmlspecialchars($category['name']); ?></td>
                                        <td><?= !empty($category['description']) ? htmlspecialchars($category['description']) : 'No Description'; ?></td>
                                        <td><?= date('M d, Y', strtotime($category['created_at'])); ?></td>
                                        <td>
                                            <a href="categories.php?edit_cat_id=<?= $category['id']; ?>"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="categories.php?del_cat_id=<?= $category['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if ($edit_category): ?>
                        <div class="card mb-4 mt-4" id="editCategoryForm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-pencil-square"></i> Edit Category
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="./Actions/editCategory.php">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                                    <input type="hidden" name="edit_cat_id" value="<?= $edit_category['id'] ?>">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="editCategoryName" class="form-label">Category Name *</label>
                                            <input type="text" class="form-control" id="editCategoryName" 
                                                name="name" value="<?= htmlspecialchars($edit_category['name']) ?>" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="editCategoryDescription" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="editCategoryDescription" 
                                                name="description" value="<?= htmlspecialchars($edit_category['description']) ?>">
                                        </div>

                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-warning w-100" name="update_category">
                                                <i class="bi bi-pencil-square"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                </div>
                </table>
            </div>
        </div>
    </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>