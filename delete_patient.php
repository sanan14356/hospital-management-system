<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$patientId = (int)get('id');
if ($patientId <= 0) {
    setFlash('error', 'Invalid patient record.');
    redirect('patients.php');
}

$stmt = mysqli_prepare($conn, "DELETE FROM patients WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $patientId);

if (mysqli_stmt_execute($stmt)) {
    setFlash('success', 'Patient deleted successfully.');
} else {
    setFlash('error', 'Unable to delete patient.');
}

redirect('patients.php');
