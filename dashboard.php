<?php
require_once 'config/session.php';
requireLogin();

require_once 'config/database.php';
require_once 'modules/business.php';
require_once 'modules/permit.php';
require_once 'modules/payment.php';

$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$business = new Business($db);
$permit = new Permit($db);
$payment = new Payment($db);

$total_businesses = $business->count();
$total_active_permits = $permit->countActive();
$total_expired_permits = $permit->countExpired();
$monthly_payments = $payment->getTotalMonthly();

// Get expiring permits
$expiring_permits = $permit->getExpiringSoon();

$current_user = getCurrentUser();

function renderDashboard() {
    global $total_businesses, $total_active_permits, $total_expired_permits, $monthly_payments, $expiring_permits, $current_user;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>BPLO Ilagan - Dashboard</title>
        <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="src/assets/vendors/ti-icons/css/themify-icons.css">
        <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="src/assets/vendors/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="src/assets/css/style.css">
        <link rel="stylesheet" href="src/assets/css/custom-blue.css">
        <link rel="shortcut icon" href="src/assets/images/favicon.png" />
    </head>
    <body>
        <div class="container-scroller">
            <?php include 'includes/sidebar.php'; ?>
            <div class="container-fluid page-body-wrapper">
                <?php include 'includes/navbar.php'; ?>
                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="page-header">
                            <h3 class="page-title">
                                <span class="page-title-icon bg-gradient-primary text-white me-2">
                                    <i class="mdi mdi-home"></i>
                                </span> Dashboard
                            </h3>
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        
                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-3 stretch-card grid-margin">
                                <div class="card bg-gradient-danger card-img-holder text-white">
                                    <div class="card-body">
                                        <img src="src/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                        <h4 class="font-weight-normal mb-3">Total Businesses <i class="mdi mdi-chart-line mdi-24px float-end"></i>
                                        </h4>
                                        <h2 class="mb-5"><?php echo $total_businesses; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card grid-margin">
                                <div class="card bg-gradient-info card-img-holder text-white">
                                    <div class="card-body">
                                        <img src="src/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                        <h4 class="font-weight-normal mb-3">Active Permits <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i>
                                        </h4>
                                        <h2 class="mb-5"><?php echo $total_active_permits; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card grid-margin">
                                <div class="card bg-gradient-warning card-img-holder text-white">
                                    <div class="card-body">
                                        <img src="src/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                        <h4 class="font-weight-normal mb-3">Expired Permits <i class="mdi mdi-alert-circle-outline mdi-24px float-end"></i>
                                        </h4>
                                        <h2 class="mb-5"><?php echo $total_expired_permits; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 stretch-card grid-margin">
                                <div class="card bg-gradient-success card-img-holder text-white">
                                    <div class="card-body">
                                        <img src="src/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                        <h4 class="font-weight-normal mb-3">Monthly Payments <i class="mdi mdi-cash-multiple mdi-24px float-end"></i>
                                        </h4>
                                        <h2 class="mb-5">₱<?php echo number_format($monthly_payments, 2); ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expiring Permits Alert -->
                        <div class="row">
                            <div class="col-12 grid-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Permits Expiring Soon (Within 30 Days)</h4>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Permit Number</th>
                                                        <th>Business Name</th>
                                                        <th>Owner</th>
                                                        <th>Expiry Date</th>
                                                        <th>Days Left</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $has_expiring = false;
                                                    while ($row = $expiring_permits->fetch(PDO::FETCH_ASSOC)): 
                                                        $has_expiring = true;
                                                        $expiry = new DateTime($row['expiry_date']);
                                                        $today = new DateTime();
                                                        $days_left = $today->diff($expiry)->days;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['permit_number']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($row['expiry_date'])); ?></td>
                                                        <td><span class="badge badge-warning"><?php echo $days_left; ?> days</span></td>
                                                        <td><span class="badge badge-success"><?php echo $row['status']; ?></span></td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                    <?php if (!$has_expiring): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No permits expiring soon</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
        
        <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="src/assets/js/off-canvas.js"></script>
        <script src="src/assets/js/misc.js"></script>
    </body>
    </html>
    <?php
}

renderDashboard();
?>
