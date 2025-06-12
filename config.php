<?php
// Database configuration
$db_host = "sql313.infinityfree.com"; // InfinityFree's database host
$db_user = "if0_39213265"; // You'll get this from InfinityFree control panel
$db_pass = "webprogram24252"; // You'll get this from InfinityFree control panel
$db_name = "if0_39213265_personal_DB"; // You'll get this from InfinityFree control panel

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass,$db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
$result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
if ($result->num_rows == 0) {
    // Create database if it doesn't exist
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS $db_name")) {
        die("Error creating database: " . $conn->error);
    }
}

// Select the database
if (!$conn->select_db($db_name)) {
    die("Error selecting database: " . $conn->error);
}
?>
