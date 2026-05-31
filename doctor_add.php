<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Add Doctor';
$activePage = 'doctors';

$errors = [];
$formData = [
    'name' => '',
    'specialization' => '',
    'phone' => '',
    'email' => '',
    'experience' => ''
];

if (isPost()) {
    $formData['name'] = post('name');
    $formData['specialization'] = post('specialization');
    $formData['phone'] = post('phone');
    $formData['email'] = post('email');
    $formData['experience'] = post('experience');

    if ($formData['name'] === '' || !validateName($formData['name'])) {
        $errors[] = 'Doctor name is required and must not contain numbers.';
    }

    if ($formData['specialization'] === '' || !validateTextNoNumbers($formData['specialization'])) {
        $errors[] = 'Specialization is required and must not contain numbers.';
    }

    if ($formData['phone'] === '' || !validatePhone($formData['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }

    if ($formData['email'] === '' || !validateEmail($formData['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($formData['experience'] === '' || (int)$formData['experience'] < 0) {
        $errors[] = 'Experience must be a valid number of years.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO doctors (name, specialization, phone, email, experience) VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $formData['name'],
            $formData['specialization'],
            $formData['phone'],
            $formData['email'],
            $formData['experience']
        );

        if (mysqli_stmt_execute($stmt)) {
            setFlash('success', 'Doctor added successfully.');
            redirect('doctors.php');
        }

        $errors[] = 'Unable to save doctor. Please try again.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Add Doctor</h1>
        <p>Register a new doctor and specialization.</p>
    </div>
    <a class="btn btn-outline" href="doctors.php">Back to Doctors</a>
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
                <label>Doctor Name <span class="required">*</span></label>
                <input type="text" name="name" value="<?php echo e($formData['name']); ?>" data-validate="name" required>
            </div>
            <div class="input-group">
                <label>Specialization / Field <span class="required">*</span></label>
                <input type="text" name="specialization" value="<?php echo e($formData['specialization']); ?>" data-validate="text" required>
            </div>
            <div class="input-group">
                <label>Phone <span class="required">*</span></label>
                <input type="text" name="phone" value="<?php echo e($formData['phone']); ?>" data-validate="phone" required>
            </div>
            <div class="input-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="<?php echo e($formData['email']); ?>" data-validate="email" required>
            </div>
            <div class="input-group">
                <label>Experience (years) <span class="required">*</span></label>
                <input type="number" name="experience" min="0" value="<?php echo e($formData['experience']); ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                Save Doctor
            </button>
            <a class="btn btn-ghost" href="doctors.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
