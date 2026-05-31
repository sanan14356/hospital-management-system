<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$errors = [];
$email = '';

if (isPost()) {
    $email = post('email');
    $password = post('password');

    if ($email === '' || !validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password FROM admins WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $email;

            setFlash('success', 'Welcome back, ' . $admin['full_name'] . '.');
            redirect('dashboard.php');
        }

        $errors[] = 'Invalid email or password.';
    }
}

$pageTitle = 'Admin Login';
include __DIR__ . '/includes/auth_header.php';
?>

<div class="auth-head">
    <div class="auth-icon">
        <i class="fa-solid fa-hospital"></i>
    </div>
    <h1>Admin Login</h1>
    <p>Sign in to manage patients, doctors, and tickets.</p>
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
        <label for="email">Email Address <span class="required">*</span></label>
        <input id="email" type="email" name="email" value="<?php echo e($email); ?>" data-validate="email" placeholder="admin@hospital.test" required>
    </div>

    <div class="input-group">
        <label for="password">Password <span class="required">*</span></label>
        <input id="password" type="password" name="password" placeholder="Enter your password" required>
    </div>

    <button class="btn btn-primary btn-block" type="submit">
        <i class="fa-solid fa-lock"></i>
        Login
    </button>
</form>

<div class="auth-footer">
    <span>New here?</span>
    <a href="register.php">Create an admin account</a>
</div>

<?php include __DIR__ . '/includes/auth_footer.php'; ?>
