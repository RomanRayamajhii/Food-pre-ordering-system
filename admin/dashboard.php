<?php 
session_start();
include 'includes/header.php'; ?>

<div class="container mt-4">
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-users"></i> Manage Users</h5>
                    <p class="card-text flex-grow-1">View and manage user accounts</p>
                    <a href="manage_users.php" class="btn btn-light mt-auto">Go to Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-shopping-cart"></i> Manage Orders</h5>
                    <p class="card-text flex-grow-1">View and manage customer orders</p>
                    <a href="manage_orders.php" class="btn btn-light mt-auto">Go to Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3 h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-utensils"></i> Update Menu</h5>
                    <p class="card-text flex-grow-1">View and manage menu items and categories</p>
                    <a href="manage_menus.php" class="btn btn-light mt-auto">Go to Menu</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
    <div class="card text-white bg-warning mb-3 h-100">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><i class="fas fa-tags"></i> Manage Categories</h5>
            <p class="card-text flex-grow-1">Add, edit, or remove categories for menu items.</p>
            <a href="manage_categories.php" class="btn btn-light mt-auto">Go to Categories</a>
        </div>
    </div>
</div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 