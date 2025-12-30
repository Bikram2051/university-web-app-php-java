<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: enquire.php");
    exit();
}

$errors = [];
$form_data = $_POST;

// Sanitize all inputs
foreach ($form_data as $key => $value) {
    if (is_array($value)) {
        $form_data[$key] = array_map(function($item) {
            return htmlspecialchars(trim(stripslashes($item)));
        }, $value);
    } else {
        $form_data[$key] = htmlspecialchars(trim(stripslashes($value)));
    }
}

// Validation rules
if (empty($form_data['firstname']) || !preg_match('/^[A-Za-z]{1,25}$/', $form_data['firstname'])) {
    $errors[] = "First name must be alphabetical and max 25 characters";
}

if (empty($form_data['lastname']) || !preg_match('/^[A-Za-z]{1,25}$/', $form_data['lastname'])) {
    $errors[] = "Last name must be alphabetical and max 25 characters";
}

if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email address is required";
}

if (empty($form_data['phone']) || !preg_match('/^\d{10}$/', preg_replace('/\D/', '', $form_data['phone']))) {
    $errors[] = "Phone number must be exactly 10 digits";
}

if (empty($form_data['street']) || strlen($form_data['street']) > 40) {
    $errors[] = "Street address is required and max 40 characters";
}

if (empty($form_data['suburb']) || strlen($form_data['suburb']) > 20) {
    $errors[] = "Suburb is required and max 20 characters";
}

if (empty($form_data['state'])) {
    $errors[] = "State is required";
}

if (empty($form_data['postcode']) || !preg_match('/^\d{4}$/', $form_data['postcode'])) {
    $errors[] = "Postcode must be exactly 4 digits";
}

// State-postcode validation
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

if (isset($state_postcodes[$form_data['state']])) {
    $valid_postcodes = $state_postcodes[$form_data['state']];
    if (!in_array(substr($form_data['postcode'], 0, 1), $valid_postcodes)) {
        $errors[] = "Postcode does not match selected state";
    }
}

if (empty($form_data['contact_method'])) {
    $errors[] = "Please select a preferred contact method";
}

if (empty($form_data['product'])) {
    $errors[] = "Please select a product";
}

if (empty($form_data['quantity']) || !filter_var($form_data['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    $errors[] = "Quantity must be a positive integer";
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $form_data;
    header("Location: enquire.php");
    exit();
}

// Store valid data in session and proceed to payment
$_SESSION['enquiry_data'] = $form_data;
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);

header("Location: payment.php");
exit();
?>