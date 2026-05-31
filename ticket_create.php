<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Create Ticket';
$activePage = 'ticket-create';

$errors = [];
$formData = [
    'patient_id' => '',
    'ticket_date' => date('Y-m-d'),
    'price' => ''
];

$patients = [];
$patientResult = mysqli_query($conn, "SELECT id, full_name, phone FROM patients ORDER BY full_name ASC");
if ($patientResult) {
    while ($row = mysqli_fetch_assoc($patientResult)) {
        $patients[] = $row;
    }
}

if (isPost()) {
    $formData['patient_id'] = post('patient_id');
    $formData['ticket_date'] = post('ticket_date');
    $formData['price'] = post('price');

    if ($formData['patient_id'] === '' || (int)$formData['patient_id'] <= 0) {
        $errors[] = 'Please select a patient.';
    }

    if ($formData['ticket_date'] === '') {
        $errors[] = 'Ticket date is required.';
    }

    if ($formData['price'] === '' || !is_numeric($formData['price']) || (float)$formData['price'] <= 0) {
        $errors[] = 'Please enter a valid ticket price.';
    }

    $patientInfo = null;
    if (empty($errors)) {
        $patientId = (int)$formData['patient_id'];
        $stmt = mysqli_prepare(
            $conn,
            "SELECT p.id, p.full_name, p.phone, p.disease, p.blood_group, p.assigned_doctor_id, d.name AS doctor_name, d.specialization
             FROM patients p
             LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
             WHERE p.id = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "i", $patientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $patientInfo = mysqli_fetch_assoc($result);

        if (!$patientInfo) {
            $errors[] = 'Selected patient was not found.';
        } elseif (!$patientInfo['assigned_doctor_id']) {
            $errors[] = 'Assign a doctor to this patient before creating a ticket.';
        }
    }

    if (empty($errors) && $patientInfo) {
        $doctorId = (int)$patientInfo['assigned_doctor_id'];
        $price = (float)$formData['price'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO tickets (patient_id, doctor_id, ticket_date, price) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iisd", $patientInfo['id'], $doctorId, $formData['ticket_date'], $price);

        if (mysqli_stmt_execute($stmt)) {
            setFlash('success', 'Ticket created successfully.');
            redirect('tickets.php');
        }

        $errors[] = 'Unable to create ticket. Please try again.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Create Patient Ticket</h1>
        <p>Generate billing ticket with assigned doctor details.</p>
    </div>
    <a class="btn btn-outline" href="tickets.php">Back to Tickets</a>
</div>

<div class="panel">
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="form" data-validate="true">
        <div class="form-errors"></div>

        <div class="form-grid">
            <div class="input-group">
                <label>Patient <span class="required">*</span></label>
                <select name="patient_id" id="ticketPatient" required>
                    <option value="">Select patient</option>
                    <?php foreach ($patients as $patient) : ?>
                        <option value="<?php echo $patient['id']; ?>" <?php echo selectedAttr($patient['id'], $formData['patient_id']); ?>>
                            <?php echo e($patient['full_name']); ?> (<?php echo e($patient['phone']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Ticket Date <span class="required">*</span></label>
                <input type="date" name="ticket_date" value="<?php echo e($formData['ticket_date']); ?>" required>
            </div>
            <div class="input-group">
                <label>Price (USD) <span class="required">*</span></label>
                <input type="number" name="price" min="1" step="0.01" value="<?php echo e($formData['price']); ?>" required>
            </div>
        </div>

        <div class="info-panel" id="ticketInfo">
            <h3>Patient Snapshot</h3>
            <div class="info-grid" id="ticketPatientInfo">
                <div class="muted">Select a patient to view details.</div>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-receipt"></i>
                Create Ticket
            </button>
            <a class="btn btn-ghost" href="tickets.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
