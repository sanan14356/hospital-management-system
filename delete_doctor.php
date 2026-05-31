<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$doctorId = (int)get('id');
if ($doctorId <= 0) {
    setFlash('error', 'Invalid doctor record.');
    redirect('doctors.php');
}

$stmt = mysqli_prepare($conn, "DELETE FROM doctors WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $doctorId);

if (mysqli_stmt_execute($stmt)) {
    setFlash('success', 'Doctor deleted successfully.');
} else {
    setFlash('error', 'Unable to delete doctor.');
}

redirect('doctors.php');
