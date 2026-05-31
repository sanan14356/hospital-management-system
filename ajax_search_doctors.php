<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

header('Content-Type: application/json');

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

$rows = '';
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows .= '<tr>';
        $rows .= '<td><div class="cell-title">' . e($row['name']) . '</div><div class="cell-subtitle">#D' . e($row['id']) . '</div></td>';
        $rows .= '<td>' . e($row['specialization']) . '</td>';
        $rows .= '<td>' . e($row['phone']) . '</td>';
        $rows .= '<td>' . e($row['email']) . '</td>';
        $rows .= '<td>' . e($row['experience']) . ' yrs</td>';
        $rows .= '<td class="text-right">';
        $rows .= '<a class="btn btn-sm btn-ghost" href="doctor_edit.php?id=' . e($row['id']) . '">Edit</a>';
        $rows .= '<a class="btn btn-sm btn-danger" href="delete_doctor.php?id=' . e($row['id']) . '" data-confirm="Delete this doctor?">Delete</a>';
        $rows .= '</td></tr>';
    }
} else {
    $rows = '<tr><td colspan="6" class="muted">No doctors found.</td></tr>';
}

echo json_encode(['html' => $rows]);
