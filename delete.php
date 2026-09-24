<?php

ob_start();
require_once __DIR__ . '/db.php';
ob_end_clean();

$id = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id = $_POST['id'];
}

if ($id === null || $id === '') {
    header('Location: index.php');
    exit();
}

if (!is_numeric($id) || (int)$id <= 0) {
    header('Location: index.php');
    exit();
}

$id = (int)$id;

$stmt = mysqli_prepare($conn, 'DELETE FROM employees WHERE id = ?');

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

header('Location: index.php');
exit();

