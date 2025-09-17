<?php
require_once '../../config/config.php';
require_once '../../controllers/AuthController.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../../index.php');
}

$authController = new AuthController();
$tokenValid = false;
$tokenExpired = false;
$resetSuccess = false;

// Check if token is valid
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = sanitize($_GET['token']);
    
    // Check token in database
    global $conn;
    $query = "SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $expiry = strtotime($user['reset_token_expiry']);
        $now = time();
        
        if ($expiry > $now) {
            $tokenValid = true;
            $userId = $user['user_id'];
        } else {
            $tokenExpired = true;
            setMessage('This password reset link has expired. Please request a new one.', 'danger');
        }
    } else {
        setMessage('Invalid password reset link. Please request a new one.', 'danger');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($password !== $confirmPassword) {
        setMessage('Passwords do not match', 'danger');
    } else if (strlen($password) < 8) {
        setMessage('Password must be at least 8 characters long', 'danger');
    } else {
        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $updateQuery = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $hashedPassword, $userId);
        
        if ($updateStmt->execute()) {
            $resetSuccess = true;
            setMessage('Your password has been reset successfully. You can now login with your new password.', 'success');
        } else {
            setMessage('Failed to update password. Please try again.', 'danger');
        }
    }
}

// Page title
$pageTitle = 'Reset Password';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo $pageTitle; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3"><?php echo SITE_NAME; ?></h1>
                            <p class="text-muted">Reset Your Password</p>
                        </div>
                        
                        <?php displayMessage(); ?>
                        
                        <?php if ($resetSuccess): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="btn btn-primary">Go to Login</a>
                            </div>
                        <?php elseif ($tokenExpired || !isset($_GET['token']) || empty($_GET['token']) || !$tokenValid): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>views/auth/forgot-password.php" class="btn btn-primary">Request New Link</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="text-decoration-none">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>