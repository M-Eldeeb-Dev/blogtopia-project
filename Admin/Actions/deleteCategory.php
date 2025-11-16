<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['del_cat_id'])) {
        $del_cat_id = $_GET['del_cat_id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id= :del_cat_id");
        $stmt->execute([":del_cat_id" => $del_cat_id]);

        // Success message 
        $_SESSION["message"] = "Category deleted successfully!";

        header('Location: /Admin/categories.php');
        exit();
    } else {
        header('Location: /Admin/categories.php');
        exit();
    }
} else {
    header('Location: /Admin/index.php');
    exit();
}
