<?php
require_once 'config/session.php';
requireLogin();

require_once 'config/database.php';
require_once 'modules/permit.php';
require_once 'modules/business.php';

$database = new Database();
$db = $database->getConnection();
$permit = new Permit($db);
$business = new Business($db);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $permit->business_id = $_POST['business_id'];
                $permit->permit_number = $permit->generatePermitNumber();
                $permit->date_issued = $_POST['date_issued'];
                $permit->expiry_date = $_POST['expiry_date'];
                $permit->status = $_POST['status'];
                
                if ($permit->create()) {
                    $message = 'Permit added successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to add permit.';
                    $message_type = 'danger';
                }
                break;
                
            case 'update':
                $permit->permit_id = $_POST['permit_id'];
                $permit->business_id = $_POST['business_id'];
                $permit->permit_number = $_POST['permit_number'];
                $permit->date_issued = $_POST['date_issued'];
                $permit->expiry_date = $_POST['expiry_date'];
                $permit->status = $_POST['status'];
                
                if ($permit->update()) {
                    $message = 'Permit updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update permit.';
                    $message_type = 'danger';
                }
                break;
                
            case 'delete':
                $permit->permit_id = $_POST['permit_id'];
                if ($permit->delete()) {
                    $message = 'Permit deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete permit.';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

$permits = $permit->readAll();
$businesses = $business->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - Permits</title>
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
                                <i class="mdi mdi-file-document"></i>
                            </span> Permit Management
                        </h3>
                        <button class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#addPermitModal">
                            <i class="mdi mdi-plus"></i> Add Permit
                        </button>
                    </div>
                    
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Permits</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Permit Number</th>
                                                    <th>Business Name</th>
                                                    <th>Owner</th>
                                                    <th>Date Issued</th>
                                                    <th>Expiry Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $permits->fetch(PDO::FETCH_ASSOC)): 
                                                    $badge_class = $row['status'] == 'Active' ? 'success' : ($row['status'] == 'Expired' ? 'danger' : 'warning');
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['permit_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($row['date_issued'])); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($row['expiry_date'])); ?></td>
                                                    <td><span class="badge badge-<?php echo $badge_class; ?>"><?php echo $row['status']; ?></span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-gradient-info" onclick='editPermit(<?php echo json_encode($row); ?>)'>
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-gradient-danger" onclick="deletePermit(<?php echo $row['permit_id']; ?>, '<?php echo htmlspecialchars($row['permit_number']); ?>')">
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
    
    <!-- Add Permit Modal -->
    <div class="modal fade" id="addPermitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Permit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business</label>
                            <div class="d-flex gap-2">
                                <select class="form-control" name="business_id" id="business_select_permit" required style="flex: 1;">
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
                            <label class="form-label">Date Issued</label>
                            <input type="date" class="form-control" name="date_issued" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Expired">Expired</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Save Permit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Permit Modal -->
    <div class="modal fade" id="editPermitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Permit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="permit_id" id="edit_permit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Business ID</label>
                            <input type="number" class="form-control" name="business_id" id="edit_business_id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permit Number</label>
                            <input type="text" class="form-control" name="permit_number" id="edit_permit_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Issued</label>
                            <input type="date" class="form-control" name="date_issued" id="edit_date_issued" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="Active">Active</option>
                                <option value="Expired">Expired</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Update Permit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="permit_id" id="delete_permit_id">
    </form>
    
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/misc.js"></script>
    <script>
        function editPermit(permit) {
            document.getElementById('edit_permit_id').value = permit.permit_id;
            document.getElementById('edit_business_id').value = permit.business_id;
            document.getElementById('edit_permit_number').value = permit.permit_number;
            document.getElementById('edit_date_issued').value = permit.date_issued;
            document.getElementById('edit_expiry_date').value = permit.expiry_date;
            document.getElementById('edit_status').value = permit.status;
            
            var modal = new bootstrap.Modal(document.getElementById('editPermitModal'));
            modal.show();
        }
        
        function deletePermit(id, number) {
            if (confirm('Are you sure you want to delete permit "' + number + '"?')) {
                document.getElementById('delete_permit_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
