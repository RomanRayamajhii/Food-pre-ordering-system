<?php
$order_id = $_GET['order_id'];
$amount   = $_GET['amount'];
$paypal_email = "businesstest44@gmail.com";

$return_url = "http://localhost/dummyfood2/Food-pre-ordering-system/paypal_success.php?order_id=$order_id";
$cancel_url = "http://localhost/dummyfood2/Food-pre-ordering-system/order_history.php";
?>
<form id="paypalForm" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="item_name" value="Order #<?php echo $order_id; ?>">
<input type="hidden" name="amount" value="<?php echo $amount; ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="return" value="<?php echo $return_url; ?>">
<input type="hidden" name="cancel_return" value="<?php echo $cancel_url; ?>">
</form>
<script>document.getElementById('paypalForm').submit();</script>
