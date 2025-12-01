<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-start">
        <a class="sidebar-brand brand-logo" href="dashboard.php">
            <img src="src/assets/images/logos/bplo.jpg" alt="BPLO Logo">
            <span>BPLO ILAGAN</span>
        </a>
        <a class="sidebar-brand brand-logo-mini" href="dashboard.php">
            <img src="src/assets/images/logos/bplo.jpg" alt="BPLO Logo">
        </a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc">
                <div class="profile-pic">
                    <div class="count-indicator">
                        <img class="img-xs rounded-circle" src="src/assets/images/faces/face15.jpg" alt="">
                        <span class="count bg-success"></span>
                    </div>
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal"><?php echo htmlspecialchars($_SESSION['username']); ?></h5>
                        <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="dashboard.php">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'businesses.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="businesses.php">
                <span class="menu-icon">
                    <i class="mdi mdi-store"></i>
                </span>
                <span class="menu-title">Businesses</span>
            </a>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'permits.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="permits.php">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document"></i>
                </span>
                <span class="menu-title">Permits</span>
            </a>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'payments.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="payments.php">
                <span class="menu-icon">
                    <i class="mdi mdi-cash-multiple"></i>
                </span>
                <span class="menu-title">Payments</span>
            </a>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'renewals.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="renewals.php">
                <span class="menu-icon">
                    <i class="mdi mdi-refresh"></i>
                </span>
                <span class="menu-title">Renewals</span>
            </a>
        </li>

        <?php if (isAdmin()): ?>
        <li class="nav-item nav-category">
            <span class="nav-link">System</span>
        </li>
        <li class="nav-item menu-items <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="users.php">
                <span class="menu-icon">
                    <i class="mdi mdi-account-multiple"></i>
                </span>
                <span class="menu-title">User Accounts</span>
            </a>
        </li>
        <?php endif; ?>
        
    </ul>
</nav>
