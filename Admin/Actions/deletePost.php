<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['del_post_id'])) {
        $del_post_id = $_GET['del_post_id'];
        $stmt = $pdo->prepare("DELETE FROM `posts` WHERE id = :del_post_id");
        $stmt->execute([":del_post_id" => $del_post_id]);

        // Success message 
        $_SESSION["message"] = "Post deleted successfully!";
        $_SESSION["message_type"] = "success";

        header('Location: /Admin/posts.php');
        exit();
    } else {
        header('Location: /Admin/posts.php');
        exit();
    }
} else {
    header('Location: /Admin/index.php');
    exit();
}
