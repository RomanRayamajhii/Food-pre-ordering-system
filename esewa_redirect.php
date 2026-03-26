<?php
session_start();
include 'config/db.php';

$order_id = $_GET['order_id'] ?? null;
$amount   = $_GET['amount'] ?? null;

if(!$order_id || !$amount){
    header("Location: menu.php");
    exit();
}

// eSewa sandbox credentials
$merchant_code = 'EPAYTEST';
$success_url = 'http://localhost/dummyfood2/Food-pre-ordering-system/esewa_success.php';
$failure_url = 'http://localhost/dummyfood2/Food-pre-ordering-system/esewa_failed.php?order_id=' . $order_id;
$amount = round($amount, 2);
// eSewa PID needs to be unique for every request in sandbox
$pid = "ORDER_" . $order_id;

$secret_key = "8gBm/:&EnhH.1/q";
$data = "total_amount=$amount,transaction_uuid=$pid,product_code=$merchant_code";
$signature = base64_encode(hash_hmac('sha256', $data, $secret_key, true));
?>
<form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
    <input type="hidden" name="tax_amount" value="0">
    <input type="hidden" name="total_amount" value="<?php echo $amount; ?>">
    <input type="hidden" name="transaction_uuid" value="<?php echo $pid; ?>">
    <input type="hidden" name="product_code" value="<?php echo $merchant_code; ?>">
    <input type="hidden" name="product_service_charge" value="0">
    <input type="hidden" name="product_delivery_charge" value="0">
    <input type="hidden" name="success_url" value="<?php echo $success_url; ?>">
    <input type="hidden" name="failure_url" value="<?php echo $failure_url; ?>">
    <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
    <input type="hidden" name="signature" value="<?php echo $signature; ?>">
</form>
<script>
document.getElementById('esewaForm').submit();</script>