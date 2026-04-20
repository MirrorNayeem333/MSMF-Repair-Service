<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Flexible MySQL connection for local XAMPP and custom setups.
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS');
$dbPass = $dbPass === false ? '' : $dbPass;
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

$requestedDb = getenv('DB_NAME');
$candidateDatabases = array_values(array_unique(array_filter([
    $requestedDb ?: null,
    'DBMS',
    'hrms',
    'HRMS'
])));

$con = mysqli_connect($dbHost, $dbUser, $dbPass, '', $dbPort);
if (!$con) {
    die("Database server connection failed ({$dbHost}:{$dbPort}): " . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');

$selectedDb = null;
foreach ($candidateDatabases as $dbName) {
    if (mysqli_select_db($con, $dbName)) {
        $selectedDb = $dbName;
        break;
    }
}

if ($selectedDb === null) {
    $dbList = implode(', ', $candidateDatabases);
    die(
        "Connected to MySQL but could not select a project database. " .
        "Tried: {$dbList}. " .
        "Set DB_NAME in your environment or create one of these databases in phpMyAdmin."
    );
}
