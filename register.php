<?php 
include('navbar.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect required fields
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required!";
        header('Location: register.php');
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header('Location: register.php');
        exit();
    }

    // Prevent SQL Injection
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email is already registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header('Location: register.php');
        exit();
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password) VALUES (NULL, ?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful. Please login.";
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header('Location: register.php');
        exit();
    }

    $stmt->close();
    $conn->close();
}

// ... rest of the HTML code ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FISHEE SHOP</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/register.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="register-section">
        <div class="register-container">
            <div class="register-wrapper">
                <!-- Left Side - Register Form -->
                <div class="register-form-container">
                    <div class="register-header">
                        <h1>Create Account</h1>
                        <p>Join our fishing community today</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert-message error">
                            <i class="bi bi-exclamation-circle"></i>
                            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert-message success">
                            <i class="bi bi-check-circle"></i>
                            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST" class="register-form" id="registerForm" novalidate>
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input" 
                                   required minlength="2" 
                                   pattern="^[A-Za-z\s]{2,}$"
                                   placeholder="Enter your full name"
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            <i class="bi bi-person"></i>
                            <div class="error-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   required
                                   placeholder="Enter your email address"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <i class="bi bi-envelope"></i>
                            <div class="error-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-input-group">
                                <input type="password" id="password" name="password" class="form-input" 
                                       required minlength="8"
                                       placeholder="Create a strong password"
                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}">
                                <i class="bi bi-lock"></i>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-meter">
                                    <div class="strength-meter-fill"></div>
                                </div>
                                <span class="strength-text"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="password-input-group">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                                       required
                                       placeholder="Confirm your password">
                                <i class="bi bi-shield-lock"></i>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="error-feedback"></div>
                        </div>

                        <button type="submit" class="register-button">
                            <i class="bi bi-person-plus"></i>
                            <span>Create Account</span>
                        </button>

                        <div class="login-prompt">
                            <p>Already have an account? <a href="login.php">Sign In</a></p>
                        </div>
                    </form>
                </div>

                <!-- Right Side - Image and Info -->
                <div class="register-image-container">
                    <div class="register-image-content">
                        <img src="assets/images/register.jpg" alt="Fishing Equipment" class="register-image">
                        <div class="image-overlay">
                            <h2>Join Our Community</h2>
                            <p>Get access to exclusive deals and premium fishing equipment</p>
                            <div class="feature-list">
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Premium Quality Products</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Expert Fishing Advice</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Exclusive Member Discounts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script>
        // Password visibility toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentElement.querySelector('.password-toggle i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                numbers: /\d/.test(password),
                special: /[!@#$%^&*]/.test(password),
                length12: password.length >= 12
            };

            strength += checks.length ? 1 : 0;
            strength += (checks.lowercase && checks.uppercase) ? 1 : 0;
            strength += checks.numbers ? 1 : 0;
            strength += checks.special ? 1 : 0;
            strength += checks.length12 ? 1 : 0;

            return {
                score: strength,
                checks: checks
            };
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            // Reset previous error states
            document.querySelectorAll('.error-feedback').forEach(div => {
                div.textContent = '';
                div.classList.remove('show');
            });
            document.querySelectorAll('.form-input').forEach(input => {
                input.classList.remove('error');
            });

            // Validate name
            if (!name.value.trim()) {
                showError(name, 'Name is required');
                isValid = false;
            } else if (name.value.length < 2) {
                showError(name, 'Name must be at least 2 characters long');
                isValid = false;
            } else if (!/^[A-Za-z\s]{2,}$/.test(name.value.trim())) {
                showError(name, 'Name can only contain letters and spaces');
                isValid = false;
            }

            // Validate email
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password
            const passwordCheck = checkPasswordStrength(password.value);
            if (!password.value) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (!passwordCheck.checks.length) {
                showError(password, 'Password must be at least 8 characters long');
                isValid = false;
            } else if (!passwordCheck.checks.lowercase || !passwordCheck.checks.uppercase) {
                showError(password, 'Password must include both uppercase and lowercase letters');
                isValid = false;
            } else if (!passwordCheck.checks.numbers) {
                showError(password, 'Password must include at least one number');
                isValid = false;
            } else if (!passwordCheck.checks.special) {
                showError(password, 'Password must include at least one special character (!@#$%^&*)');
                isValid = false;
            }

            // Validate confirm password
            if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                // Add loading state to button
                const button = this.querySelector('.register-button');
                button.innerHTML = '<i class="bi bi-arrow-repeat spin"></i><span>Creating Account...</span>';
                button.disabled = true;
            }
        });

        function showError(input, message) {
            input.classList.add('error');
            const errorDiv = input.parentElement.querySelector('.error-feedback') || 
                           input.parentElement.parentElement.querySelector('.error-feedback');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
            }
        }

        // Real-time password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const result = checkPasswordStrength(this.value);
            const meter = document.querySelector('.strength-meter-fill');
            const text = document.querySelector('.strength-text');

            // Remove previous classes
            meter.classList.remove('strength-weak', 'strength-medium', 'strength-strong');

            if (this.value.length === 0) {
                meter.style.width = '0';
                text.textContent = '';
            } else if (result.score < 2) {
                meter.style.width = '33.33%';
                meter.classList.add('strength-weak');
                text.textContent = 'Weak password';
                text.style.color = 'var(--error-color)';
            } else if (result.score < 4) {
                meter.style.width = '66.66%';
                meter.classList.add('strength-medium');
                text.textContent = 'Medium password';
                text.style.color = 'var(--warning-color)';
            } else {
                meter.style.width = '100%';
                meter.classList.add('strength-strong');
                text.textContent = 'Strong password';
                text.style.color = 'var(--success-color)';
            }
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
