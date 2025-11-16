<?php
require_once '../Config/auth.php';
requireGuest();
require_once '../Config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Verify CSRF token
        $errors = [];
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $errors['csrf'] = 'Invalid security token. Please try again.';
        } else {
            $username = htmlspecialchars(trim($_POST['username']));
            $email = htmlspecialchars(trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)));
            $password = htmlspecialchars(trim($_POST['password']));
            $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

        // Validate required fields
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $errors['fields'] = 'All fields are required';
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        // Check if passwords match
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        // Only proceed with registration if there are no errors
        if (empty($errors)) {
            try {
                // Check if email already exists
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
                $checkStmt->execute([':email' => $email]);
                
                if ($checkStmt->rowCount() > 0) {
                    $errors['email'] = 'An account with this email already exists';
                } else {
                    // Hash the password and create the user
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $hashedPassword
                    ]);
                    
                    // Get the new user's ID
                    $userId = $pdo->lastInsertId();
                    setcookie('user_id', $userId, time() + (86400 * 10), '/');
                    
                    // Redirect to login page on success
                    header("Location: login.php");
                    exit();
                }
            } catch (PDOException $e) {
                // Log the error and show a user-friendly message
                error_log('Registration error: ' . $e->getMessage());
                $errors['database'] = 'An error occurred during registration. Please try again.';
            }
        }
        }
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PHP Blog</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="../public/blog.svg">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --cyber-blue: #00d4ff;
            --cyber-purple: #a855f7;
            --dark-bg: #0f0f1e;
            --card-bg: rgba(30, 30, 46, 0.8);
            --text-primary: #e5e7eb;
            --text-secondary: #9ca3af;
            --border-glow: rgba(0, 212, 255, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--dark-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(102, 126, 234, 0.2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.2) 0px, transparent 50%);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-container {
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(0, 212, 255, 0.05),
                transparent
            );
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .auth-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--cyber-blue), var(--cyber-purple));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5); }
        }

        .auth-logo i {
            font-size: 2.5rem;
            color: white;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--cyber-blue), var(--cyber-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glow);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--cyber-blue);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.15);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.6;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--cyber-blue);
        }

        .input-group {
            position: relative;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--cyber-blue), var(--cyber-purple));
            border: none;
            border-radius: 12px;
            color: white;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
        }

        .form-footer {
            text-align: center;
            margin-top: 2rem;
            position: relative;
            z-index: 1;
        }

        .form-footer a {
            color: var(--cyber-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--cyber-purple);
            text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border-glow), transparent);
        }

        .divider span {
            padding: 0 1rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .alert {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            border-radius: 12px;
            color: #ef4444;
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
            color: #10b981;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }

            .auth-title {
                font-size: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join us and start your journey today</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        class="form-control text-white" 
                        id="username" 
                        name="username" 
                        placeholder="Choose a unique username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control text-white" 
                        id="email" 
                        name="email" 
                        placeholder="your-email@example.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control text-white" 
                            id="password" 
                            name="password" 
                            placeholder="Create a strong password"
                            required
                        >
                        <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="bi bi-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control text-white" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Re-enter your password"
                            required
                        >
                        <span class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                            <i class="bi bi-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <span>Create Account</span>
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>OR</span>
            </div>

            <div class="form-footer">
                <p class="text-secondary mb-0">
                    Already have an account? 
                    <a href="login.php">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
