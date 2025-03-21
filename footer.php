<?php
$current_year = date('Y');
?>

<footer class="footer">
    <!-- External CSS -->
    <link rel="stylesheet" href="styles/footer.css">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <div class="footer-container">
        <!-- About Section -->
        <div class="footer-section">
            <a href="index.php" class="footer-logo">
                <i class="bi bi-water"></i>
                FISHEE SHOP
            </a>
            <p class="footer-description">
                Your trusted online marketplace for premium fishing equipment. Discover quality gear for every fishing enthusiast.
            </p>
            <div class="footer-social">
                <a href="https://facebook.com" target="_blank" class="social-link" aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://twitter.com" target="_blank" class="social-link" aria-label="Twitter">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="https://instagram.com" target="_blank" class="social-link" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="https://youtube.com" target="_blank" class="social-link" aria-label="YouTube">
                    <i class="bi bi-youtube"></i>
                </a>
            </div>
        </div>

        <!-- Quick Links Section -->
        <div class="footer-section">
            <h3 class="footer-title">Quick Links</h3>
            <ul class="footer-links">
                <li>
                    <a href="about.php" class="footer-link">
                        <i class="bi bi-info-circle"></i>
                        About Us
                    </a>
                </li>
                <li>
                    <a href="products.php" class="footer-link">
                        <i class="bi bi-shop"></i>
                        Our Products
                    </a>
                </li>
                <li>
                    <a href="privacy.php" class="footer-link">
                        <i class="bi bi-shield-check"></i>
                        Privacy Policy
                    </a>
                </li>
                <li>
                    <a href="terms.php" class="footer-link">
                        <i class="bi bi-file-text"></i>
                        Terms of Service
                    </a>
                </li>
                <li>
                    <a href="faq.php" class="footer-link">
                        <i class="bi bi-question-circle"></i>
                        FAQ
                    </a>
                </li>
            </ul>
        </div>

        <!-- Contact Section -->
        <div class="footer-section">
            <h3 class="footer-title">Contact Us</h3>
            <div class="footer-contact">
                <div class="contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>123 Fishing Street, Ocean City, FC 12345</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <span>+1 (234) 567-8900</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <span>contact@fisheeshop.com</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-clock"></i>
                    <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                </div>
            </div>
        </div>

        <!-- Newsletter Section -->
        <div class="footer-section">
            <h3 class="footer-title">Newsletter</h3>
            <p class="footer-description">Subscribe to our newsletter for the latest updates and exclusive offers.</p>
            <form class="newsletter-form" action="subscribe.php" method="POST">
                <div class="newsletter-input-group">
                    <input type="email" class="newsletter-input" placeholder="Enter your email" required>
                    <button type="submit" class="newsletter-btn">
                        <i class="bi bi-send"></i>
                        <span>Subscribe</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p>&copy; <?php echo $current_year; ?> FISHEE SHOP. All Rights Reserved.</p>
            <p class="footer-tagline">Fresh From The Ocean</p>
            <div class="footer-bottom-links">
                <a href="privacy.php">Privacy</a>
                <span class="separator">|</span>
                <a href="terms.php">Terms</a>
                <span class="separator">|</span>
                <a href="sitemap.php">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
