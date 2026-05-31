<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

header('Content-Type: application/json');

$patientId = (int)get('patient_id');
if ($patientId <= 0) {
    echo json_encode(['error' => 'Invalid patient']);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT p.full_name, p.phone, p.email, p.disease, p.blood_group, p.admission_date, d.name AS doctor_name, d.specialization
     FROM patients p
     LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
     WHERE p.id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patient = mysqli_fetch_assoc($result);

if (!$patient) {
    echo json_encode(['error' => 'Patient not found']);
    exit;
}

echo json_encode([
    'full_name' => $patient['full_name'],
    'phone' => $patient['phone'],
    'email' => $patient['email'],
    'disease' => $patient['disease'],
    'blood_group' => $patient['blood_group'],
    'admission_date' => formatDate($patient['admission_date']),
    'doctor_name' => $patient['doctor_name'] ?: 'Unassigned',
    'doctor_specialization' => $patient['specialization'] ?: 'N/A'
]);
