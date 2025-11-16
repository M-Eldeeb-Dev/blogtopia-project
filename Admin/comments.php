<?php
require_once '../Config/auth.php';
requireAdmin();
require_once '../Config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$comments_stmt = $pdo->prepare("
    SELECT 
        c.*, 
        u.username AS author_username  
    FROM 
        `comments` c
    JOIN 
        `users` u ON c.user_id = u.id
    ORDER BY 
        c.created_at DESC
");

$comments_stmt->execute();
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
$count_comments = $comments_stmt->rowCount();

$pending_commentstmt = $pdo->query(" SELECT * FROM `comments` WHERE `status` = 'Pending'");
$pending_comments = $pending_commentstmt->fetchAll();
$count_pending_comments = $pending_commentstmt->rowCount();

$approved_commentstmt = $pdo->query(" SELECT * FROM `comments` WHERE `status` = 'Approved'");
$approved_comments = $approved_commentstmt->fetchAll();
$count_approved_comments = $approved_commentstmt->rowCount();

$rejected_commentstmt = $pdo->query(" SELECT * FROM `comments` WHERE `status` = 'Rejected'");
$rejected_comments = $rejected_commentstmt->fetchAll();
$count_rejected_comments = $rejected_commentstmt->rowCount();


?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="bi bi-chat-dots"></i> Comments Management
            </h1>
        </div>
        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?= $message ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }
        ?>
        <!-- Comments Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Comments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead>
                            <tr>
                                <th colspan="2">ID</th>
                                <th>Name</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($count_comments == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-danger">No Comments Found!</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($comments as $comment): ?>
                                <tr>
                                    <td><?= $comment['id'] ?></td>
                                    <td><?= $comment['author_username'] ?></td>
                                    <td><?= $comment['comment'] ?></td>
                                    <td>
                                        <span
                                            class="badge <?= $comment['status'] == 'approved' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $comment['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($comment['created_at'])) ?></td>
                                    <td>
                                        <a
                                            href="Actions/commentActions.php?action=approve&id=<?= $comment['id'] ?>"
                                            class="btn btn-sm btn-success <?= $comment['status'] == 'approved' ? 'disabled' : '' ?>">
                                            <i class="bi bi-arrow-repeat"></i>
                                            Approved
                                        </a>
                                        <a
                                            href="Actions/commentActions.php?action=reject&id=<?= $comment['id'] ?>"
                                            class="btn btn-sm btn-danger <?= $comment['status'] == 'rejected' ? 'disabled' : '' ?>">
                                            <i class="bi bi-x"></i>
                                            Rejected
                                        </a>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="deleteComment(<?= $comment['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                            Delete
                                        </button>
                                    </td>
                                    <script>
                                        function deleteComment(id) {
                                            if (confirm("Are you sure you want to delete this comment?")) {
                                                window.location.href = `/Admin/Actions/deleteComment.php?del_comment_id=${id}`;
                                            }
                                        }
                                    </script>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $count_comments ?></h4>
                                <p class="mb-0">Total Comments</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-chat-dots fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $count_pending_comments ?></h4>
                                <p class="mb-0">Pending</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $count_approved_comments ?></h4>
                                <p class="mb-0">Approved</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $count_rejected_comments ?></h4>
                                <p class="mb-0">Rejected</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-x-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>