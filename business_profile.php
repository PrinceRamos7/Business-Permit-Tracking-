<?php
require_once 'config/session.php';
requireLogin();

require_once 'config/database.php';
require_once 'modules/business.php';
require_once 'modules/permit.php';
require_once 'modules/payment.php';
require_once 'modules/renewal.php';

$database = new Database();
$db = $database->getConnection();

$business_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Business ID not found.');

$business = new Business($db);
$business->business_id = $business_id;

if (!$business->readOne()) {
    die('Business not found.');
}

$permit = new Permit($db);
$permits = $permit->readByBusiness($business_id);

$payment = new Payment($db);
$payments = $payment->readByBusiness($business_id);

$renewal = new Renewal($db);
$renewals = $renewal->readByBusiness($business_id);

function renderBusinessProfile() {
    global $business, $permits, $payments, $renewals;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>BPLO Ilagan - Business Profile</title>
        <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="src/assets/css/style.css">
        <link rel="stylesheet" href="src/assets/css/custom-blue.css">
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
                                    <i class="mdi mdi-store"></i>
                                </span> Business Profile
                            </h3>
                            <a href="businesses.php" class="btn btn-gradient-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title"><?php echo htmlspecialchars($business->business_name); ?></h4>
                                        <p class="card-description">Complete business information and records</p>
                                        
                                        <!-- Tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="business-info-tab" data-bs-toggle="tab" href="#business-info" role="tab">
                                                    <i class="mdi mdi-information"></i> Business Info
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="permits-tab" data-bs-toggle="tab" href="#permits" role="tab">
                                                    <i class="mdi mdi-file-document"></i> Permits
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments" role="tab">
                                                    <i class="mdi mdi-cash-multiple"></i> Payments
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="renewals-tab" data-bs-toggle="tab" href="#renewals" role="tab">
                                                    <i class="mdi mdi-refresh"></i> Renewals
                                                </a>
                                            </li>
                                        </ul>
                                        
                                        <div class="tab-content">
                                            <!-- Business Info Tab -->
                                            <div class="tab-pane fade show active" id="business-info" role="tabpanel">
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Business Name:</strong></label>
                                                            <p><?php echo htmlspecialchars($business->business_name); ?></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Owner Name:</strong></label>
                                                            <p><?php echo htmlspecialchars($business->owner_name); ?></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Contact Number:</strong></label>
                                                            <p><?php echo htmlspecialchars($business->contact_number); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Business Type:</strong></label>
                                                            <p><span class="badge badge-info"><?php echo htmlspecialchars($business->business_type); ?></span></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Date Registered:</strong></label>
                                                            <p><?php echo date('F d, Y', strtotime($business->date_registered)); ?></p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Business Address:</strong></label>
                                                            <p><?php echo htmlspecialchars($business->business_address); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Permits Tab -->
                                            <div class="tab-pane fade" id="permits" role="tabpanel">
                                                <div class="table-responsive mt-4">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Permit Number</th>
                                                                <th>Date Issued</th>
                                                                <th>Expiry Date</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $has_permits = false;
                                                            while ($row = $permits->fetch(PDO::FETCH_ASSOC)): 
                                                                $has_permits = true;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($row['permit_number']); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($row['date_issued'])); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($row['expiry_date'])); ?></td>
                                                                <td>
                                                                    <?php 
                                                                    $badge_class = $row['status'] == 'Active' ? 'success' : ($row['status'] == 'Expired' ? 'danger' : 'warning');
                                                                    ?>
                                                                    <span class="badge badge-<?php echo $badge_class; ?>"><?php echo $row['status']; ?></span>
                                                                </td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                            <?php if (!$has_permits): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center">No permits found</td>
                                                            </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <!-- Payments Tab -->
                                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                                <div class="table-responsive mt-4">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>OR Number</th>
                                                                <th>Amount</th>
                                                                <th>Payment Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $has_payments = false;
                                                            $total_paid = 0;
                                                            while ($row = $payments->fetch(PDO::FETCH_ASSOC)): 
                                                                $has_payments = true;
                                                                $total_paid += $row['amount'];
                                                            ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($row['OR_number']); ?></td>
                                                                <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                            <?php if (!$has_payments): ?>
                                                            <tr>
                                                                <td colspan="3" class="text-center">No payments found</td>
                                                            </tr>
                                                            <?php else: ?>
                                                            <tr class="table-info">
                                                                <td><strong>Total Paid:</strong></td>
                                                                <td colspan="2"><strong>₱<?php echo number_format($total_paid, 2); ?></strong></td>
                                                            </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <!-- Renewals Tab -->
                                            <div class="tab-pane fade" id="renewals" role="tabpanel">
                                                <div class="table-responsive mt-4">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Renewal Date</th>
                                                                <th>New Expiry Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $has_renewals = false;
                                                            while ($row = $renewals->fetch(PDO::FETCH_ASSOC)): 
                                                                $has_renewals = true;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo date('M d, Y', strtotime($row['renewal_date'])); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($row['new_expiry_date'])); ?></td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                            <?php if (!$has_renewals): ?>
                                                            <tr>
                                                                <td colspan="2" class="text-center">No renewals found</td>
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

renderBusinessProfile();
?>
