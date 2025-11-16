<?php
require_once '../Config/auth.php';
requireAdmin();
?>
<?php require_once '../Config/database.php'; ?>
<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>


<?php
$poststmt = $pdo->query(" SELECT * FROM `posts` ");
$posts = $poststmt->fetchAll();
$count_posts = $poststmt->rowCount();

$viewsstmt = $pdo->query("SELECT SUM(views) AS total_views FROM posts;");
$views = $viewsstmt->fetchColumn();

$catstmt = $pdo->query(" SELECT * FROM `categories` ");
$categories = $catstmt->fetchAll();
$count_categories = $catstmt->rowCount();

$commentstmt = $pdo->query(" SELECT * FROM `comments` ");
$comments = $commentstmt->fetchAll();
$count_comments  = $commentstmt->rowCount();

$post_published_date_stmt = $pdo->query("SELECT `created_at` FROM `posts` ORDER BY `created_at` DESC LIMIT 1;");
$result_date = $post_published_date_stmt->fetchColumn();
$post_published_time = strtotime($result_date);
$post_published_date = date('d M / Y | h:i:s A |', $post_published_time) === '01 Jan / 1970 | 12:00:00 AM |' ? 'None' : date('d M / Y | h:i:s A |', $post_published_time);

$comment_published_date_stmt = $pdo->query("SELECT `created_at` FROM `comments` ORDER BY `created_at` DESC LIMIT 1;");
$result_date = $comment_published_date_stmt->fetchColumn();
$comment_published_time = strtotime($result_date);
$comment_published_date = date('d M / Y | h:i:s A |', $comment_published_time) === '01 Jan / 1970 | 12:00:00 AM |' ? 'None' : date('d M / Y | h:i:s A |', $comment_published_time);


$post_updated_date_stmt = $pdo->query("SELECT `updated_at` FROM `posts` ORDER BY `updated_at` DESC LIMIT 1;");
$result_date = $post_updated_date_stmt->fetchColumn();
$post_updated_time = strtotime($result_date);
$post_updated_date = date('d M / Y | h:i:s A |', $post_updated_time) === '01 Jan / 1970 | 12:00:00 AM |' ? 'None' : date('d M / Y | h:i:s A |', $post_updated_time);


?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="bi bi-speedometer2"></i> Dashboard Overview
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <button type="button" class="btn btn-sm btn-primary">
                    <i class="bi bi-calendar"></i> Today
                </button>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-light">
                                <p class="mb-1 text-uppercase" style="font-size: 0.85rem;">Total Posts</p>
                                <h3 class="mb-0 fw-bold"><?= $count_posts?></h3>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.7;">
                                <i class="bi bi-file-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-light">
                                <p class="mb-1 text-uppercase" style="font-size: 0.85rem;">Categories</p>
                                <h3 class="mb-0 fw-bold"><?= $count_categories?></h3>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.7;">
                                <i class="bi bi-tags"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-light">
                                <p class="mb-1 text-uppercase" style="font-size: 0.85rem;">Comments</p>
                                <h3 class="mb-0 fw-bold"><?= $count_comments ?></h3>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.7;">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-light">
                                <p class="mb-1 text-uppercase" style="font-size: 0.85rem;">Total Views</p>
                                <h3 class="mb-0 fw-bold"><?= $views ?? 0?></h3>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.7;">
                                <i class="bi bi-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history"></i> Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item bg-transparent border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success rounded-circle p-2">
                                            <i class="bi bi-plus"></i>
                                        </span>
                                    </div>
                                    <div class="text-light flex-grow-1 ms-3">
                                        <h6 class="mb-0 ">Last post published</h6>
                                        <small class="text-info"><?= $post_published_date?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item bg-transparent border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-info rounded-circle p-2">
                                            <i class="bi bi-chat"></i>
                                        </span>
                                    </div>
                                    <div class="text-light flex-grow-1 ms-3">
                                        <h6 class="mb-0">Last comment received</h6>
                                        <small class="text-info"><?= $comment_published_date?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item bg-transparent border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-warning rounded-circle p-2">
                                            <i class="bi bi-pencil"></i>
                                        </span>
                                    </div>
                                    <div class="text-light flex-grow-1 ms-3">
                                        <h6 class="mb-0">Last post updated</h6>
                                        <small class="text-info"><?= $post_updated_date?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="addpost.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> New Post
                            </a>
                            <a href="categories.php" class="btn btn-success">
                                <i class="bi bi-tags"></i> Manage Categories
                            </a>
                            <a href="comments.php" class="btn btn-warning">
                                <i class="bi bi-chat-dots"></i> View Comments
                            </a>
                            <a href="../index.php" class="btn btn-info" target="_blank">
                                <i class="bi bi-globe"></i> View Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>