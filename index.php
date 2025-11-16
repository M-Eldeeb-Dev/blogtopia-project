<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Config/auth.php';

try {
    set_error_handler(function($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    
    if (isAuthenticated()) {
        $userRole = getUserRole();
        
        if ($userRole === 'ADMIN') {
            $redirectUrl = 'Admin/index.php';
        } else {
            $redirectUrl = 'User/index.php';
        }
    } else {
        $redirectUrl = 'User/index.php';
    }
    
    if (!headers_sent()) {
        header("Location: $redirectUrl", true, 302);
        exit();
    } else {
        echo "<script>window.location.href = '$redirectUrl';</script>";
        exit();
    }
    
} catch (Exception $e) {
    error_log("Index.php Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    http_response_code(500);
    
    if (file_exists(__DIR__ . '/error.php')) {
        require_once __DIR__ . '/error.php';
    } else {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Blog System</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
                .error-container {
                    text-align: center;
                    padding: 2rem;
                    background: rgba(0,0,0,0.3);
                    border-radius: 10px;
                    max-width: 500px;
                }
                h1 { margin-top: 0; }
                a {
                    color: white;
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>⚠️ System Error</h1>
                <p>An unexpected error occurred. Please try again later.</p>
                <p><a href="User/index.php">Go to Home Page</a></p>
            </div>
        </body>
        </html>';
    }
    exit();
    
} catch (Error $e) {
    // Handle fatal errors
    error_log("Index.php Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    http_response_code(500);
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fatal Error - Blog System</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .error-container {
                text-align: center;
                padding: 2rem;
                background: rgba(0,0,0,0.3);
                border-radius: 10px;
                max-width: 500px;
            }
            h1 { margin-top: 0; }
            a {
                color: white;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>⚠️ Fatal Error</h1>
            <p>A critical error occurred. Please contact the administrator.</p>
            <p><a href="User/index.php">Go to Home Page</a></p>
        </div>
    </body>
    </html>';
    exit();
} finally {
    restore_error_handler();
}
