<?php
// pg/process.php
// This file acts as the backend logic of our Custom Payment Gateway.
// In a real gateway, this would verify the payment with the bank and then call a webhook.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    $utr = isset($_POST['utr']) ? trim($_POST['utr']) : '';

    if (empty($order_id) || empty($utr)) {
        die("Invalid request parameters.");
    }

    // In our mock system, we assume the UTR is valid if it's not empty.
    // We redirect the user back to the verification page (callback).
    $callback_url = "../verify-payment.php?order_id=" . urlencode($order_id) . "&utr=" . urlencode($utr) . "&status=success";

    header("Location: $callback_url");
    exit();
} else {
    die("Invalid request method.");
}
?>
