<?php
session_start();
include './header.php';
include 'includes/config.php';

// Fetch menu items with their categories
$query = "SELECT 
    m.id,
    m.name,
    m.description,
    m.price,
    m.image,
    m.status,
    c.name as category_name,
    c.id as category_id
    FROM menu_items m
    JOIN categories c ON m.category_id = c.id
    ORDER BY c.name, m.name";
$result = mysqli_query($conn, $query);

// Fetch all categories for the dropdown
$categories_query = "SELECT * FROM categories WHERE status = TRUE";
$categories_result = mysqli_query($conn, $categories_query);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card-header">
            <h2>Manage Menu Items</h2>
        </div>

        <!-- Success and Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Add New Item Button -->
        <button class="btn btn-primary" onclick="openModal('addItemModal')">
   Add New Item
</button>
        <!-- Menu Items Table -->
         <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="<?php echo $row['status'] == 0 ? 'table-secondary' : ''; ?>">
                    <td>
                        <?php 
                        $image_path = !empty($row['image']) ? "../uploads/menu/" . $row['image'] : "../assets/images/default-food.jpg";
                        $display_path = !empty($row['image']) ? "/dummyfood2/food-pre-ordering-system/uploads/menu/" . $row['image'] : "/dummyfood2/food-pre-ordering-system/assets/images/default-food.jpg";
                        ?>
                        <img src="<?php echo htmlspecialchars($display_path); ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>" 
                             class="item-image">
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td>$ <?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <span class="badge <?php echo $row['status'] ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $row['status'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="edit-btn" data-id="<?php echo $row['id']; ?>" onclick="openEditModal(<?php echo $row['id']; ?>)">
                            Edit
                        </button>
                        <?php if ($row['status'] == 1): ?>
                        <button class="delete-btn" data-id="<?php echo $row['id']; ?>" onclick="deleteItem(<?php echo $row['id']; ?>)">
                           Delete
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                        </tbody>
        </table>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal" id="editItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu Item</h5>
                <button type="button" class="close" onclick="closeModal('editItemModal')">&times;</button>
            </div>
            <form action="menu_actions.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editItemId" value="">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" name="name" id="editItemName" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select class="form-control" name="category_id" id="editItemCategory" required>
                            <?php 
                            mysqli_data_seek($categories_result, 0);
                            while ($category = mysqli_fetch_assoc($categories_result)) : 
                            ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="editItemPrice" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="editItemDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="editStatusSwitch" name="status">
                            <label class="custom-control-label" for="editStatusSwitch">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editItemModal')">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Item Modal -->
    <div class="modal" id="addItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Menu Item</h5>
                <button type="button" class="close" onclick="closeModal('addItemModal')">&times;</button>
            </div>
            <form action="add_menu.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select class="form-control" name="category_id" required>
                            <?php 
                            mysqli_data_seek($categories_result, 0);
                            while ($category = mysqli_fetch_assoc($categories_result)) : 
                            ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image *</label>
                        <input type="file" class="form-control-file" name="image" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="statusSwitchAdd" name="status" checked>
                            <label class="custom-control-label" for="statusSwitchAdd">Active</label>
                        </div>
                    </div>
                </div>
            
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addItemModal')">Close</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function openEditModal(id) {
        // Fetch item details via AJAX
        $.ajax({
            url: 'get_menu_item.php',
            type: 'GET',
            data: {id: id},
            success: function(response) {
                var item = JSON.parse(response);
                if (item.error) {
                    alert(item.error); // Handle error if item not found
                } else {
                    // Populate edit modal with item details
                    $('#editItemId').val(item.id);
                    $('#editItemName').val(item.name);
                    $('#editItemCategory').val(item.category_id);
                    $('#editItemPrice').val(item.price);
                    $('#editItemDescription').val(item.description);
                    $('#editStatusSwitch').prop('checked', item.status == 1);
                    openModal('editItemModal');
                }
            },
            error: function() {
                alert('Error fetching item details.');
            }
        });
    }

    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: 'menu_actions.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    id: id
                },
                success: function(response) {
                    location.reload(); // Reloads the page to reflect changes
                },
                error: function() {
                    alert('Error deleting item.');
                }
            });
        }
    }
    </script>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f4f4f4;
    }
    .container {
        width: 95%;
        background: #ffffff;
        margin: 20px auto;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow-x: auto;
    }
    .card-header h2 {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #ddd;
        color: black;
        border-radius: 8px 8px 0 0;
    }
    .btn {
        margin: 5px 0;
        padding: 10px 15px;
        font-size:large;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-primary {
        background-color: rgb(7, 123, 248);
        color: #ffffff;
    }
    .btn-primary:hover {
        opacity: 0.9;
    }
    .alert {
        padding: 10px 20px;
        margin: 10px 0;
        border-radius: 5px;
       
    }
    .alert .close{
        float: right;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;

    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .table-responsive{
        overflow-x: auto;
    }
    .table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
        text-align: left;
    }
    .table th, .table td {
        padding: 10px;
        border: 1px solid #dee2e6;
    }
    .table th {
        background: rgb(101, 101, 101);
        color: #ffffff;
    }
    .table-secondary {
        background-color: #f8f9fa;
    }
    .edit-btn{
        background-color:rgb(255, 189, 47);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 5px 0;


    }
    .delete-btn{
        background-color: #f44336;
        color: white;
        padding: 10px 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 5px 0;
        

    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        align-items: center;
        justify-content: center;

    }
  
    .modal-content {
        background: #ffffff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        width: 80%;
        margin:30px auto;
        border-radius: 8px;
        max-width: 600px;
    }
    .modal-header {
        border-bottom: 1px solid #ddd;
        
        }
    .modal-header, .modal-footer {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;

    }
    .modal-footer .btn-secondary{
        background-color:rgb(9, 183, 15);
        color: #fff;
        border: none;
        cursor: pointer;


    }
    .modal-title {
        font-size: 20px;
    }
    .modal-body {
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-control {
        height: 30px;
    
    }
  
    .form-control, .form-control-file {
        width: 100%;
        font-size: 14px;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }
    .form-group textarea{
        height: 100px;
    }
    .item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
    .badge {
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
    }
    .badge-success {
        background: #28a745;
        color: #ffffff;
    }
    .badge-danger {
        background: #dc3545;
        color: #ffffff;
    }
    .close {
        background: transparent;
        border: none;
        font-size: 25px;
        cursor: pointer;
        font-weight: bold;
    }
</style>
</body>
</html>