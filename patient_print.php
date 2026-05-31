<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$patientId = (int)get('id');
if ($patientId <= 0) {
    setFlash('error', 'Invalid patient record.');
    redirect('patients.php');
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT p.*, d.name AS doctor_name, d.specialization, d.phone AS doctor_phone, d.email AS doctor_email
     FROM patients p
     LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
     WHERE p.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patient = mysqli_fetch_assoc($result);

if (!$patient) {
    setFlash('error', 'Patient not found.');
    redirect('patients.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="print-body" onload="window.print()">
    <div class="print-page">
        <div class="print-header">
            <h1>Patient Medical Record</h1>
            <span>ID: #P<?php echo e($patient['id']); ?></span>
        </div>

        <div class="print-section">
            <h2>Patient Details</h2>
            <div class="print-grid">
                <div><strong>Full Name:</strong> <?php echo e($patient['full_name']); ?></div>
                <div><strong>Age:</strong> <?php echo e($patient['age']); ?></div>
                <div><strong>Gender:</strong> <?php echo e($patient['gender']); ?></div>
                <div><strong>Phone:</strong> <?php echo e($patient['phone']); ?></div>
                <div><strong>Email:</strong> <?php echo e($patient['email']); ?></div>
                <div><strong>Address:</strong> <?php echo e($patient['address']); ?></div>
            </div>
        </div>

        <div class="print-section">
            <h2>Medical Information</h2>
            <div class="print-grid">
                <div><strong>Illness/Disease:</strong> <?php echo e($patient['disease']); ?></div>
                <div><strong>Blood Group:</strong> <?php echo e($patient['blood_group']); ?></div>
                <div><strong>Admission Date:</strong> <?php echo e(formatDate($patient['admission_date'])); ?></div>
            </div>
        </div>

        <div class="print-section">
            <h2>Assigned Doctor</h2>
            <?php if (!empty($patient['doctor_name'])) : ?>
                <div class="print-grid">
                    <div><strong>Name:</strong> <?php echo e($patient['doctor_name']); ?></div>
                    <div><strong>Specialization:</strong> <?php echo e($patient['specialization']); ?></div>
                    <div><strong>Phone:</strong> <?php echo e($patient['doctor_phone']); ?></div>
                    <div><strong>Email:</strong> <?php echo e($patient['doctor_email']); ?></div>
                </div>
            <?php else : ?>
                <p class="muted">No doctor assigned.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
