<?php
// Script to update password hash in database
require_once __DIR__ . '/config/config.php';

echo "<h2>Update Password Hash</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Generate new password hash for 'admin'
$password = "admin";
$new_hash = password_hash($password, PASSWORD_DEFAULT);

echo "New password hash for 'admin': " . $new_hash . "<br>";

// Update all admin users with the new hash
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $new_hash);

if ($stmt->execute()) {
    echo "✅ Successfully updated password hash for all admin users!<br>";
    echo "Rows affected: " . $stmt->affected_rows . "<br>";
} else {
    echo "❌ Error updating password: " . $stmt->error . "<br>";
}

// Verify the update
echo "<h3>Verification:</h3>";
$result = mysqli_query($conn, "SELECT id, username, password FROM users WHERE username = 'admin'");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "User ID: " . $row['id'] . " | Username: " . $row['username'] . "<br>";
        echo "New Hash: " . $row['password'] . "<br>";
        
        // Test password verification
        if (password_verify($password, $row['password'])) {
            echo "✅ Password verification SUCCESS!<br>";
        } else {
            echo "❌ Password verification FAILED!<br>";
        }
        echo "<br>";
    }
} else {
    echo "❌ Error verifying update: " . mysqli_error($conn) . "<br>";
}

echo "<br><a href='index.php'>← Back to Login</a>";
?>

