<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

header('Content-Type: application/json');

$name = get('name');
$disease = get('disease');
$bloodGroup = get('blood_group');

$sql = "SELECT p.id, p.full_name, p.disease, p.blood_group, p.phone, p.admission_date, d.name AS doctor_name
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

$rows = '';
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows .= '<tr>';
        $rows .= '<td><div class="cell-title">' . e($row['full_name']) . '</div><div class="cell-subtitle">#P' . e($row['id']) . '</div></td>';
        $rows .= '<td>' . e($row['disease']) . '</td>';
        $rows .= '<td>' . e($row['blood_group']) . '</td>';
        $rows .= '<td>' . e($row['phone']) . '</td>';
        $rows .= '<td>' . e($row['doctor_name'] ?? 'Unassigned') . '</td>';
        $rows .= '<td>' . e(formatDate($row['admission_date'])) . '</td>';
        $rows .= '<td class="text-right">';
        $rows .= '<a class="btn btn-sm btn-outline" href="patient_view.php?id=' . e($row['id']) . '">View</a>';
        $rows .= '<a class="btn btn-sm btn-ghost" href="patient_edit.php?id=' . e($row['id']) . '">Edit</a>';
        $rows .= '<a class="btn btn-sm btn-danger" href="delete_patient.php?id=' . e($row['id']) . '" data-confirm="Delete this patient record?">Delete</a>';
        $rows .= '</td></tr>';
    }
} else {
    $rows = '<tr><td colspan="7" class="muted">No patients found.</td></tr>';
}

echo json_encode(['html' => $rows]);
