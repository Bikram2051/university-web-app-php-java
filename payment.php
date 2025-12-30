<?php
session_start();

// Check if we have enquiry data
if (!isset($_SESSION['enquiry_data'])) {
    header("Location: enquire.php");
    exit();
}

$enquiry_data = $_SESSION['enquiry_data'];
$errors = isset($_SESSION['payment_errors']) ? $_SESSION['payment_errors'] : array();

// Calculate total cost
$product_prices = array(
    'aurora' => 2499.00,
    'nexus' => 1599.00,
    'essence' => 699.00
);

$base_price = isset($product_prices[$enquiry_data['product']]) ? $product_prices[$enquiry_data['product']] : 0;
$quantity = intval($enquiry_data['quantity']);
$features_cost = 0;

if (isset($enquiry_data['features'])) {
    $feature_prices = array(
        '4k_upscaling' => 200,
        'wireless_rear' => 150,
        'smart_hub' => 100,
        'extended_warranty' => 300,
        'professional_calibration' => 250
    );
    
    foreach ((array)$enquiry_data['features'] as $feature) {
        if (isset($feature_prices[$feature])) {
            $features_cost += $feature_prices[$feature];
        }
    }
}

$total_cost = ($base_price + $features_cost) * $quantity;
?>
<?php include 'header.inc'; ?>
        <h1>Complete Your Purchase</h1>

                <?php if (!empty($errors)): ?>
            <div class="error-container" style="display: block;">
                <div class="error-message">
                    <h4>Please correct the following errors:</h4>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <section id="order-summary" class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-content">
                <div class="customer-details">
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($enquiry_data['firstname'] . ' ' . $enquiry_data['lastname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($enquiry_data['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($enquiry_data['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($enquiry_data['street'] . ', ' . $enquiry_data['suburb'] . ', ' . $enquiry_data['state'] . ' ' . $enquiry_data['postcode']); ?></p>
                    <p><strong>Contact Preference:</strong> <?php echo ucfirst($enquiry_data['contact_method']); ?></p>
                </div>
                <div class="order-details">
                    <h3>Order Details</h3>
                    <p><strong>Product:</strong> <?php echo ucfirst($enquiry_data['product']); ?> Series</p>
                    <p><strong>Quantity:</strong> <?php echo $enquiry_data['quantity']; ?></p>
                    <div class="selected-features">
                        <strong>Selected Features:</strong>
                        <ul>
                            <?php if (isset($enquiry_data['features']) && !empty($enquiry_data['features'])): ?>
                                <?php 
                                $feature_names = array(
                                    '4k_upscaling' => '4K Upscaling',
                                    'wireless_rear' => 'Wireless Rear Speakers',
                                    'smart_hub' => 'Smart Hub Integration',
                                    'extended_warranty' => 'Extended Warranty',
                                    'professional_calibration' => 'Professional Calibration'
                                );
                                foreach ((array)$enquiry_data['features'] as $feature): ?>
                                    <li><?php echo isset($feature_names[$feature]) ? $feature_names[$feature] : $feature; ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No additional features selected</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="price-summary">
                    <h3>Price Summary</h3>
                    <p><strong>Base Price:</strong> $<?php echo number_format($base_price, 2); ?></p>
                    <p><strong>Additional Features:</strong> $<?php echo number_format($features_cost, 2); ?></p>
                    <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                    <p class="total-amount"><strong>Total Amount:</strong> $<?php echo number_format($total_cost, 2); ?></p>
                </div>
            </div>
        </section>

        <form id="payment-form" action="process_order.php" method="post" novalidate>
            <fieldset class="payment-info">
                <legend>Payment Information</legend>

                                <div class="form-group">
                    <label for="card-type">Credit Card Type:</label>
                    <select id="card-type" name="card_type" required>
                        <option value="">Please select</option>
                        <option value="visa" <?php echo (isset($_POST['card_type']) && $_POST['card_type'] == 'visa') ? 'selected' : ''; ?>>Visa</option>
                        <option value="mastercard" <?php echo (isset($_POST['card_type']) && $_POST['card_type'] == 'mastercard') ? 'selected' : ''; ?>>MasterCard</option>
                        <option value="amex" <?php echo (isset($_POST['card_type']) && $_POST['card_type'] == 'amex') ? 'selected' : ''; ?>>American Express</option>
                    </select>
                </div>

                 <div class="form-group">
                    <label for="card-name">Name on Card:</label>
                    <input type="text" id="card-name" name="card_name" maxlength="40" 
                           value="<?php echo isset($_POST['card_name']) ? htmlspecialchars($_POST['card_name']) : ''; ?>" required>
                                
                  <div class="form-group">
                    <label for="card-number">Credit Card Number:</label>
                    <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" 
                           value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : ''; ?>" required>
                </div>
                

                       <div class="form-group">
                        <label for="card-expiry">Expiry Date (MM-YY):</label>
                        <input type="text" id="card-expiry" name="card_expiry" placeholder="MM-YY" maxlength="5" 
                         value="<?php echo isset($_POST['card_expiry']) ? htmlspecialchars($_POST['card_expiry']) : ''; ?>" required>
                    </div>


                      <div class="form-group">
                        <label for="card-cvv">CVV:</label>
                        <input type="text" id="card-cvv" name="card_cvv" maxlength="3" placeholder="123" 
                               value="<?php echo isset($_POST['card_cvv']) ? htmlspecialchars($_POST['card_cvv']) : ''; ?>" required>
                    </div>

            </fieldset>

            <div class="form-submit">
                <button type="button" id="cancel-order" class="reset-button">Cancel Order</button>
                <button type="submit" id="check-out-button" class="submit-button">Complete Order</button>
            </div>
        </form>

        <script>
            document.getElementById('cancel-order').addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel this order?')) {
                    window.location.href = 'enquire.php';
                }
            });
        </script>
<?php 
// Clear payment errors after displaying - but only if we're showing them
if (!empty($errors)) {
    unset($_SESSION['payment_errors']);
}
include 'footer.inc'; 
?>