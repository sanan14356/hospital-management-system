<?php
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main">
        <header class="topbar">
            <button class="menu-toggle" type="button" aria-label="Toggle menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-title"><?php echo e($pageTitle); ?></div>
            <div class="topbar-actions">
                <span class="chip">
                    <i class="fa-solid fa-user-shield"></i>
                    <?php echo e(currentAdminName()); ?>
                </span>
                <a class="btn btn-ghost" href="logout.php">Logout</a>
            </div>
        </header>

        <section class="content">
            <?php renderFlash(); ?>
