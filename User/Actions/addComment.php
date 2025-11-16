<?php
// Use absolute paths to avoid issues when included from different locations
if (!function_exists('requireAuth')) {
    require_once __DIR__ . '/../../Config/auth.php';
}
requireAuth(); // Require user to be logged in to comment
if (!isset($pdo)) {
    require_once __DIR__ . '/../../Config/database.php';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = filter_var($_POST['postId'], FILTER_VALIDATE_INT);
    $comment = htmlspecialchars(trim($_POST['comment']));

    $check = $post_id && $comment;

    if ($check) {
        // Get user_id from session
        $user_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $user_stmt->execute([':username' => getUsername()]);
        $user = $user_stmt->fetch();
        $user_id = $user ? $user['id'] : null;
        
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (:post_id, :user_id, :comment)");
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id,
            ':comment' => $comment
        ]);
        header("Location: /User/blogpost.php?id=$post_id");
        exit();
    }
}

?>  