<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>HRMS Project Diagnostic Tool</h1>";

// 1. Check Connection File
if (!file_exists("connection.php")) {
    die("<p style='color:red;'>FATAL: connection.php not found!</p>");
}
echo "<p style='color:green;'>✓ connection.php exists.</p>";

// 2. Try Connecting
include "connection.php";
if ($con) {
    $dbInfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT DATABASE() AS db_name"));
    $activeDb = $dbInfo['db_name'] ?? '(none)';
    echo "<p style='color:green;'>✓ Database connection successful (Host: localhost, Active DB: {$activeDb}).</p>";
} else {
    die("<p style='color:red;'>FATAL: connection variable (\$con) is not defined!</p>");
}

// 3. Check Tables
$tables = ['customers', 'providers', 'admins', 'product', 'services', 'cart'];
foreach ($tables as $t) {
    $res = mysqli_query($con, "SHOW TABLES LIKE '$t'");
    if ($res && mysqli_num_rows($res) > 0) {
        echo "<p style='color:green;'>✓ Table '$t' exists.</p>";
        
        // Check columns for customers
        if ($t === 'customers') {
            $cols = mysqli_query($con, "DESCRIBE customers");
            $hasMobile = false;
            while($c = mysqli_fetch_assoc($cols)) {
                if ($c['Field'] === 'mobile_number') $hasMobile = true;
            }
            if ($hasMobile) {
                echo "--- <span style='color:green;'>✓ 'mobile_number' column found.</span><br>";
            } else {
                echo "--- <span style='color:orange;'>⚠ 'mobile_number' column MISSING (check if it is 'phone').</span><br>";
            }
        }
    } else {
        echo "<p style='color:red;'>✗ Table '$t' is MISSING!</p>";
    }
}

// 4. Check for at least one Admin
$adminCheck = mysqli_query($con, "SELECT COUNT(*) as cnt FROM admins");
$adminRow = mysqli_fetch_assoc($adminCheck);
echo "<p>Admin Count: " . $adminRow['cnt'] . "</p>";

echo "<h2>Next Steps:</h2>";
echo "<p>If everything above is green, your database is fine. The issue is likely in the <b>login redirect</b> or <b>header sessions</b>.</p>";
?>
