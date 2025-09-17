<?php
// Check table structure to debug the issue
require_once __DIR__ . '/config/config.php';

echo "<h2>Table Structure Check</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Check if departments table exists and its structure
echo "<h3>Checking departments table:</h3>";
$result = mysqli_query($conn, "DESCRIBE departments");
if ($result) {
    echo "Departments table columns:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} else {
    echo "❌ Error describing departments table: " . mysqli_error($conn) . "<br>";
    echo "Table might not exist. Let's check what tables exist:<br>";
    
    $tablesResult = mysqli_query($conn, "SHOW TABLES");
    if ($tablesResult) {
        echo "Existing tables:<br>";
        while ($row = mysqli_fetch_array($tablesResult)) {
            echo "- " . $row[0] . "<br>";
        }
    }
}

// Try to insert a test department
echo "<h3>Testing department insertion:</h3>";
$testQuery = "INSERT INTO departments (name, description) VALUES ('Test Department', 'Test Description')";
if (mysqli_query($conn, $testQuery)) {
    echo "✅ Test department inserted successfully!<br>";
    
    // Clean up test data
    mysqli_query($conn, "DELETE FROM departments WHERE name = 'Test Department'");
    echo "✅ Test data cleaned up<br>";
} else {
    echo "❌ Error inserting test department: " . mysqli_error($conn) . "<br>";
}

echo "<br><a href='create_database_schema.php'>← Back to Database Schema</a>";
?>




