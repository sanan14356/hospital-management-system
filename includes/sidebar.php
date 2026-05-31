<?php
$activePage = $activePage ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-hospital"></i>
        </div>
        <div class="brand-text">
            <div class="brand-title">Hospital</div>
            <div class="brand-subtitle">Management</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a class="nav-link <?php echo $activePage === 'patients' ? 'active' : ''; ?>" href="patients.php">
            <i class="fa-solid fa-bed-pulse"></i>
            <span>Patients</span>
        </a>
        <a class="nav-link <?php echo $activePage === 'doctors' ? 'active' : ''; ?>" href="doctors.php">
            <i class="fa-solid fa-user-doctor"></i>
            <span>Doctors</span>
        </a>
        <a class="nav-link <?php echo $activePage === 'tickets' ? 'active' : ''; ?>" href="tickets.php">
            <i class="fa-solid fa-file-invoice"></i>
            <span>Tickets</span>
        </a>
        <a class="nav-link <?php echo $activePage === 'ticket-create' ? 'active' : ''; ?>" href="ticket_create.php">
            <i class="fa-solid fa-receipt"></i>
            <span>Create Ticket</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <i class="fa-solid fa-user-shield"></i>
            <span><?php echo e(currentAdminName()); ?></span>
        </div>
        <a class="btn btn-ghost" href="logout.php">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Logout
        </a>
    </div>
</aside>
