<?php
require_once 'config/session.php';
requireLogin();

require_once 'config/database.php';
require_once 'modules/business.php';

$database = new Database();
$db = $database->getConnection();
$business = new Business($db);

// Handle CRUD operations
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $business->business_name = $_POST['business_name'];
                $business->owner_name = $_POST['owner_name'];
                $business->business_address = $_POST['business_address'];
                $business->contact_number = $_POST['contact_number'];
                $business->business_type = $_POST['business_type'];
                $business->date_registered = $_POST['date_registered'];
                
                if ($business->create()) {
                    $message = 'Business added successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to add business.';
                    $message_type = 'danger';
                }
                break;
                
            case 'update':
                $business->business_id = $_POST['business_id'];
                $business->business_name = $_POST['business_name'];
                $business->owner_name = $_POST['owner_name'];
                $business->business_address = $_POST['business_address'];
                $business->contact_number = $_POST['contact_number'];
                $business->business_type = $_POST['business_type'];
                $business->date_registered = $_POST['date_registered'];
                
                if ($business->update()) {
                    $message = 'Business updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update business.';
                    $message_type = 'danger';
                }
                break;
                
            case 'delete':
                $business->business_id = $_POST['business_id'];
                if ($business->delete()) {
                    $message = 'Business deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete business.';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

// Get all businesses
$businesses = $business->readAll();

function renderBusinesses() {
    global $businesses, $message, $message_type;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>BPLO Ilagan - Businesses</title>
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
                                </span> Business Management
                            </h3>
                            <button class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
                                <i class="mdi mdi-plus"></i> Add Business
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
                                        <h4 class="card-title">All Businesses</h4>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Business Name</th>
                                                        <th>Owner</th>
                                                        <th>Address</th>
                                                        <th>Contact</th>
                                                        <th>Type</th>
                                                        <th>Date Registered</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $businesses->fetch(PDO::FETCH_ASSOC)): ?>
                                                    <tr>
                                                        <td><?php echo $row['business_id']; ?></td>
                                                        <td><a href="business_profile.php?id=<?php echo $row['business_id']; ?>"><?php echo htmlspecialchars($row['business_name']); ?></a></td>
                                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['business_address']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($row['business_type']); ?></span></td>
                                                        <td><?php echo date('M d, Y', strtotime($row['date_registered'])); ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-gradient-info" onclick="editBusiness(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-gradient-danger" onclick="deleteBusiness(<?php echo $row['business_id']; ?>, '<?php echo htmlspecialchars($row['business_name']); ?>')">
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
        
        <!-- Add Business Modal -->
        <div class="modal fade" id="addBusinessModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Business</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" class="form-control" name="business_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Owner Name</label>
                                    <input type="text" class="form-control" name="owner_name" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Business Address</label>
                                    <textarea class="form-control" name="business_address" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Type</label>
                                    <select class="form-control" name="business_type" required>
                                        <option value="">Select Type</option>
                                        <option value="Retail Store">Retail Store</option>
                                        <option value="Sari-Sari Store">Sari-Sari Store</option>
                                        <option value="Restaurant">Restaurant</option>
                                        <option value="Contractor">Contractor</option>
                                        <option value="Internet Cafe">Internet Cafe</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date Registered</label>
                                    <input type="date" class="form-control" name="date_registered" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-gradient-primary">Save Business</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Edit Business Modal -->
        <div class="modal fade" id="editBusinessModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Business</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editBusinessForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="business_id" id="edit_business_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" class="form-control" name="business_name" id="edit_business_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Owner Name</label>
                                    <input type="text" class="form-control" name="owner_name" id="edit_owner_name" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Business Address</label>
                                    <textarea class="form-control" name="business_address" id="edit_business_address" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" id="edit_contact_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Type</label>
                                    <select class="form-control" name="business_type" id="edit_business_type" required>
                                        <option value="Retail Store">Retail Store</option>
                                        <option value="Sari-Sari Store">Sari-Sari Store</option>
                                        <option value="Restaurant">Restaurant</option>
                                        <option value="Contractor">Contractor</option>
                                        <option value="Internet Cafe">Internet Cafe</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date Registered</label>
                                    <input type="date" class="form-control" name="date_registered" id="edit_date_registered" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-gradient-primary">Update Business</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Delete Confirmation Form -->
        <form method="POST" id="deleteForm" style="display:none;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="business_id" id="delete_business_id">
        </form>
        
        <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="src/assets/js/off-canvas.js"></script>
        <script src="src/assets/js/misc.js"></script>
        <script>
            function editBusiness(business) {
                document.getElementById('edit_business_id').value = business.business_id;
                document.getElementById('edit_business_name').value = business.business_name;
                document.getElementById('edit_owner_name').value = business.owner_name;
                document.getElementById('edit_business_address').value = business.business_address;
                document.getElementById('edit_contact_number').value = business.contact_number;
                document.getElementById('edit_business_type').value = business.business_type;
                document.getElementById('edit_date_registered').value = business.date_registered;
                
                var modal = new bootstrap.Modal(document.getElementById('editBusinessModal'));
                modal.show();
            }
            
            function deleteBusiness(id, name) {
                if (confirm('Are you sure you want to delete "' + name + '"?')) {
                    document.getElementById('delete_business_id').value = id;
                    document.getElementById('deleteForm').submit();
                }
            }
        </script>
    </body>
    </html>
    <?php
}

renderBusinesses();
?>
