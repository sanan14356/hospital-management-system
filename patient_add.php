<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$pageTitle = 'Add Patient';
$activePage = 'patients';

$errors = [];
$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$genders = ['Male', 'Female', 'Other'];
$diseases = ['Heart Problem', 'Skin Disease', 'Fever', 'Eye Problem', 'Bone Problem', 'Diabetes', 'Hypertension'];

$doctorResult = mysqli_query($conn, "SELECT id, name, specialization FROM doctors ORDER BY name ASC");
$doctors = [];
if ($doctorResult) {
    while ($row = mysqli_fetch_assoc($doctorResult)) {
        $doctors[] = $row;
    }
}

$formData = [
    'full_name' => '',
    'age' => '',
    'gender' => '',
    'phone' => '',
    'email' => '',
    'address' => '',
    'disease' => '',
    'blood_group' => '',
    'admission_date' => date('Y-m-d'),
    'assigned_doctor_id' => ''
];

if (isPost()) {
    $formData['full_name'] = post('full_name');
    $formData['age'] = post('age');
    $formData['gender'] = post('gender');
    $formData['phone'] = post('phone');
    $formData['email'] = post('email');
    $formData['address'] = post('address');
    $formData['disease'] = post('disease');
    $formData['blood_group'] = post('blood_group');
    $formData['admission_date'] = post('admission_date');
    $formData['assigned_doctor_id'] = post('assigned_doctor_id');

    if ($formData['full_name'] === '' || !validateName($formData['full_name'])) {
        $errors[] = 'Full name is required and must not contain numbers.';
    }

    if ($formData['age'] === '' || (int)$formData['age'] <= 0) {
        $errors[] = 'Please enter a valid age.';
    }

    if ($formData['gender'] === '') {
        $errors[] = 'Gender is required.';
    }

    if ($formData['phone'] === '' || !validatePhone($formData['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }

    if ($formData['email'] !== '' && !validateEmail($formData['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($formData['disease'] === '') {
        $errors[] = 'Illness/Disease is required.';
    }

    if ($formData['blood_group'] === '') {
        $errors[] = 'Blood group is required.';
    }

    if ($formData['admission_date'] === '') {
        $errors[] = 'Admission date is required.';
    }

    if (empty($errors)) {
        $assignedDoctorId = $formData['assigned_doctor_id'] !== '' ? (int)$formData['assigned_doctor_id'] : null;

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO patients (full_name, age, gender, phone, email, address, disease, blood_group, admission_date, assigned_doctor_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sisssssssi",
            $formData['full_name'],
            $formData['age'],
            $formData['gender'],
            $formData['phone'],
            $formData['email'],
            $formData['address'],
            $formData['disease'],
            $formData['blood_group'],
            $formData['admission_date'],
            $assignedDoctorId
        );

        if (mysqli_stmt_execute($stmt)) {
            $patientId = mysqli_insert_id($conn);

            if ($assignedDoctorId) {
                $assignStmt = mysqli_prepare($conn, "INSERT INTO assignments (patient_id, doctor_id, is_active) VALUES (?, ?, 1)");
                mysqli_stmt_bind_param($assignStmt, "ii", $patientId, $assignedDoctorId);
                mysqli_stmt_execute($assignStmt);
            }

            setFlash('success', 'Patient added successfully.');
            redirect('patients.php');
        }

        $errors[] = 'Unable to save patient. Please try again.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Add Patient</h1>
        <p>Capture full medical information for a new patient.</p>
    </div>
    <a class="btn btn-outline" href="patients.php">Back to Patients</a>
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
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" value="<?php echo e($formData['full_name']); ?>" data-validate="name" required>
            </div>
            <div class="input-group">
                <label>Age <span class="required">*</span></label>
                <input type="number" name="age" min="1" value="<?php echo e($formData['age']); ?>" required>
            </div>
            <div class="input-group">
                <label>Gender <span class="required">*</span></label>
                <select name="gender" required>
                    <option value="">Select gender</option>
                    <?php foreach ($genders as $gender) : ?>
                        <option value="<?php echo e($gender); ?>" <?php echo selectedAttr($gender, $formData['gender']); ?>><?php echo e($gender); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Phone <span class="required">*</span></label>
                <input type="text" name="phone" value="<?php echo e($formData['phone']); ?>" data-validate="phone" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo e($formData['email']); ?>" data-validate="email">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" value="<?php echo e($formData['address']); ?>">
            </div>
            <div class="input-group">
                <label>Illness / Disease <span class="required">*</span></label>
                <input type="text" name="disease" list="diseaseOptions" value="<?php echo e($formData['disease']); ?>" required>
                <datalist id="diseaseOptions">
                    <?php foreach ($diseases as $option) : ?>
                        <option value="<?php echo e($option); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="input-group">
                <label>Blood Group <span class="required">*</span></label>
                <select name="blood_group" required>
                    <option value="">Select group</option>
                    <?php foreach ($bloodGroups as $group) : ?>
                        <option value="<?php echo e($group); ?>" <?php echo selectedAttr($group, $formData['blood_group']); ?>><?php echo e($group); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Admission Date <span class="required">*</span></label>
                <input type="date" name="admission_date" value="<?php echo e($formData['admission_date']); ?>" required>
            </div>
            <div class="input-group">
                <label>Assign Doctor (optional)</label>
                <select name="assigned_doctor_id">
                    <option value="">Unassigned</option>
                    <?php foreach ($doctors as $doctor) : ?>
                        <option value="<?php echo $doctor['id']; ?>" <?php echo selectedAttr($doctor['id'], $formData['assigned_doctor_id']); ?>>
                            <?php echo e($doctor['name']); ?> - <?php echo e($doctor['specialization']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                Save Patient
            </button>
            <a class="btn btn-ghost" href="patients.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
