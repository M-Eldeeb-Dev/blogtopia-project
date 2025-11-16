<?php
require_once '../Config/auth.php';
requireAdmin();
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../Config/database.php';

$sql = "SELECT * FROM users";
$stmt = $pdo->prepare($sql);
if ($stmt->execute()) {
    $users = $stmt->fetchAll();
} else {
    $users = [
        'id' => 1,
        'username' => 'Unknown',
        'email' => 'Unknown',
        'password' => 'Unknown',
        'role' => 'Unknown',
        'created_at' => 'Unknown'
    ];
}
?>
<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"> <i class="bi bi-people"></i> Users Management </h1>
        </div> <!-- d-flex -->
    </div> <!-- py-4 -->

    <?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <strong><?= $_SESSION['message'] ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <!-- Stats Cards -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-light">
                            <p class="mb-1 text-uppercase" style="font-size: 0.85rem;">Total Users</p>
                            <h3 class="mb-0 fw-bold"><?= count($users) ?></h3>
                        </div>
                        <div class="text-primary" style="font-size: 3rem; opacity: 0.7;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)) { ?>
                            <tr>
                                <td colspan="7" class="text-center text-danger">No Users Found!</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($users as $user) { ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <strong>
                                            <i class="bi bi-person-circle"></i>
                                            <?= htmlspecialchars($user['username']) ?>
                                        </strong>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] === 'ADMIN' ? 'danger' : 'info' ?>">
                                            <?= $user['role'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if (!($user['role'] === 'ADMIN')): ?>
                                            <a href="./Actions/deleteUser.php?del_user_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" title="Delete User">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        <?php else: ?>
                                            <a href="/Admin/index.php" class="btn btn-sm btn-primary" title="Main Dashboard">
                                                <i class="bi bi-speedometer2 text-black"></i> Dashboard
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div> <!-- table-responsive -->
        </div> <!-- card-body -->
    </div> <!-- card -->
</main>

<?php include 'includes/footer.php'; ?>