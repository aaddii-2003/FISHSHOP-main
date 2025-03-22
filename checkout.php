<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Force JSON response
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey("sk_test_51QzXw3I8s1DzTM0541sVuaJbtjbT4ToapeydrL2jmBGA5W6C1BOend9BWMXiVr83ajTQZQSbn1gOMv9mkyxjun5100ntBsMC8X");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request! No POST data received."]);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['price'])) {
    echo json_encode(["error" => "Missing ID or Price."]);
    exit;
}

// Validate and sanitize inputs
$fishingitemId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);

if ($fishingitemId === false || $price === false || $price <= 0) {
    echo json_encode(["error" => "Invalid ID or Price format."]);
    exit;
}

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'inr',
                'product_data' => [
                    'name' => "Fishing Item #$fishingitemId",
                ],
                'unit_amount' => intval($price * 100), // Convert to cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => "http://localhost/fishshop/success.php?session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => "http://localhost/fishshop/cancel.php",
    ]);

    echo json_encode(["url" => $checkout_session->url]); // Return JSON response
} catch (Exception $e) {
    echo json_encode(["error" => "Stripe error: " . $e->getMessage()]);
}
?>
