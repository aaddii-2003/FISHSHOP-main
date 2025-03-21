<?php
session_start();

// Database connection (Modify with your actual database credentials)
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle actions (add, remove, buy)
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $itemid = $_POST['id'];

    if ($action == 'add') {
        // Check if the cart is initialized
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or update the item quantity in cart
        if (!isset($_SESSION['cart'][$itemid])) {
            $_SESSION['cart'][$itemid] = 1;
            $_SESSION['message'] = "Item added to cart successfully!";
        } else {
            $_SESSION['cart'][$itemid]++;
            $_SESSION['message'] = "Item quantity updated in cart!";
        }
    }

    if ($action == 'remove') {
        // Remove item from the cart
        if (isset($_SESSION['cart'][$itemid])) {
            unset($_SESSION['cart'][$itemid]);
            $_SESSION['message'] = "Item removed from cart successfully!";
        }
    }

    if ($action == 'buy') {
        // Set up direct purchase session variables
        $_SESSION['orders'] = [$itemid];
        $_SESSION['direct_purchase'] = true;

        // Redirect to the order page for direct purchase
        header("Location: order.php");
        exit();
    }
}

// Handle quantity updates
if (isset($_POST['action']) && $_POST['action'] == 'update_quantity') {
    $itemid = $_POST['id'];
    $quantity = max(1, min(10, intval($_POST['quantity'])));
    
    if (isset($_SESSION['cart'][$itemid])) {
        $_SESSION['cart'][$itemid] = $quantity;
        $_SESSION['message'] = "Quantity updated successfully!";
    }
}

// Handle "Proceed to Order" logic for moving items from cart to orders
if (isset($_POST['action']) && $_POST['action'] == 'proceed_to_order') {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        // Add all cart items to orders session with their quantities
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = [];
        }

        foreach ($_SESSION['cart'] as $itemid => $quantity) {
            $_SESSION['orders'][$itemid] = $quantity;
        }

        // Clear the cart after transferring the items
        unset($_SESSION['cart']);
        $_SESSION['direct_purchase'] = false;

        // Redirect to the order page
        header("Location: order.php");
        exit();
    }
}

// Fetch cart items
$cart_items = [];
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $itemids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM fishitem WHERE id IN ($itemids)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/fiction.css"> <!-- Custom CSS for category page -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - fishMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Your Cart</h2>

        <!-- Display Cart Books -->
        <?php if (count($cart_items) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Seller Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_price = 0;
                        foreach ($cart_items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total_price += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo $item['title']; ?></td>
                                <td><?php echo $item['seller_name']; ?></td>
                                <td>Rs. <?php echo $item['price']; ?></td>
                                <td>
                                    <form method="POST" action="cart.php" class="d-flex align-items-center">
                                        <input type="hidden" name="action" value="update_quantity">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="10" class="form-control form-control-sm" style="width: 70px;"
                                               onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>Rs. <?php echo $subtotal; ?></td>
                                <td>
                                    <!-- Remove Button -->
                                    <form method="POST" action="cart.php" style="display:inline;">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Total Price -->
                <div class="d-flex justify-content-end">
                    <h4>Total: Rs. <?php echo $total_price; ?></h4>
                </div>

                <!-- Checkout Button -->
                <div class="d-flex justify-content-between mt-3">
                    <!-- Proceed to Order Button -->
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="action" value="proceed_to_order">
                        <button type="submit" class="btn btn-success btn-lg">Proceed to Order</button>
                    </form>
                    <a href="index.php" class="btn btn-secondary btn-lg">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <p>Your cart is empty! <a href="index.php">Browse Item</a> and add some.</p>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
