<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Patient Record';
$activePage = 'patients';

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
     WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $patientId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patient = mysqli_fetch_assoc($result);

if (!$patient) {
    setFlash('error', 'Patient not found.');
    redirect('patients.php');
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Patient Record</h1>
        <p>Complete medical profile and doctor assignment.</p>
    </div>
    <div class="header-actions">
        <a class="btn btn-ghost" href="patient_edit.php?id=<?php echo $patientId; ?>">Edit</a>
        <a class="btn btn-outline" href="patient_print.php?id=<?php echo $patientId; ?>" target="_blank">Print Record</a>
    </div>
</div>

<div class="details-grid">
    <div class="detail-card">
        <h2>Patient Details</h2>
        <div class="detail-row">
            <span>Full Name</span>
            <strong><?php echo e($patient['full_name']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Age</span>
            <strong><?php echo e($patient['age']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Gender</span>
            <strong><?php echo e($patient['gender']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Phone</span>
            <strong><?php echo e($patient['phone']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Email</span>
            <strong><?php echo e($patient['email']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Address</span>
            <strong><?php echo e($patient['address']); ?></strong>
        </div>
    </div>

    <div class="detail-card">
        <h2>Medical Information</h2>
        <div class="detail-row">
            <span>Illness / Disease</span>
            <strong><?php echo e($patient['disease']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Blood Group</span>
            <strong><?php echo e($patient['blood_group']); ?></strong>
        </div>
        <div class="detail-row">
            <span>Admission Date</span>
            <strong><?php echo e(formatDate($patient['admission_date'])); ?></strong>
        </div>
    </div>

    <div class="detail-card">
        <h2>Assigned Doctor</h2>
        <?php if (!empty($patient['doctor_name'])) : ?>
            <div class="detail-row">
                <span>Name</span>
                <strong><?php echo e($patient['doctor_name']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Specialization</span>
                <strong><?php echo e($patient['specialization']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Phone</span>
                <strong><?php echo e($patient['doctor_phone']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Email</span>
                <strong><?php echo e($patient['doctor_email']); ?></strong>
            </div>
        <?php else : ?>
            <p class="muted">No doctor assigned yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
