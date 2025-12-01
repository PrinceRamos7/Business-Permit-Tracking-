<?php
require_once 'config/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'modules/renewal.php';
require_once 'modules/business.php';

$database = new Database();
$db = $database->getConnection();
$renewal = new Renewal($db);
$business = new Business($db);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $renewal->business_id = $_POST['business_id'];
            $renewal->renewal_date = $_POST['renewal_date'];
            $renewal->new_expiry_date = $_POST['new_expiry_date'];
            if ($renewal->create()) {
                $message = 'Renewal added successfully!';
                $message_type = 'success';
            }
            break;
        case 'update':
            $renewal->renewal_id = $_POST['renewal_id'];
            $renewal->business_id = $_POST['business_id'];
            $renewal->renewal_date = $_POST['renewal_date'];
            $renewal->new_expiry_date = $_POST['new_expiry_date'];
            if ($renewal->update()) {
                $message = 'Renewal updated successfully!';
                $message_type = 'success';
            }
            break;
        case 'delete':
            $renewal->renewal_id = $_POST['renewal_id'];
            if ($renewal->delete()) {
                $message = 'Renewal deleted successfully!';
                $message_type = 'success';
            }
            break;
    }
}

$renewals = $renewal->readAll();
$businesses = $business->readAll();
$current_user = getCurrentUser();

function renderRenewals() {
    global $renewals, $businesses, $message, $message_type, $current_user;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - Renewals</title>
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="src/assets/css/style.css">
    <link rel="stylesheet" href="src/assets/css/corona-dark.css">
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
                                <i class="mdi mdi-refresh"></i>
                            </span> Renewal Log
                        </h3>
                        <button class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#addRenewalModal">
                            <i class="mdi mdi-plus"></i> Add Renewal
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
                                    <h4 class="card-title">All Renewals</h4>
                                    
                                    <!-- Table Controls -->
                                    <div class="table-header">
                                        <div class="entries-control">
                                            <span>Show</span>
                                            <select><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option></select>
                                            <span>entries</span>
                                        </div>
                                        <div class="search-box">
                                            <input type="text" placeholder="Search">
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Business Name</th>
                                                    <th>Owner</th>
                                                    <th>Renewal Date</th>
                                                    <th>New Expiry Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $counter = 1;
                                                while ($row = $renewals->fetch(PDO::FETCH_ASSOC)): 
                                                ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                    <td><?php echo date('Y/m/d', strtotime($row['renewal_date'])); ?></td>
                                                    <td><?php echo date('Y/m/d', strtotime($row['new_expiry_date'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-gradient-info" onclick='editRenewal(<?php echo json_encode($row); ?>)'><i class="mdi mdi-pencil"></i></button>
                                                        <button class="btn btn-sm btn-gradient-danger" onclick="deleteRenewal(<?php echo $row['renewal_id']; ?>)"><i class="mdi mdi-delete"></i></button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="pagination-wrapper">
                                        <div class="pagination-info">Showing 1 to 10 entries</div>
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
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
    
    <!-- Add Renewal Modal -->
    <div class="modal fade" id="addRenewalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Renewal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business</label>
                            <div class="d-flex gap-2">
                                <select class="form-control" name="business_id" id="business_select_renewal" required style="flex: 1;">
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
                            <label class="form-label">Renewal Date</label>
                            <input type="date" class="form-control" name="renewal_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Expiry Date</label>
                            <input type="date" class="form-control" name="new_expiry_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Save Renewal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Renewal Modal -->
    <div class="modal fade" id="editRenewalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="renewal_id" id="edit_renewal_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Renewal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business ID</label>
                            <input type="number" class="form-control" name="business_id" id="edit_business_id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Renewal Date</label>
                            <input type="date" class="form-control" name="renewal_date" id="edit_renewal_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Expiry Date</label>
                            <input type="date" class="form-control" name="new_expiry_date" id="edit_new_expiry_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Update Renewal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="renewal_id" id="delete_renewal_id">
    </form>
    
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/misc.js"></script>
    <script>
        function editRenewal(renewal) {
            document.getElementById('edit_renewal_id').value = renewal.renewal_id;
            document.getElementById('edit_business_id').value = renewal.business_id;
            document.getElementById('edit_renewal_date').value = renewal.renewal_date;
            document.getElementById('edit_new_expiry_date').value = renewal.new_expiry_date;
            new bootstrap.Modal(document.getElementById('editRenewalModal')).show();
        }
        
        function deleteRenewal(id) {
            if (confirm('Are you sure you want to delete this renewal?')) {
                document.getElementById('delete_renewal_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>

<?php
}

renderRenewals();
?>
