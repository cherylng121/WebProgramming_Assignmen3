<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS personal_DB";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("personal_DB");

// Create contact_submissions table
$sql = "CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phoneNum VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table contact_submissions created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
echo "Database initialization completed!";
?> 