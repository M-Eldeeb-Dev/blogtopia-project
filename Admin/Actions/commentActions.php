<?php
require_once '../../Config/auth.php';
requireAdmin();
require_once '../../Config/database.php';
// Step 1: Initialize variables
$message = '';
$message_type = '';

// Step 2: Handle comment actions
if (!empty($_GET['action']) && !empty($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

    if (in_array($action, ['approve', 'reject'])) {
        $stmt = $pdo->prepare("UPDATE comments SET status = ? WHERE id = ?");
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt->execute([$status, $id]);

        $message = ucfirst($action) . " Comment Successfully!";

        $_SESSION['message'] = $message;
        header("Location: ../comments.php");
        exit();
    }
}