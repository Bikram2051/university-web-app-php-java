<?php
session_start();
require_once 'settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $_POST['order_status'];
    
    $valid_statuses = ['PENDING', 'FULFILLED', 'PAID', 'ARCHIVED'];
    
    if (in_array($order_status, $valid_statuses)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET order_status = ? WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $order_status, $order_id);
        
        if (mysqli_stmt_execute($stmt)) {
    $_SESSION['message'] = "Order status updated successfully";
} else {
    error_log("Error updating order status in update_order.php: " . mysqli_error($conn));
    $_SESSION['error'] = "Error updating order status";
}
        
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
header("Location: manager.php");
exit();
?>