<?php
// Test script to debug login issues
session_start();
require_once __DIR__ . '/config/config.php';

echo "<h2>Login Debug Test</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// First, let's check the table structure
echo "<h3>Table Structure:</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "Users table columns:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} else {
    echo "❌ Error describing table: " . mysqli_error($conn) . "<br>";
}

// Check what users exist in the database
echo "<h3>Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "User data: ";
        foreach ($row as $key => $value) {
            echo $key . "=" . $value . " | ";
        }
        echo "<br><br>";
    }
} else {
    echo "❌ Error querying users: " . mysqli_error($conn) . "<br>";
}

// Test password hashing
echo "<h3>Password Hash Test:</h3>";
$test_password = "admin";
echo "Testing password: " . $test_password . "<br>";

// Test with the hash from your database
$stored_hash = '$2y$10$EHXKX8nIHrAMkIwtUbPCR.7p4V8uVOdiQrQBzhMKF3QnR8Fw0KS5W';
echo "Stored hash: " . $stored_hash . "<br>";

if (password_verify($test_password, $stored_hash)) {
    echo "✅ Password verification SUCCESS with stored hash!<br>";
} else {
    echo "❌ Password verification FAILED with stored hash!<br>";
}

// Test creating a new hash
$new_hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "New hash for 'admin': " . $new_hash . "<br>";

if (password_verify($test_password, $new_hash)) {
    echo "✅ Password verification SUCCESS with new hash!<br>";
} else {
    echo "❌ Password verification FAILED with new hash!<br>";
}

// Test the actual login query
echo "<h3>Login Query Test:</h3>";
$loginInput = "admin";
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $loginInput, $loginInput);
$stmt->execute();
$result = $stmt->get_result();

echo "Searching for: " . $loginInput . "<br>";
echo "Found " . $result->num_rows . " users<br>";

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo "Found user: " . $user['username'] . " (" . $user['email'] . ")<br>";
    echo "User password hash: " . $user['password'] . "<br>";
    
    if (password_verify($test_password, $user['password'])) {
        echo "✅ Login would be SUCCESSFUL!<br>";
    } else {
        echo "❌ Login would FAIL - password doesn't match!<br>";
    }
} else {
    echo "❌ No user found!<br>";
}
?>
