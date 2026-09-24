<?php
require_once __DIR__ . '/db.php';

<<<<<<< HEAD
/* ---------- Read filters from the URL ---------- */
$search   = isset($_GET['q']) ? trim($_GET['q']) : '';
$position = isset($_GET['position']) ? trim($_GET['position']) : '';
$sort     = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$sortOptions = [
    'newest'    => ['label' => 'Newest first', 'sql' => 'created_at DESC, id DESC'],
    'oldest'    => ['label' => 'Oldest first', 'sql' => 'created_at ASC, id ASC'],
    'name_asc'  => ['label' => 'Name A to Z',  'sql' => 'name ASC'],
    'name_desc' => ['label' => 'Name Z to A',  'sql' => 'name DESC'],
];
if (!isset($sortOptions[$sort])) {
    $sort = 'newest';
}

/* ---------- Build the employee query ---------- */
$sql    = 'SELECT id, name, email, position FROM employees';
$where  = [];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR position LIKE ?)';
    $like    = '%' . addcslashes($search, '%_\\') . '%';
    $types  .= 'sss';
    array_push($params, $like, $like, $like);
}
if ($position !== '') {
    $where[] = 'position = ?';
    $types  .= 's';
    $params[] = $position;
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . $sortOptions[$sort]['sql'];

$employees = [];
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }
    mysqli_stmt_close($stmt);
}

/* ---------- Positions for the filter dropdown ---------- */
$positions = [];
$posResult = mysqli_query($conn, 'SELECT DISTINCT position FROM employees ORDER BY position ASC');
if ($posResult) {
    while ($row = mysqli_fetch_assoc($posResult)) {
        $positions[] = $row['position'];
    }
}

/* ---------- Optional status message (?status=added, etc.) ---------- */
$statusMessages = [
    'added'     => ['success', 'Employee added.'],
    'updated'   => ['success', 'Employee updated.'],
    'deleted'   => ['success', 'Employee deleted.'],
    'duplicate' => ['warning', 'That email is already used by another employee.'],
    'invalid'   => ['danger',  'Please fill in all fields correctly.'],
    'error'     => ['danger',  'Something went wrong. Please try again.'],
];
$status = isset($_GET['status']) ? $_GET['status'] : '';

function e($value)
{
=======
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
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
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
<<<<<<< HEAD
    <nav class="sidebar bg-white border-end p-3">
=======
    <nav class="sidebar p-3">
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
        <h5 class="fw-bold mb-4">HR System</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="index.php" class="nav-link active bg-primary text-white rounded">Employees</a>
            </li>
        </ul>
    </nav>

<<<<<<< HEAD
    <!-- Main -->
    <main class="flex-grow-1 p-4">

        <!-- Page head -->
        <div class="d-flex justify-content-between align-items-center mb-4">
=======
    <!-- Main content -->
    <main class="main-content flex-grow-1 p-4">

        <!-- Page header -->
        <div class="page-heading d-flex justify-content-between align-items-center mb-4">
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
            <div>
                <h4 class="page-title fw-bold mb-0">Employees</h4>
                <small class="text-muted">Manage your employees</small>
            </div>
            <button class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addModal">
                + Add Employee
            </button>
        </div>

<<<<<<< HEAD
        <?php if (isset($statusMessages[$status])): ?>
            <div class="alert alert-<?= $statusMessages[$status][0] ?> alert-dismissible fade show" role="alert">
                <?= e($statusMessages[$status][1]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <!-- Search and filter -->
                <form method="get" action="index.php" class="row g-2 mb-3">
                    <div class="col-lg-5">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search name, email, or position"
                               value="<?= e($search) ?>">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select name="position" class="form-select">
                            <option value="">All positions</option>
                            <?php foreach ($positions as $p): ?>
                                <option value="<?= e($p) ?>" <?= $p === $position ? 'selected' : '' ?>>
                                    <?= e($p) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select name="sort" class="form-select">
                            <?php foreach ($sortOptions as $key => $opt): ?>
                                <option value="<?= $key ?>" <?= $key === $sort ? 'selected' : '' ?>>
                                    <?= e($opt['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">Apply</button>
                        <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
=======
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
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
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
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
<<<<<<< HEAD
                            <?php if (!$employees): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No employees found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($employees as $emp): ?>
                                    <tr>
                                        <td><?= sprintf('EMP-%04d', $emp['id']) ?></td>
                                        <td><?= e($emp['name']) ?></td>
                                        <td><?= e($emp['email']) ?></td>
                                        <td><?= e($emp['position']) ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="<?= (int)$emp['id'] ?>"
                                                    data-name="<?= e($emp['name']) ?>"
                                                    data-email="<?= e($emp['email']) ?>"
                                                    data-position="<?= e($emp['position']) ?>">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="<?= (int)$emp['id'] ?>">
                                                Delete
                                            </button>
=======
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
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
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

<<<<<<< HEAD
<!-- Add employee -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="add.php">
=======
<!-- Add employee modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="add.php" method="post">
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
<<<<<<< HEAD
                    <input type="text" name="name" class="form-control" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="150" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control" maxlength="100" required>
=======
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
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save employee</button>
            </div>
        </form>
    </div>
</div>

<<<<<<< HEAD
<!-- Edit employee -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="edit.php">
            <input type="hidden" name="id" id="editId">
=======
<!-- Edit employee modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="edit.php" method="post">
            <input type="hidden" name="id" id="edit-id">
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
<<<<<<< HEAD
                    <input type="text" name="name" id="editName" class="form-control" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-control" maxlength="150" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" id="editPosition" class="form-control" maxlength="100" required>
=======
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
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<<<<<<< HEAD
<!-- Delete employee -->
=======
<!-- Delete employee modal -->
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="delete.php">
            <input type="hidden" name="id" id="deleteId">
            <div class="modal-header">
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this employee? This cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, delete</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
<<<<<<< HEAD
    // Fill the edit modal with the clicked row's data
    document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        document.getElementById('editId').value       = btn.getAttribute('data-id');
        document.getElementById('editName').value     = btn.getAttribute('data-name');
        document.getElementById('editEmail').value    = btn.getAttribute('data-email');
        document.getElementById('editPosition').value = btn.getAttribute('data-position');
    });

    // Pass the clicked row's id to the delete modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
        document.getElementById('deleteId').value = event.relatedTarget.getAttribute('data-id');
    });
=======
document.querySelectorAll('.edit-button').forEach(function (button) {
    button.addEventListener('click', function () {
        document.getElementById('edit-id').value = button.dataset.id;
        document.getElementById('edit-name').value = button.dataset.name;
        document.getElementById('edit-email').value = button.dataset.email;
        document.getElementById('edit-position').value = button.dataset.position;
        document.getElementById('edit-salary').value = button.dataset.salary;
    });
});
>>>>>>> e84b8cb0180b9abcbd16d23bfa6d1804f41e678d
</script>
</body>
</html>
<?php mysqli_close($conn); ?>