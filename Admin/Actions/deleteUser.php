<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['del_user_id'])) {
        $del_user_id = $_GET['del_user_id'];
        $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = :del_user_id");
        $stmt->execute([":del_user_id" => $del_user_id]);

        // Success message 
        $_SESSION["message"] = "User deleted successfully!";
        $_SESSION["message_type"] = "success";

        header('Location: /Admin/users.php');
        exit();
    } else {
        header('Location: /Admin/users.php');
        exit();
    }
} else {
    header('Location: /Admin/index.php');
    exit();
}
