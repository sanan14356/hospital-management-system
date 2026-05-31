<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$patientsCount = 0;
$doctorsCount = 0;
$ticketsCount = 0;
$assignedCount = 0;

$result = mysqli_query($conn, "SELECT COUNT(*) FROM patients");
if ($result) {
    $patientsCount = (int)mysqli_fetch_row($result)[0];
}

$result = mysqli_query($conn, "SELECT COUNT(*) FROM doctors");
if ($result) {
    $doctorsCount = (int)mysqli_fetch_row($result)[0];
}

$result = mysqli_query($conn, "SELECT COUNT(*) FROM tickets");
if ($result) {
    $ticketsCount = (int)mysqli_fetch_row($result)[0];
}

$result = mysqli_query($conn, "SELECT COUNT(*) FROM patients WHERE assigned_doctor_id IS NOT NULL");
if ($result) {
    $assignedCount = (int)mysqli_fetch_row($result)[0];
}

$recentPatients = mysqli_query(
    $conn,
    "SELECT p.id, p.full_name, p.disease, p.admission_date, d.name AS doctor_name
     FROM patients p
     LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
     ORDER BY p.created_at DESC
     LIMIT 5"
);

$recentTickets = mysqli_query(
    $conn,
    "SELECT t.id, t.ticket_date, t.price, p.full_name, d.name AS doctor_name
     FROM tickets t
     INNER JOIN patients p ON t.patient_id = p.id
     INNER JOIN doctors d ON t.doctor_id = d.id
     ORDER BY t.created_at DESC
     LIMIT 5"
);

include __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <div>
        <h1>Welcome back, <?php echo e(currentAdminName()); ?>.</h1>
        <p>Here is a quick view of your hospital activity today.</p>
    </div>
    <a class="btn btn-primary" href="patient_add.php">
        <i class="fa-solid fa-user-plus"></i>
        Add Patient
    </a>
</div>

<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-bed-pulse"></i>
        </div>
        <div>
            <p>Total Patients</p>
            <h3><?php echo $patientsCount; ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        <div>
            <p>Total Doctors</p>
            <h3><?php echo $doctorsCount; ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div>
            <p>Total Tickets</p>
            <h3><?php echo $ticketsCount; ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <p>Assigned Doctors</p>
            <h3><?php echo $assignedCount; ?></h3>
        </div>
    </div>
</div>

<div class="split-grid">
    <div class="panel">
        <div class="panel-header">
            <h2>Recent Patients</h2>
            <a class="btn btn-ghost" href="patients.php">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Disease</th>
                        <th>Doctor</th>
                        <th>Admitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentPatients && mysqli_num_rows($recentPatients) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($recentPatients)) : ?>
                            <tr>
                                <td><?php echo e($row['full_name']); ?></td>
                                <td><?php echo e($row['disease']); ?></td>
                                <td><?php echo e($row['doctor_name'] ?? 'Unassigned'); ?></td>
                                <td><?php echo e(formatDate($row['admission_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="muted">No patients found yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Recent Tickets</h2>
            <a class="btn btn-ghost" href="tickets.php">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentTickets && mysqli_num_rows($recentTickets) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($recentTickets)) : ?>
                            <tr>
                                <td><?php echo e($row['full_name']); ?></td>
                                <td><?php echo e($row['doctor_name']); ?></td>
                                <td><?php echo e(formatDate($row['ticket_date'])); ?></td>
                                <td>$<?php echo number_format((float)$row['price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="muted">No tickets issued yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
