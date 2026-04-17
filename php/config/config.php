<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$db_name = 'operation_db'; // Update this with your database name

$conn = mysqli_connect($hostname, $username, $password, $db_name);

if(!$conn){
    echo 'Database not connected!';
    exit();
} else {
    // Set charset to utf8mb4
    mysqli_set_charset($conn, "utf8mb4");
}
?>
