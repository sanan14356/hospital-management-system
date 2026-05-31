<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$errors = [];
$fullName = '';
$email = '';

if (isPost()) {
    $fullName = post('full_name');
    $email = post('email');
    $password = post('password');
    $confirm = post('confirm_password');

    if ($fullName === '' || !validateName($fullName)) {
        $errors[] = 'Full name is required and must not contain numbers.';
    }

    if ($email === '' || !validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($confirm !== $password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM admins WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($result)) {
            $errors[] = 'An admin account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $hashed);

        if (mysqli_stmt_execute($stmt)) {
            setFlash('success', 'Registration successful. Please login.');
            redirect('login.php');
        }

        $errors[] = 'Registration failed. Please try again.';
    }
}

$pageTitle = 'Admin Register';
include __DIR__ . '/includes/auth_header.php';
?>

<div class="auth-head">
    <div class="auth-icon">
        <i class="fa-solid fa-user-plus"></i>
    </div>
    <h1>Create Admin Account</h1>
    <p>Register an admin account for this hospital system.</p>
</div>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error) : ?>
            <div><?php echo e($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" class="form" data-validate="true">
    <div class="form-errors"></div>

    <div class="input-group">
        <label for="full_name">Full Name <span class="required">*</span></label>
        <input id="full_name" type="text" name="full_name" value="<?php echo e($fullName); ?>" data-validate="name" placeholder="Dr. Alex Johnson" required>
    </div>

    <div class="input-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input id="email" type="email" name="email" value="<?php echo e($email); ?>" data-validate="email" placeholder="admin@hospital.test" required>
    </div>

    <div class="input-group">
        <label for="password">Password <span class="required">*</span></label>
        <input id="password" type="password" name="password" placeholder="Create a password" required>
    </div>

    <div class="input-group">
        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
        <input id="confirm_password" type="password" name="confirm_password" placeholder="Re-enter password" required>
    </div>

    <button class="btn btn-primary btn-block" type="submit">
        <i class="fa-solid fa-user-check"></i>
        Register
    </button>
</form>

<div class="auth-footer">
    <span>Already have an account?</span>
    <a href="login.php">Back to login</a>
</div>

<?php include __DIR__ . '/includes/auth_footer.php'; ?>
