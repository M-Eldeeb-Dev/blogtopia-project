<?php
// Use absolute path to avoid issues when included from different locations
if (!isset($pdo)) {
    require_once __DIR__ . '/../Config/database.php';
}

$stmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status='published' 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();
?>
<?php require_once 'Includes/header.php'; ?>

<div class="container mt-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="page-title">
            <i class="bi bi-rocket-takeoff"></i> Latest Blog Posts
        </h1>
        <p class="page-subtitle">
            Discover amazing stories, insights, and ideas from our community
        </p>
    </div>

    <!-- Blog Posts Grid -->
    <div class="row">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="blog-post-card">
                        <!-- Post Image -->
                        <?php if (!empty($post['image'])): ?>
                            <div class="post-image-wrapper">
                                <img src="../Uploads/<?= htmlspecialchars($post['image']); ?>" 
                                     alt="<?= htmlspecialchars($post['title']); ?>" 
                                     class="post-image">
                                <div class="post-image-overlay"></div>
                            </div>
                        <?php else: ?>
                            <div class="post-image-wrapper">
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(168, 85, 247, 0.2));">
                                    <i class="bi bi-image" style="font-size: 4rem; color: var(--cyber-blue);"></i>
                                </div>
                                <div class="post-image-overlay"></div>
                            </div>
                        <?php endif; ?>

                        <!-- Post Content -->
                        <div class="post-content">
                            <!-- Category Badge -->
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="post-category">
                                    <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($post['category_name']); ?>
                                </span>
                            <?php endif; ?>

                            <!-- Post Title -->
                            <h2 class="post-title">
                                <?= htmlspecialchars($post['title']); ?>
                            </h2>

                            <!-- Post Meta -->
                            <div class="post-meta">
                                <span>
                                    <i class="bi bi-calendar-event"></i>
                                    <?= date('F j, Y', strtotime($post['created_at'])); ?>
                                </span>
                                <span>
                                    <i class="bi bi-clock"></i>
                                    <?php 
                                    $readTime = ceil(str_word_count(strip_tags($post['content'])) / 200);
                                    echo $readTime . ' min read';
                                    ?>
                                </span>
                            </div>

                            <!-- Post Excerpt -->
                            <p class="post-excerpt">
                                <?php
                                $content = strip_tags($post['content']);
                                echo substr($content, 0, 150) . '...';
                                ?>
                            </p>

                            <!-- Read More Button -->
                            <a href="/User/blogpost.php?id=<?= $post['id']; ?>" class="read-more-btn">
                                Read Full Article
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- No Posts Message -->
            <div class="col-12 py-5 my-5">
                <div class="no-posts-message">
                    <i class="bi bi-inbox"></i>
                    <h3>No Posts Yet</h3>
                    <p>Check back soon for amazing content!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'Includes/footer.php'; ?>