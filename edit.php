<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php');
	exit();
}

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$position = trim($_POST['position'] ?? '');
$salary = $_POST['salary'] ?? '';

if ($id <= 0 || $name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $position === '' || $salary === '' || !is_numeric($salary) || (float)$salary < 0) {
	header('Location: index.php?error=Invalid%20employee%20details.');
	exit();
}

$stmt = mysqli_prepare($conn, 'UPDATE employees SET name = ?, email = ?, position = ?, salary = ? WHERE id = ?');
if (!$stmt) {
	header('Location: index.php?error=Unable%20to%20update%20the%20employee.');
	exit();
}
mysqli_stmt_bind_param($stmt, 'sssdi', $name, $email, $position, $salary, $id);
$updated = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

header('Location: ' . ($updated ? 'index.php' : 'index.php?error=Unable%20to%20update%20the%20employee.'));
exit();
