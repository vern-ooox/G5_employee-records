<?php
require_once __DIR__ . '/db.php';

$employees = [];
$search = trim($_GET['search'] ?? '');
$query = 'SELECT id, name, email, position, salary FROM employees';
if ($search !== '') {
    $query .= ' WHERE name LIKE ? OR email LIKE ? OR position LIKE ?';
    $searchValue = '%' . $search . '%';
    $stmt = mysqli_prepare($conn, $query . ' ORDER BY id DESC');
    mysqli_stmt_bind_param($stmt, 'sss', $searchValue, $searchValue, $searchValue);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $query . ' ORDER BY id DESC');
    $stmt = null;
}
if ($result) {
    while ($employee = mysqli_fetch_assoc($result)) {
        $employees[] = $employee;
    }
    mysqli_free_result($result);
}
if ($stmt) {
    mysqli_stmt_close($stmt);
}

$error = $_GET['error'] ?? '';

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --blue: #116cf5; --ink: #18202a; --muted: #66717e; --line: #e2e6ea; --canvas: #f7f8fa; }
        * { box-sizing: border-box; }
        body { background: var(--canvas) !important; color: var(--ink); font-family: Arial, sans-serif; }
        .sidebar { width: 206px; min-height: 100vh; flex: 0 0 206px; background: #fff; border-right: 1px solid var(--line); }
        .sidebar h5 { font-size: 20px; letter-spacing: -.4px; }
        .sidebar .nav-link { color: var(--blue); border-radius: 6px; padding: 10px 15px; }
        .sidebar .nav-link.active { background: var(--blue) !important; color: #fff !important; }
        .main-content { min-width: 0; }
        .page-title { font-size: 24px; letter-spacing: -.5px; }
        .add-button { background: var(--blue); border-color: var(--blue); border-radius: 6px; padding: 9px 14px; }
        .add-button:hover { background: #0758d3; border-color: #0758d3; }
        .employee-card { border: 1px solid #edf0f2 !important; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,.08) !important; }
        .search-row { max-width: 570px; }
        .search-row .form-control { border-radius: 6px 0 0 6px; padding: 9px 11px; }
        .search-button { background: var(--blue); border-color: var(--blue); border-radius: 0 6px 6px 0; }
        .table { margin-bottom: 0; }
        .table thead th { background: #f8f9fa; border-bottom: 1px solid #cfd4d9; color: #151b22; font-size: 15px; }
        .table td, .table th { padding: 10px 8px; }
        .table tbody td { color: var(--muted); border-bottom-color: var(--line); }
        .table tbody tr:last-child td { border-bottom: 0; }
        @media (max-width: 650px) { .sidebar { width: 150px; flex-basis: 150px; } main { padding: 20px 14px !important; } .page-heading { align-items: flex-start !important; flex-direction: column; gap: 14px; } .add-button { width: 100%; } }
    </style>
</head>
<body class="bg-light">

<div class="d-flex">

    <!-- Navigation -->
    <nav class="sidebar p-3">
        <h5 class="fw-bold mb-4">HR System</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="index.php" class="nav-link active bg-primary text-white rounded">Employees</a>
            </li>
        </ul>
    </nav>

    <!-- Main content -->
    <main class="main-content flex-grow-1 p-4">

        <!-- Page header -->
        <div class="page-heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="page-title fw-bold mb-0">Employees</h4>
                <small class="text-muted">Manage your employees</small>
            </div>
            <button class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addModal">
                + Add Employee
            </button>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo escape($error); ?></div>
        <?php endif; ?>

        <div class="card employee-card border-0 shadow-sm">
            <div class="card-body">

                <!-- Search -->
                <form class="search-row input-group mb-3" method="get">
                    <input type="text" name="search" value="<?php echo escape($search); ?>" class="form-control" placeholder="Search name, email, or position">
                    <button type="submit" class="btn btn-primary search-button">Search</button>
                    <?php if ($search !== ''): ?><a href="index.php" class="btn btn-outline-secondary ms-2">Reset</a><?php endif; ?>
                </form>

                <!-- Employee table -->
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Salary</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($employees) === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No employees found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($employees as $employee): ?>
                                    <tr>
                                        <td><?php echo escape($employee['id']); ?></td>
                                        <td><?php echo escape($employee['name']); ?></td>
                                        <td><?php echo escape($employee['email']); ?></td>
                                        <td><?php echo escape($employee['position']); ?></td>
                                        <td><?php echo number_format((float)$employee['salary'], 2); ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-button"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="<?php echo escape($employee['id']); ?>"
                                                data-name="<?php echo escape($employee['name']); ?>"
                                                data-email="<?php echo escape($employee['email']); ?>"
                                                data-position="<?php echo escape($employee['position']); ?>"
                                                data-salary="<?php echo escape($employee['salary']); ?>">Edit</button>
                                            <a href="delete.php?id=<?php echo escape($employee['id']); ?>" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this employee?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Add employee modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="add.php" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salary</label>
                    <input type="number" name="salary" class="form-control" min="0" step="0.01" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save employee</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit employee modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="edit.php" method="post">
            <input type="hidden" name="id" id="edit-id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" id="edit-position" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salary</label>
                    <input type="number" name="salary" id="edit-salary" class="form-control" min="0" step="0.01" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete employee modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this employee? This cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Yes, delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.edit-button').forEach(function (button) {
    button.addEventListener('click', function () {
        document.getElementById('edit-id').value = button.dataset.id;
        document.getElementById('edit-name').value = button.dataset.name;
        document.getElementById('edit-email').value = button.dataset.email;
        document.getElementById('edit-position').value = button.dataset.position;
        document.getElementById('edit-salary').value = button.dataset.salary;
    });
});
</script>
</body>
</html>