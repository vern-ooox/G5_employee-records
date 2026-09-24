<?php
$conn = mysqli_connect("localhost", "root", "", "employee-records");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

<<<<<<< HEAD
mysqli_set_charset($conn, "utf8mb4");
=======
mysqli_set_charset($conn, 'utf8mb4');

$createEmployeesTable = "CREATE TABLE IF NOT EXISTS employees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    position VARCHAR(100) NOT NULL,
    salary DECIMAL(12, 2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_query($conn, $createEmployeesTable)) {
    die("Unable to initialize employees table: " . mysqli_error($conn));
}
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
