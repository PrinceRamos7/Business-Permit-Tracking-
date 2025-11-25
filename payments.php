<?php
require_once 'config/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'modules/payment.php';
require_once 'modules/business.php';

$database = new Database();
$db = $database->getConnection();
$payment = new Payment($db);
$business = new Business($db);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $payment->business_id = $_POST['business_id'];
            $payment->amount = $_POST['amount'];
            $payment->OR_number = $_POST['OR_number'];
            $payment->payment_date = $_POST['payment_date'];
            $message = $payment->create() ? 'Payment added successfully!' : 'Failed to add payment.';
            $message_type = $payment->create() ? 'success' : 'danger';
            break;
        case 'update':
            $payment->payment_id = $_POST['payment_id'];
            $payment->business_id = $_POST['business_id'];
            $payment->amount = $_POST['amount'];
            $payment->OR_number = $_POST['OR_number'];
            $payment->payment_date = $_POST['payment_date'];
            $message = $payment->update() ? 'Payment updated successfully!' : 'Failed to update payment.';
            $message_type = $payment->update() ? 'success' : 'danger';
            break;
        case 'delete':
            $payment->payment_id = $_POST['payment_id'];
            $message = $payment->delete() ? 'Payment deleted successfully!' : 'Failed to delete payment.';
            $message_type = $payment->delete() ? 'success' : 'danger';
            break;
    }
}

$payments = $payment->readAll();
$businesses = $business->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - Payments</title>
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
                                <i class="mdi mdi-cash-multiple"></i>
                            </span> Payment Records
                        </h3>
                        <button class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="mdi mdi-plus"></i> Add Payment
                        </button>
                    </div>
                    
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Payments</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>OR Number</th>
                                                    <th>Business Name</th>
                                                    <th>Owner</th>
                                                    <th>Amount</th>
                                                    <th>Payment Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $payments->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['OR_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                    <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-gradient-info" onclick='editPayment(<?php echo json_encode($row); ?>)'>
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-gradient-danger" onclick="deletePayment(<?php echo $row['payment_id']; ?>)">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
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
    
    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business</label>
                            <div class="d-flex gap-2">
                                <select class="form-control" name="business_id" id="business_select_payment" required style="flex: 1;">
                                    <option value="">Select Business</option>
                                    <?php while ($b = $businesses->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $b['business_id']; ?>"><?php echo htmlspecialchars($b['business_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="button" class="btn btn-gradient-success" onclick="window.open('businesses.php', '_blank')" title="Add New Business">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted">Can't find the business? Click the + button to add a new one.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OR Number</label>
                            <input type="text" class="form-control" name="OR_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="payment_id" id="edit_payment_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business ID</label>
                            <input type="number" class="form-control" name="business_id" id="edit_business_id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="edit_amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OR Number</label>
                            <input type="text" class="form-control" name="OR_number" id="edit_OR_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" id="edit_payment_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Update Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="payment_id" id="delete_payment_id">
    </form>
    
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/misc.js"></script>
    <script>
        function editPayment(payment) {
            document.getElementById('edit_payment_id').value = payment.payment_id;
            document.getElementById('edit_business_id').value = payment.business_id;
            document.getElementById('edit_amount').value = payment.amount;
            document.getElementById('edit_OR_number').value = payment.OR_number;
            document.getElementById('edit_payment_date').value = payment.payment_date;
            new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
        }
        
        function deletePayment(id) {
            if (confirm('Are you sure you want to delete this payment?')) {
                document.getElementById('delete_payment_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
