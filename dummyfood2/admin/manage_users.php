<?php 
include 'includes/header.php';
include 'includes/config.php';

// Check for success messages
$success_msg = '';
if(isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'blocked': $success_msg = 'User has been blocked successfully!'; break;
        case 'unblocked': $success_msg = 'User has been unblocked successfully!'; break;
        case 'deleted': $success_msg = 'User has been deleted successfully!'; break;
    }
}

// Fetch users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Manage Users</h3>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                            Add New User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($success_msg): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $success_msg; ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td>
                                        <?php if($row['status'] == 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Blocked</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if($row['status'] == 'active'): ?>
                                                <a href="user_actions.php?action=block&id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-warning btn-sm"
                                                   onclick="return confirm('Are you sure you want to block this user?')">
                                                    Block
                                                </a>
                                            <?php else: ?>
                                                <a href="user_actions.php?action=unblock&id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('Are you sure you want to unblock this user?')">
                                                    Unblock
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="user_actions.php?action=delete&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">
                                                Delete
                                            </a>
                                        </div>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="user_actions.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="user_actions.php" method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle edit user button click
    $('.edit-user').click(function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var address = $(this).data('address');

        $('#edit_user_id').val(id);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit_address').val(address);
    });
});
</script>

<?php include 'includes/footer.php'; ?> 