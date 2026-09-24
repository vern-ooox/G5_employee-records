<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$name     = isset($_POST['name']) ? trim($_POST['name']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$position = isset($_POST['position']) ? trim($_POST['position']) : '';

if ($name === '' || $email === '' || $position === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?status=invalid');
    exit();
}

$status = 'error';

try {
    $stmt = mysqli_prepare($conn, 'INSERT INTO employees (name, email, position) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $position);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $status = 'added';
} catch (mysqli_sql_exception $ex) {
    // 1062 = duplicate entry (email is UNIQUE)
    $status = ($ex->getCode() === 1062) ? 'duplicate' : 'error';
}

mysqli_close($conn);
header('Location: index.php?status=' . $status);
exit();