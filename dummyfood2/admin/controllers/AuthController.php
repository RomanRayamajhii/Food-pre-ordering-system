<?php
class AuthController {
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']);
    }
    
    public function getError() {
        return isset($_GET['error']) ? true : false;
    }
} 