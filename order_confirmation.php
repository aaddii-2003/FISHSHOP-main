<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "You need to log in first to view this page.";
    header('Location: login.php');
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details
$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Prepare and execute query to get order details
$stmt = $conn->prepare("SELECT o.*, oi.fishitem_id, oi.quantity, oi.price, f.name as item_name 
FROM orders o 
JOIN order_items oi ON o.order_id = oi.order_id 
JOIN fishitem f ON oi.fishitem_id = f.id 
WHERE o.order_id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Order not found or unauthorized access.";
    header('Location: index.php');
    exit();
}

// Get order information
$order_items = [];
$order_info = null;

while ($row = $result->fetch_assoc()) {
    if ($order_info === null) {
        $order_info = [
            'order_id' => $row['order_id'],
            'full_name' => $row['full_name'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'city' => $row['city'],
            'postal_code' => $row['postal_code'],
            'payment_method' => $row['payment_method'],
            'total_amount' => $row['total_amount'],
            'order_status' => $row['order_status'],
            'created_at' => $row['created_at']
        ];
    }
    
    $order_items[] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'subtotal' => $row['subtotal']
    ];
}

// Include header
include('navbar.php');
?>

<div class="container mt-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Order Confirmation - Order #<?php echo $order_info['order_id']; ?></h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Shipping Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order_info['full_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_info['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order_info['address']); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($order_info['city']); ?></p>
                    <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order_info['postal_code']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order_info['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order_info['payment_method']); ?></p>
                    <p><strong>Order Status:</strong> <?php echo ucfirst($order_info['order_status']); ?></p>
                </div>
            </div>

            <h5>Order Items</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                        <td><strong>$<?php echo number_format($order_info['total_amount'], 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>