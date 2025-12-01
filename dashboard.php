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
        <link rel="stylesheet" href="src/assets/css/corona-dark.css">
        <link rel="shortcut icon" href="src/assets/images/favicon.png" />
        <style>
            /* Corona Dark Theme */
            body {
                background: #191c24;
                color: #8e94a9;
            }
            
            .container-scroller {
                background: #191c24;
            }
            
            .content-wrapper {
                background: #191c24 !important;
            }
            
            .page-header {
                border-bottom: 1px solid #2c2e33;
            }
            
            .page-title {
                color: #ffffff;
                font-weight: 600;
            }
            
            /* Stat Cards - Corona Style */
            .card {
                background: #1f2128;
                border: 1px solid #2c2e33;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            /* Gradient Cards */
            .bg-gradient-danger {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border: none;
            }
            
            .bg-gradient-info {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
                border: none;
            }
            
            .bg-gradient-warning {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
                border: none;
            }
            
            .bg-gradient-success {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
                border: none;
            }
            
            .card-img-holder .card-body {
                position: relative;
                z-index: 1;
            }
            
            .card h4 {
                font-size: 0.875rem;
                font-weight: 500;
                opacity: 0.9;
            }
            
            .card h2 {
                font-size: 2rem;
                font-weight: 700;
            }
            
            /* Table Styling */
            .table {
                color: #8e94a9;
            }
            
            .table thead th {
                background: #1f2128;
                color: #ffffff;
                border-bottom: 2px solid #2c2e33;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
                padding: 1rem;
            }
            
            .table tbody tr {
                border-bottom: 1px solid #2c2e33;
                transition: all 0.3s ease;
            }
            
            .table tbody tr:hover {
                background: #23252b;
            }
            
            .table tbody td {
                padding: 1rem;
                vertical-align: middle;
            }
            
            /* Badges */
            .badge {
                padding: 0.5rem 1rem;
                border-radius: 6px;
                font-weight: 600;
                font-size: 0.75rem;
            }
            
            .badge-warning {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                color: #ffffff;
            }
            
            .badge-success {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                color: #ffffff;
            }
            
            /* Card Title */
            .card-title {
                color: #ffffff;
                font-weight: 600;
                font-size: 1.125rem;
                margin-bottom: 1.5rem;
            }
            
            /* Breadcrumb */
            .breadcrumb {
                background: transparent;
                color: #8e94a9;
            }
            
            .breadcrumb-item.active {
                color: #8e94a9;
            }
            
            /* Icon Styling */
            .page-title-icon {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                width: 40px;
                height: 40px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .card h2 {
                    font-size: 1.5rem;
                }
                
                .card h4 {
                    font-size: 0.75rem;
                }
            }
        </style>
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
                                        <h4 class="font-weight-normal mb-3">Expired Permits <i class="mdi mdi-alert mdi-24px float-end"></i>
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
                                        <h4 class="card-title">Permits Expiring Soon</h4>
                                        
                                        <!-- Table Controls -->
                                        <div class="table-header">
                                            <div class="entries-control">
                                                <span>Show</span>
                                                <select id="entriesPerPage">
                                                    <option value="5">5</option>
                                                    <option value="10" selected>10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                </select>
                                                <span>entries</span>
                                            </div>
                                            <div class="search-box">
                                                <input type="text" id="tableSearch" placeholder="Search">
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Permit Number</th>
                                                        <th>Business Name</th>
                                                        <th>Owner</th>
                                                        <th>Expiry Date</th>
                                                        <th>Days Left</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $has_expiring = false;
                                                    $counter = 1;
                                                    while ($row = $expiring_permits->fetch(PDO::FETCH_ASSOC)): 
                                                        $has_expiring = true;
                                                        $expiry = new DateTime($row['expiry_date']);
                                                        $today = new DateTime();
                                                        $days_left = $today->diff($expiry)->days;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td><?php echo htmlspecialchars($row['permit_number']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                        <td><?php echo date('Y/m/d', strtotime($row['expiry_date'])); ?></td>
                                                        <td><span class="badge badge-warning"><?php echo $days_left; ?> days</span></td>
                                                        <td><span class="badge badge-success"><?php echo $row['status']; ?></span></td>
                                                        <td><a href="permits.php?id=<?php echo $row['permit_id']; ?>" class="action-link">View</a></td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                    <?php if (!$has_expiring): ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center" style="padding: 2rem; color: var(--text-secondary);">No permits expiring soon</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <div class="pagination-wrapper">
                                            <div class="pagination-info">
                                                Showing 1 to 5 of 10 entries
                                            </div>
                                            <ul class="pagination">
                                                <li class="page-item disabled">
                                                    <a class="page-link" href="#">Previous</a>
                                                </li>
                                                <li class="page-item active">
                                                    <a class="page-link" href="#">1</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">2</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">Next</a>
                                                </li>
                                            </ul>
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
