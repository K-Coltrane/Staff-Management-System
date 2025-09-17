<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        global $conn;
        $this->userModel = new User($conn);
    }
    
    /**
     * Handle user login
     */
    public function login() {
        global $conn;

        // Check if connection exists
        if (!$conn) {
            echo "Database connection error!";
            return false;
        }

        // Sanitize input
        $loginInput = sanitize($_POST['username']);
        $password = $_POST['password']; // Don't sanitize password before verification

        // Debug: Show what we're searching for
        echo "DEBUG: Searching for login input: " . $loginInput . "<br>";
        echo "DEBUG: Password provided: " . $password . "<br>";

        // Use prepared statement for security - check both username and email
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        if (!$stmt) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            return false;
        }
        
        $stmt->bind_param("ss", $loginInput, $loginInput);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "DEBUG: Found " . $result->num_rows . " users<br>";

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Debug: Show user data
            echo "DEBUG: User found - ID: " . $user['user_id'] . ", Username: " . $user['username'] . ", Email: " . $user['email'] . "<br>";
            echo "DEBUG: Stored password hash: " . $user['password'] . "<br>";

            // Verify the password
            if (password_verify($password, $user['password'])) {
                echo "DEBUG: Password verification SUCCESS!<br>";
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'admin'; // Default to admin if no role column

                return true;
            } else {
                echo "DEBUG: Password verification FAILED!<br>";
            }
        } else {
            echo "DEBUG: No user found with that username/email<br>";
        }

        return false;
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        // Destroy the session
        session_start();
        session_unset();
        session_destroy();

        // Redirect to the login page
        header('Location: ../../index.php');
        exit;
    }

    //Handle Password Reset
    public function requestPasswordReset() {
        global $conn;
        
        // Check if connection exists
        if (!$conn) {
            setMessage("Database connection error!", "danger");
            redirect('forgot-password.php');
            return;
        }
    
        // Sanitize the email input
        $email = sanitize($_POST['email']);
    
        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
    
            // Generate a unique password reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetTokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour
    
            // Save the token and expiry in the database
            $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $updateStmt->bind_param("sss", $resetToken, $resetTokenExpiry, $email);
            
            if ($updateStmt->execute()) {
                // Send the reset email
                $resetLink = BASE_URL . "views/auth/reset-password.php?token=$resetToken";
                $subject = "Password Reset Request";
                $message = "Hello, \n\nClick the link below to reset your password:\n$resetLink\n\nIf you did not request this, please ignore this email.";
                $headers = "From: " . ADMIN_EMAIL;
    
                if (mail($email, $subject, $message, $headers)) {
                    setMessage("A password reset link has been sent to your email.", "success");
                } else {
                    setMessage("Failed to send the password reset email. Please try again later.", "danger");
                }
            } else {
                setMessage("Failed to generate a password reset token. Please try again later.", "danger");
            }
        } else {
            setMessage("No account found with that email address.", "danger");
        }
    
        // Redirect back to the forgot password page
        redirect('forgot-password.php');
    }
}
?>
