<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php');
	exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$position = trim($_POST['position'] ?? '');
$salary = $_POST['salary'] ?? '';

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $position === '' || $salary === '' || !is_numeric($salary) || (float)$salary < 0) {
	header('Location: index.php?error=Please%20complete%20all%20employee%20fields.');
	exit();
}

$stmt = mysqli_prepare($conn, 'INSERT INTO employees (name, email, position, salary) VALUES (?, ?, ?, ?)');
if (!$stmt) {
	header('Location: index.php?error=Unable%20to%20save%20the%20employee.');
	exit();
}
mysqli_stmt_bind_param($stmt, 'sssd', $name, $email, $position, $salary);
$saved = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

header('Location: ' . ($saved ? 'index.php' : 'index.php?error=Unable%20to%20save%20the%20employee.'));
exit();
