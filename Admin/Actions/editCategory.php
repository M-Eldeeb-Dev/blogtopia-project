<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Invalid request method!";
    $_SESSION['message_type'] = "danger";
    header('Location: /Admin/categories.php');
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['message'] = "Invalid security token!";
    $_SESSION['message_type'] = "danger";
    header('Location: /Admin/categories.php');
    exit();
}

// Validate category ID
$edit_id = isset($_POST['edit_cat_id']) ? (int)$_POST['edit_cat_id'] : 0;
if ($edit_id <= 0) {
    $_SESSION['message'] = "Invalid category ID!";
    $_SESSION['message_type'] = "danger";
    header('Location: /Admin/categories.php');
    exit();
}

// Validate input
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($name)) {
    $_SESSION['message'] = "Category name is required!";
    $_SESSION['message_type'] = "danger";
    header('Location: /Admin/categories.php?edit_cat_id=' . $edit_id);
    exit();
}

try {
    // Check if category exists
    $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $check_stmt->execute([$edit_id]);
    
    if (!$check_stmt->fetch()) {
        $_SESSION['message'] = "Category not found!";
        $_SESSION['message_type'] = "danger";
        header('Location: /Admin/categories.php');
        exit();
    }
    
    // Update the category
    $update_stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    $update_stmt->execute([$name, $description, $edit_id]);
    
    // Check if the update was successful
    if ($update_stmt->rowCount() > 0) {
        $_SESSION['message'] = "Category updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "No changes were made to the category.";
        $_SESSION['message_type'] = "info";
    }
    
} catch (PDOException $e) {
    $_SESSION['message'] = "Error updating category: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    error_log("Category update error: " . $e->getMessage());
}

// Redirect back to categories page
header('Location: /Admin/categories.php');
exit();
