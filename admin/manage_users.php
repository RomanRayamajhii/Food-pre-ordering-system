<?php 
include './header.php';
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .card-header {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert button {
            background: transparent;
            border: none;
            font-size: 20px;
            line-height: 1;
            float: right;
            cursor: pointer;
        }
.table-content {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th, .table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #f1f1f1;
        }
        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 2px;
            text-decoration: none;
            font-size: 0.9em;
            border-radius: 5px;
            text-align: center;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: 1px solid #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: 1px solid #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            border: 1px solid #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            color: #fff;
            display: inline-block;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Manage Users</h3>
            </div>
            <?php if($success_msg): ?>
                <div class="alert">
                    <?php echo $success_msg; ?>
                    <button onclick="remove()">&times;</button>
                </div>
            <?php endif; ?>
            <div class="table-content">
                <table class="table">
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
                                <a href="user_actions.php?action=block&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-warning"
                                   onclick="return confirm('Are you sure you want to block this user?')">
                                    Block
                                </a>
                                <a href="user_actions.php?action=unblock&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-success"
                                   onclick="return confirm('Are you sure you want to unblock this user?')">
                                    Unblock
                                </a>
                                <a href="user_actions.php?action=delete&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
    let rows = document.querySelectorAll('.table tbody tr');

    rows.forEach(function(row) {
        let badge = row.querySelector('.badge-success');
        let badge1 = row.querySelector('.badge-danger');
        let block = row.querySelector('.btn-warning');
        let unblock = row.querySelector('.btn-success');

 
        if (badge) {
            block.style.display = 'inline-block'; 
            unblock.style.display = 'none'; 
        }
        if (badge1) {
            unblock.style.display = 'inline-block'; 
            block.style.display = 'none';  
    }
});
function remove() {
        let alert = document.querySelector('.alert');
   
            alert.style.display = 'none';
        
    }

</script>

</body>
</html>
