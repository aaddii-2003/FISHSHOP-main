<?php 
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch user data
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            header('Location: index.php');
        } else {
            $_SESSION['error'] = "Incorrect password!";
            header('Location: login.php');
        }
    } else {
        $_SESSION['error'] = "No user found with that email!";
        header('Location: login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FISHEE SHOP</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="login-section">
        <div class="login-container">
            <div class="login-wrapper">
                <!-- Left Side - Login Form -->
                <div class="login-form-container">
                    <div class="login-header">
                        <h1>Welcome Back!</h1>
                        <p>Sign in to continue your fishing journey</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert-message error">
                            <i class="bi bi-exclamation-circle"></i>
                            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="login-form">
                        <div class="form-group">
                            <label for="email">
                                <i class="bi bi-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="password">
                                <i class="bi bi-lock"></i>
                                Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" id="password" name="password" class="form-input" required>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember">
                                <span>Remember me</span>
                            </label>
                            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                        </div>

                        <button type="submit" class="login-button">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="register-prompt">
                        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                    </div>
                </div>

                <!-- Right Side - Image and Info -->
                <div class="login-image-container">
                    <div class="login-image-content">
                        <img src="assets/images/login.jpg" alt="Fishing Equipment" class="login-image">
                        <div class="image-overlay">
                            <h2>Premium Fishing Equipment</h2>
                            <p>Discover our wide range of high-quality fishing gear</p>
                            <div class="feature-list">
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Quality Assured Products</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Expert Support</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Fast Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <!-- Scripts -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Add loading animation to login button on form submit
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const button = this.querySelector('.login-button');
            button.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Signing In...';
            button.disabled = true;
        });
    </script>
</body>
</html>
