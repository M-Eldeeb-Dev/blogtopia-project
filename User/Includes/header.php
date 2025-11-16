<?php
// Use absolute path to avoid issues when included from different locations
if (!function_exists('isAuthenticated')) {
    require_once __DIR__ . '/../../Config/auth.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogTopia - PHP Blog</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="../public/blog.svg">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom Futuristic CSS -->
    <link rel="stylesheet" href="assets/css/user.css">
</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/User/index.php">
                <i class="bi bi-speedometer2"></i> BlogTopia
            </a>
            <div class="d-flex align-items-center gap-2 text-white">
                <a href="/User/index.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-box-arrow-right"></i> All Blogs
                </a>
                <?php if (isAuthenticated()): ?>
                <p class="pt-3 px-1"><?= 'Welcome, ' . htmlspecialchars(getUsername()); ?></p>
                <?php if (isAdmin()): ?>
                <a href="/Admin/index.php" class="btn btn-warning btn-sm">
                    <i class="bi bi-speedometer2"></i> Admin Panel
                </a>
                <?php endif; ?>
                <a href="/Auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                <?php else: ?>
                <a href="/Auth/login.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="/Auth/register.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus"></i> Register
                </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
