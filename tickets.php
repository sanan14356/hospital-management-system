<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Tickets';
$activePage = 'tickets';

$tickets = mysqli_query(
    $conn,
    "SELECT t.*, p.full_name, p.phone, d.name AS doctor_name, d.specialization
     FROM tickets t
     INNER JOIN patients p ON t.patient_id = p.id
     INNER JOIN doctors d ON t.doctor_id = d.id
     ORDER BY t.created_at DESC"
);

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Ticket System</h1>
        <p>Generate and manage patient billing tickets.</p>
    </div>
    <a class="btn btn-primary" href="ticket_create.php">
        <i class="fa-solid fa-receipt"></i>
        Create Ticket
    </a>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Price</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tickets && mysqli_num_rows($tickets) > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($tickets)) : ?>
                        <tr>
                            <td>#T<?php echo e($row['id']); ?></td>
                            <td>
                                <div class="cell-title"><?php echo e($row['full_name']); ?></div>
                                <div class="cell-subtitle"><?php echo e($row['phone']); ?></div>
                            </td>
                            <td><?php echo e($row['doctor_name']); ?></td>
                            <td><?php echo e(formatDate($row['ticket_date'])); ?></td>
                            <td>$<?php echo number_format((float)$row['price'], 2); ?></td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-outline" href="ticket_print.php?id=<?php echo $row['id']; ?>" target="_blank">Print</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="muted">No tickets issued yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
