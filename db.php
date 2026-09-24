<?php
$conn = mysqli_connect("localhost", "root", "", "employee-records");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected!";