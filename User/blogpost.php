<?php
session_start();
// Use absolute path to avoid issues when included from different locations
if (!isset($pdo)) {
    require_once __DIR__ . '/../Config/database.php';
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get post with category information
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = :post_id AND p.status = 'published'
");
$stmt->execute(['post_id' => $post_id]);
$post = $stmt->fetch();


if (!$post) {
    header('Location: index.php');
    exit();
}

// Increase views
$stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = :post_id");
$stmt->execute(['post_id' => $post_id]);


// Fetch comments for this post
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>
<?php require_once 'Includes/header.php'; ?>

<div class="container mt-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="/User/index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to All Posts
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Main Post Article -->
            <article class="blog-post-detail">
                <!-- Post Header -->
                <div class="post-header">
                    <!-- Category Badge -->
                    <?php if (!empty($post['category_name'])): ?>
                        <span class="post-category-badge">
                            <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($post['category_name']); ?>
                        </span>
                    <?php endif; ?>

                    <!-- Post Title -->
                    <h1 class="post-detail-title">
                        <?= htmlspecialchars($post['title']); ?>
                    </h1>

                    <!-- Post Meta Information -->
                    <div class="post-detail-meta">
                        <span class="meta-item">
                            <i class="bi bi-calendar-event"></i>
                            <?= date('F j, Y', strtotime($post['created_at'])); ?>
                        </span>
                        <span class="meta-item">
                            <i class="bi bi-eye"></i>
                            <?= number_format($post['views']); ?> views
                        </span>
                        <span class="meta-item">
                            <i class="bi bi-clock"></i>
                            <?php 
                            $readTime = ceil(str_word_count(strip_tags($post['content'])) / 200);
                            echo $readTime . ' min read';
                            ?>
                        </span>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($post['image'])): ?>
                    <div class="post-detail-image">
                        <img src="../uploads/<?= htmlspecialchars($post['image']); ?>" 
                             alt="<?= htmlspecialchars($post['title']); ?>" 
                             class="img-fluid">
                        <div class="image-overlay"></div>
                    </div>
                <?php endif; ?>

                <!-- Post Content -->
                <div class="post-detail-content">
                    <?= nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <!-- Post Footer -->
                <div class="post-detail-footer">
                    <div class="share-section">
                        <h5><i class="bi bi-share"></i> Share this post</h5>
                    </div>
                </div>
            </article>

            <!-- Comments Section -->
            <div class="comments-section">
                <h3 class="comments-title">
                    <i class="bi bi-chat-dots"></i> Comments (<?= count($comments); ?>)
                </h3>

                <!-- Comment Form -->
                <div class="comment-form-wrapper">
                    <h4 class="form-title">Leave a Comment</h4>
                    <form method="POST" action="./Actions/addComment.php" class="comment-form">
                        <div class="row g-3">
                                <input type="hidden" name="postId" value="<?= $_GET['id'] ?>">
                                <input type="hidden" name="userId" value="<?= $_COOKIE['user_id'] ?>">
                            <div class="col-12">
                                <label class="form-label">Your Comment *</label>
                                <textarea name="comment" class="form-control" rows="5" placeholder="Write your comment here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="submit_comment" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send"></i> Post Comment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Comments List -->
                <div class="comments-list">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <div class="comment-avatar">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <div class="comment-content">
                                    <div class="comment-header">
                                        <h5 class="comment-author"><i class="bi bi-person-circle"></i> Comment From User</h5>
                                        <span class="comment-date">
                                            <i class="bi bi-clock"></i>
                                            <?= date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="comment-text">
                                        <?= nl2br(htmlspecialchars($comment['comment'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-comments">
                            <i class="bi bi-chat-left-text"></i>
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Includes/footer.php'; ?>