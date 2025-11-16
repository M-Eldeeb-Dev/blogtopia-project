<?php
require_once '../Config/auth.php';
requireAdmin();
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../Config/database.php';

// Get all posts with category names
$stmt = $pdo->prepare(
    "SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC"
);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>
<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"> <i class="bi bi-file-text"></i> Posts Management </h1>
            <!-- Add New Post Button -->
            <a href="addpost.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Post
            </a>
        </div> <!-- d-flex -->
    </div> <!-- py-4 -->
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <strong><?= $_SESSION['message'] ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
    <!-- Posts Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Posts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)) { ?>
                            <tr>
                                <td colspan="9" class="text-center text-danger">No Posts Found!</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($posts as $post) { ?>
                                <tr>
                                    <td><?= $post['id'] ?></td>
                                    <td>
                                        <strong><?= $post['title'] ?></strong>
                                    </td>
                                    <td>
                                        <?= !empty($post['category_name']) ? htmlspecialchars($post['category_name']) : 'Uncategorized' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $post['status'] === 'draft' ? 'secondary' : 'success'?>"><?= $post['status']?></span>
                                    </td>
                                    <td><?= $post['views'] ?></td>
                                    <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                                    <td>
                                        <a href="editpost.php?edit_post_id=<?= $post['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="./Actions/deletePost.php?del_post_id=<?= $post['id'] ?>"
                                            class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                        <a href="../User/blogpost.php?id=<?= $post['id'] ?>"
                                            class="btn btn-sm btn-success">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div> <!-- table-responsive -->
        </div> <!-- card-body -->
    </div> <!-- card -->
</main>
<?php include 'includes/footer.php'; ?>