<?php
session_start();

// Check if accessed directly
if (!isset($_SESSION['order_id'])) {
    header("Location: enquire.php");
    exit();
}

require_once 'settings.php';

$order_id = $_SESSION['order_id'];
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: enquire.php");
    exit();
}

include 'header.inc';
?>

        <h1>Order Confirmation</h1>
        
        <div class="receipt-details">
            <div class="receipt-section">
                <h2>Thank You for Your Order!</h2>
                <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
                <p><strong>Order Status:</strong> <?php echo $order['order_status']; ?></p>
                <p><strong>Order Date:</strong> <?php echo $order['order_time']; ?></p>
            </div>

            <div class="receipt-section">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['street'] . ', ' . $order['suburb'] . ', ' . $order['state'] . ' ' . $order['postcode']); ?></p>
            </div>

            <div class="receipt-section">
                <h3>Order Details</h3>
                <p><strong>Product:</strong> <?php echo ucfirst($order['product']); ?> Series</p>
                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                
                <?php if ($order['features'] && $order['features'] != '[]'): ?>
                <h4>Selected Features:</h4>
                <ul>
                    <?php 
                    $features = json_decode($order['features'], true);
                    $feature_names = [
                        '4k_upscaling' => '4K Upscaling',
                        'wireless_rear' => 'Wireless Rear Speakers',
                        'smart_hub' => 'Smart Hub Integration',
                        'extended_warranty' => 'Extended Warranty',
                        'professional_calibration' => 'Professional Calibration'
                    ];
                    foreach ($features as $feature): ?>
                        <li><?php echo $feature_names[$feature]; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>

            <div class="receipt-section">
                <h3>Payment Summary</h3>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['order_cost'], 2); ?></p>
                <p><strong>Payment Method:</strong> <?php echo strtoupper($order['card_type']); ?> ending in <?php echo substr($order['card_number'], -4); ?></p>
            </div>
        </div>

        <div class="receipt-actions">
            <a href="index.php" class="cta-button">Return to Home</a>
            <a href="product.php" class="cta-button">Continue Shopping</a>
        </div>

<?php 
// Clear order session after display
unset($_SESSION['order_id']);
unset($_SESSION['order_cost']);
include 'footer.inc';
?>