<?php
// Check user role and add role column if needed
require_once __DIR__ . '/config/config.php';

echo "<h2>User Role Check</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Check if role column exists
echo "<h3>Checking table structure:</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    $hasRole = false;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        if ($row['Field'] == 'role') {
            $hasRole = true;
        }
    }
    
    if (!$hasRole) {
        echo "<br>❌ No 'role' column found!<br>";
        echo "Adding 'role' column...<br>";
        
        $alterQuery = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'staff' AFTER email";
        if (mysqli_query($conn, $alterQuery)) {
            echo "✅ Successfully added 'role' column!<br>";
            
            // Update admin users to have 'admin' role
            $updateQuery = "UPDATE users SET role = 'admin' WHERE username = 'admin'";
            if (mysqli_query($conn, $updateQuery)) {
                echo "✅ Updated admin users to have 'admin' role!<br>";
            } else {
                echo "❌ Error updating admin roles: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "❌ Error adding role column: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "<br>✅ 'role' column exists!<br>";
    }
} else {
    echo "❌ Error describing table: " . mysqli_error($conn) . "<br>";
}

// Show current user data
echo "<h3>Current Users:</h3>";
$result = mysqli_query($conn, "SELECT id, username, email, role FROM users");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . " | Username: " . $row['username'] . " | Email: " . $row['email'] . " | Role: " . ($row['role'] ?? 'NULL') . "<br>";
    }
} else {
    echo "❌ Error querying users: " . mysqli_error($conn) . "<br>";
}

echo "<br><a href='index.php'>← Back to Login</a>";
?>

