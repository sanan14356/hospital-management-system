<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$ticketId = (int)get('id');
if ($ticketId <= 0) {
    setFlash('error', 'Invalid ticket.');
    redirect('tickets.php');
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT t.*, p.full_name, p.phone, p.email, p.disease, d.name AS doctor_name, d.specialization
     FROM tickets t
     INNER JOIN patients p ON t.patient_id = p.id
     INNER JOIN doctors d ON t.doctor_id = d.id
     WHERE t.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $ticketId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($result);

if (!$ticket) {
    setFlash('error', 'Ticket not found.');
    redirect('tickets.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #T<?php echo e($ticket['id']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="print-body" onload="window.print()">
    <div class="print-page">
        <div class="print-header">
            <h1>Patient Ticket</h1>
            <span>#T<?php echo e($ticket['id']); ?></span>
        </div>

        <div class="print-section">
            <h2>Patient Details</h2>
            <div class="print-grid">
                <div><strong>Name:</strong> <?php echo e($ticket['full_name']); ?></div>
                <div><strong>Phone:</strong> <?php echo e($ticket['phone']); ?></div>
                <div><strong>Email:</strong> <?php echo e($ticket['email']); ?></div>
                <div><strong>Disease:</strong> <?php echo e($ticket['disease']); ?></div>
            </div>
        </div>

        <div class="print-section">
            <h2>Assigned Doctor</h2>
            <div class="print-grid">
                <div><strong>Name:</strong> <?php echo e($ticket['doctor_name']); ?></div>
                <div><strong>Specialization:</strong> <?php echo e($ticket['specialization']); ?></div>
            </div>
        </div>

        <div class="print-section">
            <h2>Billing Details</h2>
            <div class="print-grid">
                <div><strong>Date:</strong> <?php echo e(formatDate($ticket['ticket_date'])); ?></div>
                <div><strong>Price:</strong> $<?php echo number_format((float)$ticket['price'], 2); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
