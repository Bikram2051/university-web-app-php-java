<?php
// Database connection settings
$host = 'feenix-mariadb.swin.edu.au';
$user = 's105974087';
$password = '070694';
$database = 's105974087_db';

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Connection failed. Please try again later.");
}

// Security headers.
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
// Set charset to utf8
mysqli_set_charset($conn, "utf8");

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if (mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist, create it
    $sql = "CREATE TABLE orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(25) NOT NULL,
        lastname VARCHAR(25) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(10) NOT NULL,
        street VARCHAR(40) NOT NULL,
        suburb VARCHAR(20) NOT NULL,
        state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
        postcode VARCHAR(4) NOT NULL,
        contact_method ENUM('email','phone','post') NOT NULL,
        product ENUM('aurora','nexus','essence') NOT NULL,
        quantity INT NOT NULL,
        features TEXT,
        comments TEXT,
        order_cost DECIMAL(10,2) NOT NULL,
        order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        order_status ENUM('PENDING','FULFILLED','PAID','ARCHIVED') DEFAULT 'PENDING',
        card_type ENUM('visa','mastercard','amex') NOT NULL,
        card_name VARCHAR(40) NOT NULL,
        card_number VARCHAR(16) NOT NULL,
        card_expiry VARCHAR(5) NOT NULL,
        card_cvv VARCHAR(3) NOT NULL
    )";
    
    if (mysqli_query($conn, $sql)) {
        error_log("Orders table created successfully");
    } else {
        error_log("Error creating table: " . mysqli_error($conn));
    }
}
?>