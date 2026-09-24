<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$id       = isset($_POST['id']) ? $_POST['id'] : '';
$name     = isset($_POST['name']) ? trim($_POST['name']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$position = isset($_POST['position']) ? trim($_POST['position']) : '';

if (!is_numeric($id) || (int)$id <= 0
    || $name === '' || $email === '' || $position === ''
    || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?status=invalid');
    exit();
}

$id = (int)$id;
$status = 'error';

try {
    $stmt = mysqli_prepare($conn, 'UPDATE employees SET name = ?, email = ?, position = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $position, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $status = 'updated';
} catch (mysqli_sql_exception $ex) {
    $status = ($ex->getCode() === 1062) ? 'duplicate' : 'error';
}

mysqli_close($conn);
header('Location: index.php?status=' . $status);
exit();