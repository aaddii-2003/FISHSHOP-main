<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "You need to log in first to view this page.";
    header('Location: login.php');  // Redirect to login page
    exit();
}

// Database connection (Modify with your actual database credentials)
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the orders
$orders = [];
if (isset($_SESSION['orders']) && count($_SESSION['orders']) > 0) {
    $fishingitemid = implode(',', array_map('intval', $_SESSION['orders']));
    
    // Ensure the SQL query works even for one or multiple IDs
    $sql = "SELECT * FROM fishitem WHERE id IN ($fishingitemid)";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    } else {
        echo "Error fetching orders: " . $conn->error;
    }
}

// Handle cancel action
if (isset($_POST['cancel_order'])) {
    $book_id = $_POST['id'];
    if (($key = array_search($book_id, $_SESSION['orders'])) !== false) {
        unset($_SESSION['orders'][$key]);
        $_SESSION['message'] = "Order canceled successfully!";
    }
    header("Location: order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/order.css"> <!-- Custom CSS for order page -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders - fishMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Your Orders</h2>

        <!-- Display Orders -->
        <?php if (count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>seller Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_price = 0;
                        foreach ($orders as $book):
                            $total_price += $book['price'];
                        ?>
                            <tr>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['seller_name']; ?></td>
                                <td>Rs. <?php echo $book['price']; ?></td>
                                <td>
                                    <!-- Cancel Order Button -->
                                    <form method="POST" action="order.php" style="display:inline;">
                                        <input type="hidden" name="cancel_order" value="1">
                                        <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
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
            </div>
        <?php else: ?>
            <p>You have no orders placed yet. <a href="fiction.php">Browse books</a> and place an order.</p>
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
