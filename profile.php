<?php
// Start the session
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Verify current password
    $verify_stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $verify_stmt->bind_param("i", $user_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();
    $current_hash = $result->fetch_assoc()['password'];
    
    if (!password_verify($current_password, $current_hash)) {
        $_SESSION['error'] = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match";
    } elseif (strlen($new_password) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pwd_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_pwd_stmt->bind_param("si", $new_hash, $user_id);
        
        if ($update_pwd_stmt->execute()) {
            $_SESSION['success'] = "Password updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating password: " . $conn->error;
        }
        $update_pwd_stmt->close();
    }
    header('Location: profile.php');
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $country = trim($_POST['country']);
    $zip_code = trim($_POST['zip_code']);
    
    // Update profile picture if uploaded
    $profile_image = $user['profile_image']; // Keep existing image by default
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $target_dir = "assets/images/profiles/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $new_filename = "profile_" . $user_id . "_" . time() . "." . $ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = $target_file;
            }
        }
    }

    // Update user information
    $update_stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, profile_image=?, date_of_birth=?, city=?, state=?, country=?, zip_code=? WHERE user_id=?");
    $update_stmt->bind_param("ssssssssssi", $name, $email, $phone, $address, $profile_image, $date_of_birth, $city, $state, $country, $zip_code, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        // Update session variables
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['error'] = "Error updating profile: " . $conn->error;
    }
    $update_stmt->close();
}

// Fetch user's order history
$orders_stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.id) as total_items,
    SUM(oi.quantity * fi.price) as order_total
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN fishitem fi ON oi.fishitem_id = fi.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FISHEE SHOP</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/profile.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-content">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar-container">
                    <img src="<?php echo $user['profile_image'] ?? 'assets/images/default-avatar.png'; ?>" 
                         alt="Profile Picture" class="profile-avatar">
                    <div class="avatar-overlay">
                        <i class="bi bi-camera"></i>
                        <span>Change Photo</span>
                    </div>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <!-- Profile Stats -->
            <div class="profile-stats">
                <div class="stat-card">
                    <i class="bi bi-bag"></i>
                    <div class="stat-number"><?php echo $orders_result->num_rows; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-heart"></i>
                    <div class="stat-number">0</div>
                    <div class="stat-label">Wishlist Items</div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-star"></i>
                    <div class="stat-number">0</div>
                    <div class="stat-label">Reviews</div>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <i class="bi bi-check-circle"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Sections -->
            <div class="profile-sections">
                <!-- Personal Information -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <i class="bi bi-person"></i>
                        Personal Information
                    </h2>
                    <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="form-group">
                            <label for="profile_image" class="form-label">Profile Picture</label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control" 
                                   accept="image/*" onchange="previewImage(this)">
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   pattern="[0-9]{10,}" title="Please enter a valid phone number">
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                                   value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Street Address</label>
                            <textarea id="address" name="address" class="form-control" 
                                      rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">City</label>
                            <input type="text" id="city" name="city" class="form-control"
                                   value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" id="state" name="state" class="form-control"
                                   value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" id="country" name="country" class="form-control"
                                   value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                            <input type="text" id="zip_code" name="zip_code" class="form-control"
                                   value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="save-button">
                            <i class="bi bi-check2"></i>
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Recent Orders -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <i class="bi bi-clock-history"></i>
                        Recent Orders
                    </h2>
                    <div class="order-history">
                        <?php if ($orders_result->num_rows > 0): ?>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <div class="order-item">
                                    <div class="order-header">
                                        <span class="order-number">#<?php echo $order['order_id']; ?></span>
                                        <span class="order-date">
                                            <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="order-details">
                                        <div class="order-info">
                                            <span class="order-items"><?php echo $order['total_items']; ?> items</span>
                                            <span class="order-total">₱<?php echo number_format($order['order_total'], 2); ?></span>
                                        </div>
                                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-orders">No orders yet</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <i class="bi bi-key"></i>
                        Change Password
                    </h2>
                    <form action="profile.php" method="POST" class="profile-form" id="password-form">
                        <input type="hidden" name="change_password" value="1">

                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control"
                                   pattern=".{8,}" title="Password must be at least 8 characters long" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="save-button">
                            <i class="bi bi-key"></i>
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Preview profile image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-avatar').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Trigger file input when clicking on avatar
        document.querySelector('.avatar-overlay').addEventListener('click', function() {
            document.getElementById('profile_image').click();
        });

        // Form validation
        // Profile form validation
        document.querySelector('.profile-form').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone');
            if (phone.value && !phone.value.match(/^[0-9]{10,}$/)) {
                e.preventDefault();
                alert('Please enter a valid phone number');
            }
        });

        // Password form validation
        document.getElementById('password-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match');
            }
        });
    </script>
</body>
</html>
