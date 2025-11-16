<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
        <nav class="col-md-3 col-lg-2 d-md-block sidebar border-end min-vh-100">
            <div class="position-sticky pt-3">


                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Main Menu</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door"></i>
                            Dashboard
                        </a>
                    </li>
                </ul>


                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Content</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'posts.php') ? 'active' : '' ?>" href="posts.php">
                            <i class="bi bi-file-text"></i>
                            Posts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'categories.php') ? 'active' : '' ?>" href="categories.php">
                            <i class="bi bi-tags"></i>
                            Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'comments.php') ? 'active' : '' ?>" href="comments.php">
                            <i class="bi bi-chat-dots"></i>
                            Comments
                        </a>
                    </li>
                </ul>


                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Management</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'users.php') ? 'active' : '' ?>" href="users.php">
                            <i class="bi bi-people"></i>
                            Users
                        </a>
                    </li>
                </ul>


                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Other</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="../User/index.php" target="_blank">
                            <i class="bi bi-globe"></i>
                            View Site
                        </a>
                    </li>
                </ul>
            </div>
        </nav>


