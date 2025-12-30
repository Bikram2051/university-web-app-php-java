<?php
session_start();
if (!isset($_SESSION['enquiry_data']) || !isset($_POST['card_type'])) {
    header("Location: enquire.php");
    exit();
}

$errors = [];
$enquiry_data = $_SESSION['enquiry_data'];
$payment_data = $_POST;

// Server-side validation
function sanitize_input($data) {
    return htmlspecialchars(trim(stripslashes($data)));
}

// Validate personal information
if (empty($enquiry_data['firstname']) || !preg_match('/^[A-Za-z]{1,25}$/', $enquiry_data['firstname'])) {
    $errors[] = "First name must be alphabetical and max 25 characters";
}

if (empty($enquiry_data['lastname']) || !preg_match('/^[A-Za-z]{1,25}$/', $enquiry_data['lastname'])) {
    $errors[] = "Last name must be alphabetical and max 25 characters";
}

if (empty($enquiry_data['email']) || !filter_var($enquiry_data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email address is required";
}

// Validate address
if (empty($enquiry_data['street']) || strlen($enquiry_data['street']) > 40) {
    $errors[] = "Street address is required and max 40 characters";
}

// Validate state and postcode
$state_postcodes = [
    'VIC' => ['3', '8'],
    'NSW' => ['1', '2'],
    'QLD' => ['4', '9'],
    'NT' => ['0'],
    'WA' => ['6'],
    'SA' => ['5'],
    'TAS' => ['7'],
    'ACT' => ['0']
];

if (isset($state_postcodes[$enquiry_data['state']])) {
    $valid_postcodes = $state_postcodes[$enquiry_data['state']];
    if (!in_array(substr($enquiry_data['postcode'], 0, 1), $valid_postcodes)) {
        $errors[] = "Postcode does not match selected state";
    }
}

// Validate credit card
if (empty($payment_data['card_type'])) {
    $errors[] = "Credit card type is required";
}

// Card number validation based on type
$card_number = preg_replace('/\s+/', '', $payment_data['card_number']);
switch ($payment_data['card_type']) {
    case 'visa':
        if (!preg_match('/^4\d{15}$/', $card_number)) {
            $errors[] = "Visa card must have 16 digits starting with 4";
        }
        break;
    case 'mastercard':
        if (!preg_match('/^5[1-5]\d{14}$/', $card_number)) {
            $errors[] = "MasterCard must have 16 digits starting with 51-55";
        }
        break;
    case 'amex':
        if (!preg_match('/^3[47]\d{13}$/', $card_number)) {
            $errors[] = "American Express must have 15 digits starting with 34 or 37";
        }
        break;
}



// Validate card expiry date
if (empty($payment_data['card_expiry']) || !preg_match('/^\d{2}-\d{2}$/', $payment_data['card_expiry'])) {
    $errors[] = "Expiry date must be in MM-YY format (e.g., 12-25)";
} else {
    // Check if card is expired
    $expiry_parts = explode('-', $payment_data['card_expiry']);
    $expiry_month = intval($expiry_parts[0]);
    $expiry_year = intval($expiry_parts[1]);
    $current_year = intval(date('y'));
    $current_month = intval(date('m'));
    
    if ($expiry_month < 1 || $expiry_month > 12) {
        $errors[] = "Expiry month must be between 01 and 12";
    } elseif ($expiry_year < $current_year || ($expiry_year == $current_year && $expiry_month < $current_month)) {
        $errors[] = "Credit card has expired";
    }
}

// Validate card name (letters and spaces only)
if (empty($payment_data['card_name']) || !preg_match('/^[A-Za-z\s]+$/', $payment_data['card_name'])) {
    $errors[] = "Name on card can only contain letters and spaces";
} elseif (strlen($payment_data['card_name']) > 40) {
    $errors[] = "Name on card cannot exceed 40 characters";
}

// Validate CVV (exactly 3 digits)
if (empty($payment_data['card_cvv']) || !preg_match('/^\d{3}$/', $payment_data['card_cvv'])) {
    $errors[] = "CVV must be exactly 3 digits";
}
// If errors, redirect back to payment page
if (!empty($errors)) {
    $_SESSION['payment_errors'] = $errors;
    header("Location: payment.php");
    exit();
}

// Calculate total cost
$product_prices = [
    'aurora' => 2499.00,
    'nexus' => 1599.00,
    'essence' => 699.00
];

$base_price = $product_prices[$enquiry_data['product']];
$quantity = intval($enquiry_data['quantity']);
$features_cost = 0;

if (isset($enquiry_data['features'])) {
    $feature_prices = [
        '4k_upscaling' => 200,
        'wireless_rear' => 150,
        'smart_hub' => 100,
        'extended_warranty' => 300,
        'professional_calibration' => 250
    ];
    
    foreach ((array)$enquiry_data['features'] as $feature) {
        if (isset($feature_prices[$feature])) {
            $features_cost += $feature_prices[$feature];
        }
    }
}

$total_cost = ($base_price + $features_cost) * $quantity;

// Store order in database
$features_json = isset($enquiry_data['features']) ? json_encode($enquiry_data['features']) : '[]';

$stmt = mysqli_prepare($conn, "INSERT INTO orders (
    firstname, lastname, email, phone, street, suburb, state, postcode, 
    contact_method, product, quantity, features, comments, order_cost,
    card_type, card_name, card_number, card_expiry, card_cvv
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, "sssssssssssisssssss", 
    $enquiry_data['firstname'], $enquiry_data['lastname'], $enquiry_data['email'],
    $enquiry_data['phone'], $enquiry_data['street'], $enquiry_data['suburb'],
    $enquiry_data['state'], $enquiry_data['postcode'], $enquiry_data['contact_method'],
    $enquiry_data['product'], $quantity, $features_json, $enquiry_data['comments'],
    $total_cost, $payment_data['card_type'], $payment_data['card_name'],
    $card_number, $payment_data['card_expiry'], $payment_data['card_cvv']
);

if (mysqli_stmt_execute($stmt)) {
    $order_id = mysqli_insert_id($conn);
    $_SESSION['order_id'] = $order_id;
    $_SESSION['order_cost'] = $total_cost;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Clear session data
    unset($_SESSION['enquiry_data']);
    unset($_SESSION['payment_errors']);
    
    header("Location: receipt.php");
    exit();
} else {
    error_log("Database error in process_order.php: " . mysqli_error($conn));
    $_SESSION['payment_errors'] = ["System error occurred. Please try again."];
    header("Location: payment.php");
    exit();
}
?>