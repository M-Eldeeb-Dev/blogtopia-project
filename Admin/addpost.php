<?php
require_once '../Config/auth.php';
requireAdmin();
require_once '../Config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
    $status = htmlspecialchars($_POST['status']);

    // Handle image 
    $image = '';
    if ($_FILES['image']['name']) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fileName);
        $image = $fileName;
    }

    $check = $title && $content && $category_id > 0  && $status && $image;

    if ($check) {
        // Save to database 
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, category_id, status) 
                    VALUES (:title, :content, :image, :category_id, :status)");

        $stmt->execute([
            ':title'       => $title,
            ':content'     => $content,
            ':image'       => $image,
            ':category_id' => $category_id,
            ':status'      => $status
        ]);

        header('Location: /Admin/posts.php');
        exit();
    }
}
?>


<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-file-text"></i> Add New Post </h1>
            <a href="posts.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Posts </a>
        </div> <!-- d-flex -->
        <!-- Add New Post Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle"></i> Create New Blog Post
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="addpost.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Post Title</label>
                        <input type="text" class="form-control" name="title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM categories");
                            $stmt->execute();
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo '<option class="bg-dark text-white" value="' . $category['id'] . '">' . $category['name'] . "</option>";
                            }
                            ?>
                        </select>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option class="bg-dark text-white" value="draft">Draft</option>
                            <option class="bg-dark text-white" value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Create Post
                    </button>
                </form>
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- py-4 -->
</main>
<?php include 'includes/footer.php'; ?>