<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "You need to log in first to process your order.";
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
// Check if we have order data
if (!isset($_SESSION['order_data'])) {
    header('Location: order.php');
    exit();
}

// Get order data from session
$order_data = $_SESSION['order_data'];

    // Get user information
    $user_id = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $order_data['full_name']);
    $phone = mysqli_real_escape_string($conn, $order_data['phone']);
    $address = mysqli_real_escape_string($conn, $order_data['address']);
    $city = mysqli_real_escape_string($conn, $order_data['city']);
    $postal_code = mysqli_real_escape_string($conn, $order_data['postal_code']);
    $payment_method = isset($order_data['payment_method']) ? mysqli_real_escape_string($conn, $order_data['payment_method']) : 'cod';
    
    // Calculate total amount
    $total_amount = 0;
    $order_items = [];
    
    // Check if there are items to process
if ((isset($_SESSION['orders']) && !empty($_SESSION['orders'])) || (isset($_SESSION['cart']) && !empty($_SESSION['cart']))) {
    // Determine which session to use (direct purchase or cart)
    $items_to_process = isset($_SESSION['orders']) ? $_SESSION['orders'] : $_SESSION['cart'];
        $itemids = implode(',', array_keys($items_to_process));
        $sql = "SELECT * FROM fishitem WHERE id IN ($itemids)";
        $result = $conn->query($sql);
        
        while ($item = $result->fetch_assoc()) {
            $quantity = $items_to_process[$item['id']];
            $subtotal = $item['price'] * $quantity;
            $total_amount += $subtotal;
            $order_items[] = [
                'item_id' => $item['id'],
                'quantity' => $quantity,
                'price' => $item['price'],
                'subtotal' => $subtotal
            ];
        }
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order record
        $order_sql = "INSERT INTO orders (user_id,name, phone, address, 
                      Payment_mode, total_price,status, created_at) 
                      VALUES (?,?,?,?,?,?, 'pending', NOW())";
        
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("issssd", $user_id, $full_name, $phone, $address, $payment_method, $total_amount);
        $stmt->execute();
        
        $order_id = $conn->insert_id;
        
        // Insert order items
        $item_sql = "INSERT INTO order_items (order_id, fishitem_id, quantity, price) 
                     VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($item_sql);
        
        foreach ($order_items as $item) {
            $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], 
                             $item['price']);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear the orders session
        unset($_SESSION['orders']);
        
        // Set success message
        $_SESSION['success'] = "Order placed successfully! Your order ID is " . $order_id;
        
        // Redirect to order confirmation page
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        $_SESSION['error'] = "Error processing your order. Please try again.";
        header("Location: order.php");
        exit();
    }


// If not POST request, redirect back to order page
header("Location: order.php");
exit();
?>