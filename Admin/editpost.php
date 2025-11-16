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
    $postId = filter_var($_POST['postId'], FILTER_VALIDATE_INT);
    $status = htmlspecialchars($_POST['status']);

    $check = $title && $content && $category_id > 0  && $status && $postId;

    if ($check) {
        // Save to database 
        $sql = "UPDATE `posts` SET `title`= :title,`content`= :content, `category_id`= :category_id , `status`= :status WHERE id = :postId";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':title'       => $title,
            ':content'     => $content,
            ':category_id' => $category_id,
            ':status'      => $status,
            ':postId'      => $postId
        ]);

        header('Location: /Admin/posts.php');
        exit();
    }
}
?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['edit_post_id'])) {
        if ($_GET['edit_post_id'] <= 0) {
            header('location: /Admin/posts.php');
        }

        $edit_id = $_GET['edit_post_id'];
        $check_stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :edit_post_id");
        $check_stmt->execute(["edit_post_id" => $edit_id]);
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);

        $check = $result;

        if ($check) {
            $title = $result['title'];
            $content = $result['content'];
            $category_id = $result['category_id'];
            $status = $result['status'];
        }
    }
}
?>

<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-file-text"></i> Edit Post </h1>
            <a href="posts.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Posts </a>
        </div> <!-- d-flex -->
        <!-- Add New Post Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle"></i> Edit Blog Post
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="editpost.php">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Post Title</label>
                        <input type="hidden" value="<?= filter_var($_GET['edit_post_id'], FILTER_SANITIZE_NUMBER_INT) ?>" name="postId">
                        <input type="text" class="form-control" value="<?= $title; ?>" name="title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="6" required><?= $content; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select bg-dark text-white" value="<?= $category_id; ?>" name="category_id" required>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM categories");
                            $stmt->execute();
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo '<option class="bg-dark text-white" value="' . $category['id'] . '">' . $category['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select bg-dark text-white" name="status">
                            <option <?= ($status == 'draft') ? 'selected' : ''; ?> class="bg-dark text-white" value="draft">Draft</option>
                            <option <?= ($status == 'published') ? 'selected' : ''; ?> class="bg-dark text-white" value="published">Published</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Edit Post
                    </button>
                </form>
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- py-4 -->
</main>
<?php include 'includes/footer.php'; ?>