<?php
function requireLogin()
{
    if (empty($_SESSION['admin_id'])) {
        setFlash('error', 'Please login to continue.');
        redirect('login.php');
    }
}

function currentAdminName()
{
    return $_SESSION['admin_name'] ?? 'Admin';
}
