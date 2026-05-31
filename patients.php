<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Patients';
$activePage = 'patients';

$name = get('name');
$disease = get('disease');
$bloodGroup = get('blood_group');

$sql = "SELECT p.*, d.name AS doctor_name, d.specialization
        FROM patients p
        LEFT JOIN doctors d ON p.assigned_doctor_id = d.id";
$where = [];
$params = [];
$types = '';

if ($name !== '') {
    $where[] = 'p.full_name LIKE ?';
    $params[] = "%$name%";
    $types .= 's';
}

if ($disease !== '') {
    $where[] = 'p.disease LIKE ?';
    $params[] = "%$disease%";
    $types .= 's';
}

if ($bloodGroup !== '') {
    $where[] = 'p.blood_group = ?';
    $params[] = $bloodGroup;
    $types .= 's';
}

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY p.created_at DESC';

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    bindParams($stmt, $types, $params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Patient Management</h1>
        <p>Track medical records, assignments, and patient status.</p>
    </div>
    <a class="btn btn-primary" href="patient_add.php">
        <i class="fa-solid fa-user-plus"></i>
        Add Patient
    </a>
</div>

<form class="filter-grid js-filter-form" action="patients.php" method="GET" data-endpoint="ajax_search_patients.php" data-target="patientsTableBody">
    <div class="input-group">
        <label for="name">Patient Name</label>
        <input id="name" type="text" name="name" value="<?php echo e($name); ?>" placeholder="Search by name">
    </div>
    <div class="input-group">
        <label for="disease">Disease</label>
        <input id="disease" type="text" name="disease" value="<?php echo e($disease); ?>" placeholder="Search by disease">
    </div>
    <div class="input-group">
        <label for="blood_group">Blood Group</label>
        <select id="blood_group" name="blood_group">
            <option value="">All groups</option>
            <?php foreach ($bloodGroups as $group) : ?>
                <option value="<?php echo e($group); ?>" <?php echo selectedAttr($group, $bloodGroup); ?>><?php echo e($group); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-actions">
        <button class="btn btn-ghost" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
            Search
        </button>
        <a class="btn btn-outline" href="patients.php">Reset</a>
    </div>
</form>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Disease</th>
                    <th>Blood</th>
                    <th>Phone</th>
                    <th>Assigned Doctor</th>
                    <th>Admitted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="patientsTableBody">
                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>
                                <div class="cell-title"><?php echo e($row['full_name']); ?></div>
                                <div class="cell-subtitle">#P<?php echo e($row['id']); ?></div>
                            </td>
                            <td><?php echo e($row['disease']); ?></td>
                            <td><?php echo e($row['blood_group']); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo e($row['doctor_name'] ?? 'Unassigned'); ?></td>
                            <td><?php echo e(formatDate($row['admission_date'])); ?></td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-outline" href="patient_view.php?id=<?php echo $row['id']; ?>">View</a>
                                <a class="btn btn-sm btn-ghost" href="patient_edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a class="btn btn-sm btn-danger" href="delete_patient.php?id=<?php echo $row['id']; ?>" data-confirm="Delete this patient record?">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="muted">No patients found. Add your first patient to get started.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
