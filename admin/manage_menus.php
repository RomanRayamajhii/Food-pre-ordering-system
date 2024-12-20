<?php
session_start();
include 'includes/header.php';
include 'includes/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch menu items with their categories using the view we created
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Menu Items</h2>
            <button type="button" class="btn btn-info" onclick="toggleDebug()">
                <i class="fas fa-bug"></i> Debug Info
            </button>
        </div>
        
        <div id="debugInfo" style="display: none;">
            <?php
            function getDebugInfo() {
                // Server Information
                $debug_info = "<div style='background: #f8f9fa; padding: 10px; margin: 10px; border-radius: 5px; font-family: monospace;'>";
                $debug_info .= "<h5><strong>Debug Information:</strong></h5>";
                $debug_info .= "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
                $debug_info .= "<strong>Upload Directory:</strong> " . $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2/uploads/menu/' . "<br><br>";
                
                // Check upload directory
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/dummyfood2/uploads/menu/';
                if (is_dir($upload_dir)) {
                    $debug_info .= "<strong>Files in upload directory:</strong><br>";
                    $files = scandir($upload_dir);
                    foreach ($files as $file) {
                        if ($file != "." && $file != "..") {
                            $debug_info .= "- " . $file . " (size: " . filesize($upload_dir . $file) . " bytes)<br>";
                        }
                    }
                } else {
                    $debug_info .= "<span style='color: red;'>Upload directory does not exist!</span><br>";
                }

                // Database Records
                $debug_info .= "<br><strong>Menu Items with Images:</strong><br>";
                global $conn;
                $query = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $file_exists = file_exists($upload_dir . $row['image']);
                        $status = $file_exists ? "✅" : "❌";
                        $debug_info .= "{$status} ID: {$row['id']} - {$row['name']} (Image: {$row['image']})<br>";
                    }
                } else {
                    $debug_info .= "<span style='color: gray;'>No menu items with images found</span><br>";
                }
                
                $debug_info .= "</div>";
                return $debug_info;
            }

            echo getDebugInfo();
            ?>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Add New Item Button -->
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addItemModal">
            <i class="fas fa-plus"></i> Add New Item
        </button>

        <!-- Menu Items Table -->
        <table class="table table-bordered">
            <thead class="thead-dark">
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
                <tr <?php echo $row['status'] == 0 ? 'class="table-secondary"' : ''; ?>>
                    <td>
                        <?php 
                        $image_path = !empty($row['image']) ? "../uploads/menu/" . $row['image'] : "../assets/images/default-food.jpg";
                        $display_path = !empty($row['image']) ? "/dummyfood2/uploads/menu/" . $row['image'] : "/dummyfood2/assets/images/default-food.jpg";
                        ?>
                        <img src="<?php echo htmlspecialchars($display_path); ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>" 
                             style="width: 50px; height: 50px; object-fit: cover;"
                             onerror="this.src='/dummyfood2/assets/images/default-food.jpg'">
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td>Rs. <?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $row['status'] ? 'success' : 'danger'; ?>">
                            <?php echo $row['status'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if ($row['status'] == 1): ?>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-ban"></i>
                        </button>
                        <?php else: ?>
                        <button class="btn btn-sm btn-success restore-btn" data-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-redo"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu Item</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="menu_actions.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="">
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
                            <label>New Image</label>
                            <input type="file" class="form-control-file" name="image">
                            <small class="form-text text-muted">Leave empty to keep current image</small>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status">
                                <label class="custom-control-label" for="statusSwitch">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Debug Modal -->
    <div class="modal fade" id="debugModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Debug Information</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    // Server Information
                    echo "<h6 class='text-primary'>Server Information:</h6>";
                    echo "<div class='ml-3 mb-3'>";
                    echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
                    echo "<strong>Script Path:</strong> " . __FILE__ . "<br>";
                    echo "<strong>Upload Directory:</strong> " . realpath("../uploads/menu") . "<br>";
                    echo "</div>";
                    // Upload Directory Status
                    $upload_dir = "../uploads/menu/";
                    echo "<h6 class='text-primary'>Upload Directory Status:</h6>";
                    echo "<div class='ml-3 mb-3'>";
                    echo "<strong>Directory Exists:</strong> " . (file_exists($upload_dir) ? "Yes" : "No") . "<br>";
                    if (file_exists($upload_dir)) {
                        echo "<strong>Permissions:</strong> " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
                        echo "<strong>Writable:</strong> " . (is_writable($upload_dir) ? "Yes" : "No") . "<br>";
                    }
                    echo "</div>";
                    // Files in Upload Directory
                    echo "<h6 class='text-primary'>Files in Upload Directory:</h6>";
                    echo "<div class='ml-3 mb-3'>";
                    if (is_dir($upload_dir)) {
                        $files = scandir($upload_dir);
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-sm table-bordered'>";
                        echo "<thead><tr><th>File</th><th>Size</th><th>Permissions</th><th>Preview</th></tr></thead>";
                        echo "<tbody>";
                        foreach ($files as $file) {
                            if ($file != "." && $file != "..") {
                                $file_path = $upload_dir . $file;
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($file) . "</td>";
                                echo "<td>" . filesize($file_path) . " bytes</td>";
                                echo "<td>" . substr(sprintf('%o', fileperms($file_path)), -4) . "</td>";
                                echo "<td><img src='/dummyfood2/uploads/menu/" . htmlspecialchars($file) . "' 
                                          style='height: 30px; width: 30px; object-fit: cover;'
                                          onerror=\"this.src='/dummyfood2/assets/images/default-food.jpg'\"></td>";
                                echo "</tr>";
                            }
                        }
                        echo "</tbody></table></div>";
                    } else {
                        echo "<div class='text-danger'>Directory not accessible</div>";
                    }
                    echo "</div>";
                    // Database Records
                    echo "<h6 class='text-primary'>Menu Items with Images:</h6>";
                    echo "<div class='ml-3 mb-3'>";
                    $query = "SELECT id, name, image FROM menu_items WHERE image IS NOT NULL";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-sm table-bordered'>";
                        echo "<thead><tr><th>ID</th><th>Name</th><th>Image Name</th><th>File Exists</th></tr></thead>";
                        echo "<tbody>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['image']) . "</td>";
                            echo "<td>" . (file_exists($upload_dir . $row['image']) ? 
                                  "<span class='text-success'>Yes</span>" : 
                                  "<span class='text-danger'>No</span>") . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                    } else {
                        echo "<div class='text-muted'>No menu items with images found</div>";
                    }
                    echo "</div>";
                    // PHP Configuration
                    echo "<h6 class='text-primary'>PHP Upload Configuration:</h6>";
                    echo "<div class='ml-3'>";
                    echo "<strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "<br>";
                    echo "<strong>post_max_size:</strong> " . ini_get('post_max_size') . "<br>";
                    echo "<strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "<br>";
                    echo "</div>";
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> </div>
        </div></div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle Edit Button Click
        $('.edit-btn').click(function() {
            var id = $(this).data('id');
            // Fetch item details via AJAX
            $.ajax({
                url: 'get_menu_item.php',
                type: 'GET',
                data: {id: id},
                success: function(response) {
                    var item = JSON.parse(response);
                    // Populate edit modal with item details
                    $('#editItemModal').find('input[name="id"]').val(item.id);
                    $('#editItemModal').find('input[name="name"]').val(item.name);
                    $('#editItemModal').find('select[name="category_id"]').val(item.category_id);
                    $('#editItemModal').find('input[name="price"]').val(item.price);
                    $('#editItemModal').find('textarea[name="description"]').val(item.description);
                    $('#editItemModal').find('input[name="status"]').prop('checked', item.status == 1);
                    $('#editItemModal').modal('show');
                },
                error: function() {
                    alert('Error fetching item details.');
                }
            });
        });
        // Handle Delete Button Click
        $('.delete-btn').click(function() {
            if (confirm('Are you sure you want to delete this item?')) {
                var id = $(this).data('id');
                var form = $('<form action="menu_actions.php" method="post">' +
                            '<input type="hidden" name="action" value="delete">' +
                            '<input type="hidden" name="id" value="' + id + '">' +
                            '</form>');
                $('body').append(form);
                form.submit();
            }
        });
        // Handle Restore Button Click
        $('.restore-btn').click(function() {
            if (confirm('Are you sure you want to restore this item?')) {
                var id = $(this).data('id');
                var form = $('<form action="menu_actions.php" method="post">' +
                            '<input type="hidden" name="action" value="restore">' +
                            '<input type="hidden" name="id" value="' + id + '">' +
                            '</form>');
                $('body').append(form);
                form.submit();
            }
        });

        $('#addItemForm').on('submit', function(e) {
            var fileInput = $('input[name="image"]');
            var file = fileInput[0].files[0];
            
            if (file && file.size > 2 * 1024 * 1024) { // 2MB limit
                e.preventDefault();
                alert('File size too large. Please select an image under 2MB.');
                return false;
            }
        });
    });
    </script>

    <!-- Add this div for debug information -->
    <div id="debugInfo" style="display: none;" class="debug-panel">
    </div>

    <!-- Add this JavaScript at the bottom of your file -->
    <script>
    document.getElementById('debugBtn').addEventListener('click', function() {
        fetch('get_debug_info.php')
            .then(response => response.text())
            .then(data => {
                const debugPanel = document.getElementById('debugInfo');
                debugPanel.innerHTML = data;
                debugPanel.style.display = debugPanel.style.display === 'none' ? 'block' : 'none';
            })
            .catch(error => console.error('Error:', error));
    });
    </script>

    <!-- Add this CSS -->
    <style>
    .debug-panel {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
        font-family: monospace;
    }
    </style>

    <style>
    #debugInfo {
        margin-bottom: 20px;
    }
    #debugInfo pre {
        margin: 0;
        white-space: pre-wrap;
    }
    </style>

    <script>
    function toggleDebug() {
        var debugDiv = document.getElementById('debugInfo');
        debugDiv.style.display = debugDiv.style.display === 'none' ? 'block' : 'none';
    }
    </script>

    <!-- Add New Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add New Menu Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                            <label>Image</label>
                            <input type="file" class="form-control-file" name="image" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" checked>
                                <label class="custom-control-label" for="statusSwitch">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 
