<?php
session_start();
require_once 'controllers/AuthController.php';

$auth = new AuthController();
if($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

// Load the view
include 'views/login.html'; 