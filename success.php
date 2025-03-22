<?php
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey("sk_test_51QzXw3I8s1DzTM0541sVuaJbtjbT4ToapeydrL2jmBGA5W6C1BOend9BWMXiVr83ajTQZQSbn1gOMv9mkyxjun5100ntBsMC8X");

if (isset($_GET['session_id'])) {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

    if ($session->payment_status === 'paid') {
        echo "<h2>Payment Successful!</h2>";
        echo "<p>Thank you for your purchase.</p>";
        // ✅ Here, you can insert the order into your database
    } else {
        echo "<h2>Payment Not Completed</h2>";
    }
} else {
    echo "Invalid session.";
}
?>
