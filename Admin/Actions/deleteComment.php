<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['del_comment_id'])) {
        $del_comment_id = $_GET['del_comment_id'];
        $stmt = $pdo->prepare("DELETE FROM `comments` WHERE id = :del_comment_id");
        $stmt->execute([":del_comment_id" => $del_comment_id]);

        // Success message 
        $_SESSION["message"] = "Comment deleted successfully!";
        $_SESSION["message_type"] = "success";

        header('Location: /Admin/comments.php');
        exit();
    } else {
        header('Location: /Admin/comments.php');
        exit();
    }
} else {
    header('Location: /Admin/index.php');
    exit();
}
