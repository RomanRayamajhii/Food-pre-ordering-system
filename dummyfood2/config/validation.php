<?php
function validateUsername($username) {
    return strlen($username) >= 3 && strlen($username) <= 50 && preg_match('/^[a-zA-Z0-9_]+$/', $username);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= 6;
}

function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s()]{8,20}$/', $phone);
}
?> 