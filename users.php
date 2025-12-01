<?php
require_once 'config/session.php';
requireAdmin(); // Only admins can access this page

require_once 'config/database.php';
require_once 'modules/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $user->username = $_POST['username'];
            $user->password = $_POST['password'];
            $user->role = $_POST['role'];
            if ($user->create()) {
                $message = 'User added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to add user. Username may already exist.';
                $message_type = 'danger';
            }
            break;
        case 'update':
            $user->user_id = $_POST['user_id'];
            $user->username = $_POST['username'];
            $user->password = $_POST['password']; // Can be empty
            $user->role = $_POST['role'];
            if ($user->update()) {
                $message = 'User updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to update user.';
                $message_type = 'danger';
            }
            break;
        case 'delete':
            $user->user_id = $_POST['user_id'];
            if ($user->delete()) {
                $message = 'User deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete user.';
                $message_type = 'danger';
            }
            break;
    }
}

$users = $user->readAll();
$current_user = getCurrentUser();

function renderUsers() {
    global $users, $message, $message_type, $current_user;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BPLO Ilagan - User Accounts</title>
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
                                <i class="mdi mdi-account-multiple"></i>
                            </span> User Account Management
                        </h3>
                        <button class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="mdi mdi-plus"></i> Add User
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
                                    <h4 class="card-title">All Users</h4>
                                    
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
                                                    <th>Username</th>
                                                    <th>Role</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $counter = 1;
                                                while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                                                ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td><span class="badge badge-<?php echo $row['role'] == 'Admin' ? 'danger' : 'info'; ?>"><?php echo $row['role']; ?></span></td>
                                                    <td><?php echo date('Y/m/d', strtotime($row['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-gradient-info" onclick='editUser(<?php echo json_encode($row); ?>)'><i class="mdi mdi-pencil"></i></button>
                                                        <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                                        <button class="btn btn-sm btn-gradient-danger" onclick="deleteUser(<?php echo $row['user_id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')"><i class="mdi mdi-delete"></i></button>
                                                        <?php endif; ?>
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
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role" id="edit_role" required>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="user_id" id="delete_user_id">
    </form>
    
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/misc.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_user_id').value = user.user_id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_password').value = '';
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }
        
        function deleteUser(id, username) {
            if (confirm('Are you sure you want to delete user "' + username + '"?')) {
                document.getElementById('delete_user_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>

<?php
}

renderUsers();
?>
