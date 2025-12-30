<?php
session_start();
require_once 'settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    // Check if order is pending
    $check_stmt = mysqli_prepare($conn, "SELECT order_status FROM orders WHERE order_id = ?");
    mysqli_stmt_bind_param($check_stmt, "i", $order_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $status);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);
    
    if ($status == 'PENDING') {
        $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        
        if (mysqli_stmt_execute($stmt)) {
    $_SESSION['message'] = "Order cancelled successfully";
} else {
    error_log("Error cancelling order in cancel_order.php: " . mysqli_error($conn));
    $_SESSION['error'] = "Error cancelling order";
}
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Only pending orders can be cancelled";
    }
}

mysqli_close($conn);
header("Location: manager.php");
exit();
?>