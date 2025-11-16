<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated (logged in)
 * @return bool
 */
function isAuthenticated() {
    return (isset($_SESSION['username']) && isset($_SESSION['role'])) || 
           (isset($_COOKIE['username']) && isset($_COOKIE['role']));
}

/**
 * Get current user's role
 * @return string|null
 */
function getUserRole() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    if (isset($_COOKIE['role'])) {
        return $_COOKIE['role'];
    }
    return null;
}

/**
 * Get current username
 * @return string|null
 */
function getUsername() {
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    if (isset($_COOKIE['username'])) {
        return $_COOKIE['username'];
    }
    return null;
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    $role = getUserRole();
    return $role === 'ADMIN';
}

/**
 * Require authentication - redirect to login if not authenticated
 * @param string $redirectUrl URL to redirect to if not authenticated
 */
function requireAuth($redirectUrl = '/Auth/login.php') {
    if (!isAuthenticated()) {
        header("Location: $redirectUrl");
        exit();
    }
}

/**
 * Require admin role - redirect if not admin
 * @param string $redirectUrl URL to redirect to if not admin
 */
function requireAdmin($redirectUrl = '/User/index.php') {
    if (!isAuthenticated()) {
        header("Location: /Auth/login.php");
        exit();
    }
    
    if (!isAdmin()) {
        header("Location: $redirectUrl");
        exit();
    }
}

/**
 * Require guest (not authenticated) - redirect if already logged in
 * @param string $redirectUrl URL to redirect to if already authenticated
 */
function requireGuest($redirectUrl = '/index.php') {
    if (isAuthenticated()) {
        header("Location: $redirectUrl");
        exit();
    }
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Logout user - clear session and cookies
 */
function logoutUser() {
    // Clear cookies
    setcookie('username', '', time() - 86400, '/');
    setcookie('role', '', time() - 86400, '/');
    
    // Clear session
    session_unset();
    session_destroy();
}

/**
 * Login user - set session and cookies
 * @param string $username
 * @param string $role
 * @param bool $rememberMe
 */
function loginUser($username, $role, $rememberMe = true) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Set session variables
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    
    // Set cookies if remember me is enabled
    if ($rememberMe) {
        $cookieExpiry = time() + (86400 * 30); // 30 days
        setcookie('username', $username, $cookieExpiry, '/');
        setcookie('role', $role, $cookieExpiry, '/');
    }
}

/**
 * Sanitize input data
 * @param mixed $data
 * @return mixed
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email
 * @return string|false
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
