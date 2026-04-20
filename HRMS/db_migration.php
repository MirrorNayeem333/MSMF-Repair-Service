<?php
include 'connection.php';

$sql = "CREATE TABLE IF NOT EXISTS cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $sql)) {
    echo "Cart table created successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}
?>
