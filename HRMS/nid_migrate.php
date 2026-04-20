<?php
/**
 * One-time migration: adds nid_photo column to customers and providers tables.
 * Run once via browser: http://localhost/HRMS/nid_migrate.php
 * Delete this file after running.
 */
include 'connection.php';

$results = [];

// Add nid_photo to customers
$r1 = mysqli_query($con, "ALTER TABLE customers ADD COLUMN IF NOT EXISTS nid_photo VARCHAR(255) DEFAULT NULL");
$results[] = $r1 ? "✅ customers.nid_photo column added (or already exists)." : "❌ customers: " . mysqli_error($con);

// Add nid_photo to providers
$r2 = mysqli_query($con, "ALTER TABLE providers ADD COLUMN IF NOT EXISTS nid_photo VARCHAR(255) DEFAULT NULL");
$results[] = $r2 ? "✅ providers.nid_photo column added (or already exists)." : "❌ providers: " . mysqli_error($con);

echo "<pre>" . implode("\n", $results) . "\n\nDone. Please delete this file.</pre>";
?>
