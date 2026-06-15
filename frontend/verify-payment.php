<?php
include('config/constants.php');
include('config/blocked-check.php');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$utr = isset($_GET['utr']) ? trim($_GET['utr']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($order_id <= 0 || empty($utr) || $status !== 'success') {
    echo "<script>alert('Payment verification failed.'); window.location.href = 'mycart.php';</script>";
    exit();
}

$tran_id = "UPI: UTR: " . $utr;

// Update order_manager
$update_order = "UPDATE `order_manager` SET payment_status = 'upi', transaction_id = '$tran_id' WHERE id = $order_id AND payment_status = 'pending_upi'";
if (mysqli_query($conn, $update_order)) {
    // Update aamarpay
    $update_aamar = "UPDATE `aamarpay` SET status = 'upi', transaction_id = '$tran_id' WHERE order_id = $order_id";
    mysqli_query($conn, $update_aamar);

    // Deduct stock
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $value) {
            $item_name = mysqli_real_escape_string($conn, $value['Item_Name']);
            $quantity = (int)$value['Quantity'];
            
            $update_quantity_query = "UPDATE `tbl_food` SET stock = stock - $quantity WHERE title = '$item_name'";
            mysqli_query($conn, $update_quantity_query);
            
            $update_quantity = "UPDATE `tbl_restro_food_item` SET stock = stock - $quantity WHERE title = '$item_name'";
            mysqli_query($conn, $update_quantity);
        }
        // Clear the cart
        unset($_SESSION['cart']);
    } else {
        // Fallback: If session cart is empty, pull from online_orders_new
        $query_items = "SELECT Item_Name, Quantity FROM online_orders_new WHERE order_id = $order_id";
        $res_items = mysqli_query($conn, $query_items);
        if ($res_items && mysqli_num_rows($res_items) > 0) {
            while ($row = mysqli_fetch_assoc($res_items)) {
                $item_name = mysqli_real_escape_string($conn, $row['Item_Name']);
                $quantity = (int)$row['Quantity'];
                $update_quantity_query = "UPDATE `tbl_food` SET stock = stock - $quantity WHERE title = '$item_name'";
                mysqli_query($conn, $update_quantity_query);
                $update_quantity = "UPDATE `tbl_restro_food_item` SET stock = stock - $quantity WHERE title = '$item_name'";
                mysqli_query($conn, $update_quantity);
            }
        }
    }

    echo "<script>alert('Payment verified and Order placed successfully!'); window.location.href = 'view-orders.php';</script>";
} else {
    echo "<script>alert('Failed to update order status.'); window.location.href = 'mycart.php';</script>";
}
?>
