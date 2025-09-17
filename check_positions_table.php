<?php
// Check positions table structure
require_once __DIR__ . '/config/config.php';

echo "<h2>Positions Table Structure Check</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Check if positions table exists
$tablesResult = mysqli_query($conn, "SHOW TABLES LIKE 'positions'");
if (mysqli_num_rows($tablesResult) > 0) {
    echo "✅ Positions table exists<br>";
    
    // Check positions table structure
    echo "<h3>Positions table columns:</h3>";
    $result = mysqli_query($conn, "DESCRIBE positions");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "❌ Error describing positions table: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "❌ Positions table does not exist<br>";
}

// Check what tables exist
echo "<h3>All existing tables:</h3>";
$tablesResult = mysqli_query($conn, "SHOW TABLES");
if ($tablesResult) {
    while ($row = mysqli_fetch_array($tablesResult)) {
        echo "- " . $row[0] . "<br>";
    }
}

echo "<br><a href='create_database_schema.php'>← Back to Database Schema</a>";
?>




