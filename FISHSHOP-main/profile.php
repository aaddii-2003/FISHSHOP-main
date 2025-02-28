<?php
// Start the session
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Online Fish Shop</title>
    <link rel="stylesheet" href="styles/navbar.css"> <!-- Assuming your shared navbar CSS -->
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/profile.css"> <!-- Assuming you have the profile-specific CSS -->
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Profile Content -->
    <div class="profile-wrapper">
        <div class="profile-header">
            <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
        </div>

        <div class="profile-info">
            <h2>Your Profile</h2>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
            </div>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
