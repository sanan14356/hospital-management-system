<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Doctors';
$activePage = 'doctors';

$name = get('name');
$specialization = get('specialization');

$sql = "SELECT * FROM doctors";
$where = [];
$params = [];
$types = '';

if ($name !== '') {
    $where[] = 'name LIKE ?';
    $params[] = "%$name%";
    $types .= 's';
}

if ($specialization !== '') {
    $where[] = 'specialization LIKE ?';
    $params[] = "%$specialization%";
    $types .= 's';
}

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY created_at DESC';

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    bindParams($stmt, $types, $params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Doctor Management</h1>
        <p>Manage hospital specialists and their details.</p>
    </div>
    <a class="btn btn-primary" href="doctor_add.php">
        <i class="fa-solid fa-user-doctor"></i>
        Add Doctor
    </a>
</div>

<form class="filter-grid js-filter-form" action="doctors.php" method="GET" data-endpoint="ajax_search_doctors.php" data-target="doctorsTableBody">
    <div class="input-group">
        <label for="name">Doctor Name</label>
        <input id="name" type="text" name="name" value="<?php echo e($name); ?>" placeholder="Search by name">
    </div>
    <div class="input-group">
        <label for="specialization">Specialization</label>
        <input id="specialization" type="text" name="specialization" value="<?php echo e($specialization); ?>" placeholder="Search by field">
    </div>
    <div class="filter-actions">
        <button class="btn btn-ghost" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
            Search
        </button>
        <a class="btn btn-outline" href="doctors.php">Reset</a>
    </div>
</form>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Experience</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="doctorsTableBody">
                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>
                                <div class="cell-title"><?php echo e($row['name']); ?></div>
                                <div class="cell-subtitle">#D<?php echo e($row['id']); ?></div>
                            </td>
                            <td><?php echo e($row['specialization']); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo e($row['email']); ?></td>
                            <td><?php echo e($row['experience']); ?> yrs</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-ghost" href="doctor_edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a class="btn btn-sm btn-danger" href="delete_doctor.php?id=<?php echo $row['id']; ?>" data-confirm="Delete this doctor?">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="muted">No doctors found. Add the first doctor to begin.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
