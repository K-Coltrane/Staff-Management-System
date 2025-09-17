<?php
// Add role column and set all users as admin
require_once __DIR__ . '/config/config.php';

echo "<h2>Adding Role Column</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Step 1: Add role column
echo "<h3>Step 1: Adding 'role' column to users table</h3>";
$alterQuery = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'admin' AFTER email";
if (mysqli_query($conn, $alterQuery)) {
    echo "✅ Successfully added 'role' column with default value 'admin'!<br>";
} else {
    echo "❌ Error adding role column: " . mysqli_error($conn) . "<br>";
    exit;
}

// Step 2: Update all existing users to have 'admin' role
echo "<h3>Step 2: Setting all existing users to 'admin' role</h3>";
$updateQuery = "UPDATE users SET role = 'admin'";
if (mysqli_query($conn, $updateQuery)) {
    echo "✅ Successfully updated all users to 'admin' role!<br>";
    echo "Rows affected: " . mysqli_affected_rows($conn) . "<br>";
} else {
    echo "❌ Error updating user roles: " . mysqli_error($conn) . "<br>";
}

// Step 3: Verify the changes
echo "<h3>Step 3: Verification</h3>";
$result = mysqli_query($conn, "SELECT id, username, email, role, created_at FROM users");
if ($result) {
    echo "Current users in database:<br><br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . " | Username: " . $row['username'] . " | Email: " . $row['email'] . " | Role: " . $row['role'] . " | Created: " . $row['created_at'] . "<br>";
    }
} else {
    echo "❌ Error querying users: " . mysqli_error($conn) . "<br>";
}

// Step 4: Test the updated table structure
echo "<h3>Step 4: Updated table structure</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "Users table columns:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} else {
    echo "❌ Error describing table: " . mysqli_error($conn) . "<br>";
}

echo "<br><h3>✅ Role column added successfully!</h3>";
echo "<p>All existing users now have 'admin' role. You can now:</p>";
echo "<ul>";
echo "<li>Login with admin privileges</li>";
echo "<li>See all admin menu items in the dashboard</li>";
echo "<li>Add new users with different roles later</li>";
echo "</ul>";

echo "<br><a href='index.php'>← Back to Login</a>";
?>

