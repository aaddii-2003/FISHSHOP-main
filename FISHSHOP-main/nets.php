<?php
// Include the navbar
include('navbar.php');

// Database connection (Modify with your actual database credentials)
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    <title>FishingMart - Fishing item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="category-title">Fishing Nets</h2>

        <!-- Success Message -->
        <?php
        if (isset($_GET['success']) && $_GET['success'] == '1') {
            echo '<div class="alert alert-success">fishingitem added to cart successfully!</div>';
        }
        ?>

        <div class="row">
            <?php
            // Query to fetch fishingitems in Fiction category
            $sql = "SELECT * FROM fishitem WHERE category = 'Nets'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($fishingitem = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-3">
                        <div class="card fishingitem-card">
                            <img src="<?php echo $fishingitem['image_url']; ?>" class="card-img-top" alt="<?php echo $fishingitem['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $fishingitem['title']; ?></h5>
                                <p class="card-text"><?php echo $fishingitem['seller_name']; ?></p>
                                <p class="fishingitem-price">Rs.<?php echo $fishingitem['price']; ?></p>
                                <div class="d-flex justify-content-between">
                                    <a href="viewdetail.php?id=<?php echo $fishingitem['id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="id" value="<?php echo $fishingitem['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                                    </form>
                                </div>
                                <!-- Updated Buy Now button -->
                                <button class="btn btn-primary btn-sm mt-2" onclick="buyNow(<?php echo $fishingitem['id']; ?>)">Buy Now</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No fishingitems available in this category.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script>
        function buyNow(fishingitemId) {
            // Send the Buy Now request to cart.php with a 'buy' action
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=buy&id=${fishingitemId}`,
            })
            .then(response => {
                // Redirect to the order page after placing the order
                window.location.href = 'order.php';
            })
            .catch(error => {
                console.error('Error placing the order:', error);
                alert('Failed to place the order. Try again!');
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
