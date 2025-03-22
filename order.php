<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "You need to log in first to view this page.";
    header('Location: login.php');  // Redirect to login page
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information for pre-filling the form
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Initialize orders array
$orders = [];
$is_direct_purchase = isset($_SESSION['direct_purchase']) && $_SESSION['direct_purchase'];

// Check if there are any orders
if (!isset($_SESSION['orders']) || empty($_SESSION['orders'])) {
    // Don't redirect, let the page handle the empty state
    $orders = [];
} else {
    // Fetch the orders
    if (isset($_SESSION['orders']) && count($_SESSION['orders']) > 0) {
        if ($is_direct_purchase) {
            // For direct purchase, we have a single item ID
            $fishingitemid = $_SESSION['orders'][0];
            $sql = "SELECT * FROM fishitem WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fishingitemid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $row['quantity'] = 1; // Direct purchase always has quantity 1
                    $orders[] = $row;
                }
            }
        } else {
            // For cart purchases, handle multiple items with quantities
            $items = [];
            foreach ($_SESSION['orders'] as $itemId => $quantity) {
                $items[] = intval($itemId);
            }
            $fishingitemid = implode(',', $items);
            
            $sql = "SELECT * FROM fishitem WHERE id IN ($fishingitemid)";
            $result = $conn->query($sql);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $row['quantity'] = $_SESSION['orders'][$row['id']];
                    $orders[] = $row;
                }
            }
        }
    }
}

// Handle cancel action
if (isset($_POST['cancel_order'])) {
    // Clear both orders and order initiation flag
    unset($_SESSION['orders']);
    unset($_SESSION['order_initiated']);
    unset($_SESSION['direct_purchase']);
    $_SESSION['message'] = "Order canceled successfully!";
    header('Location: items.php');
    exit();
}

// Calculate total price
$total_price = 0;
foreach ($orders as $order) {
    $total_price += $order['price'] * $order['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['cancel_order'])) {
    // Validate form data
    $errors = [];
    $required_fields = ['full_name', 'phone', 'address', 'city', 'postal_code', 'payment_method'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . " is required";
        }
    }
    
    // Validate phone number format
    if (!empty($_POST['phone']) && !preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
        $errors[] = "Phone number must be 10 digits";
    }
    
    // Validate postal code format
    if (!empty($_POST['postal_code']) && !preg_match('/^[0-9]{5}$/', $_POST['postal_code'])) {
        $errors[] = "Postal code must be 5 digits";
    }

    if (empty($errors)) {
        // Store order data in session
        $_SESSION['order_data'] = [
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'postal_code' => $_POST['postal_code'],
            'payment_method' => $_POST['payment_method'] ?? 'cod'
        ];
        
        // Redirect to process_order.php
        header('Location: process_order.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FishMart</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/order.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4">Checkout</h2>

        <?php if (!empty($orders)): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Shipping Information</h3>
                            
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form id="shipping-form" method="POST" action="process_order.php">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone (10 digits)</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code (5 digits)</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" pattern="[0-9]{5}" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                        <label class="form-check-label" for="cod">Cash on Delivery</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                        <label class="form-check-label" for="card">Credit/Debit Card</label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Order Summary</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['title']; ?></td>
                                            <td><?php echo $order['quantity']; ?></td>
                                            <td class="text-end">Rs. <?php echo $order['price'] * $order['quantity']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-end">Rs. <?php echo $total_price; ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <button type="submit" form="shipping-form" class="btn btn-primary w-100">Place Order</button>
                            
                            <div class="mt-3">
                                <form method="POST" action="order.php" style="display:inline;">
                                    <input type="hidden" name="cancel_order" value="1">
                                    <button type="submit" class="btn btn-outline-danger w-100">Cancel Order</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">No Orders Found!</h4>
                <p>You currently have no items in your order. Please browse our collection and add items to your cart or use Buy Now to place an order.</p>
                <hr>
                <p class="mb-0">
                    <a href="index.php" class="btn btn-primary">Browse Items</a>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <?php 
    include('footer.php');
    // Close the database connection
    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<div class="container mt-5">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Order Details</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method">
                        <option value="cod">Cash on Delivery</option>
                        <option value="card">Credit/Debit Card</option>
                    </select>
                </div>
                <button class="btn btn-primary" onclick="buyNow(${item.id}, ${item.price})">Buy Now</button>
                <button type="submit" name="cancel_order" class="btn btn-danger">Cancel Order</button>
            </form>
        </div>
    </div>
</div>

<script>
    function buyNow(itemId, price) {
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                id: itemId,
                price: price
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                window.location.href = data.url; // Redirect to Stripe checkout
            } else {
                alert('Error: ' + (data.error || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong!');
        });
    }
</script>