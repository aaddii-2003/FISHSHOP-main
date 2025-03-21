<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FishShop - Professional Fishing Equipment</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/footer.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Professional Fishing Equipment</h1>
            <p class="hero-description">Discover our premium selection of fishing gear for both beginners and professionals. Quality equipment for your perfect catch.</p>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="categories-header">
            <h2 class="categories-title">Explore Our Categories</h2>
            <p class="categories-description">Browse through our carefully curated collection of professional fishing equipment</p>
        </div>

        <div class="categories-grid">
            <!-- Fishing Rods -->
            <div class="category-card">
                <a href="items.php?category=FishingRods" class="category-link" data-category="FishingRods">
                    <div class="category-image-container">
                        <img src="assets/images/fishingrod.png" alt="Fishing Rods" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Fishing Rods</h3>
                    </div>
                </a>
            </div>

            <!-- Fishing Reels -->
            <div class="category-card">
                <a href="items.php?category=FishingReels" class="category-link" data-category="FishingReels">
                    <div class="category-image-container">
                        <img src="assets/images/fishingreel.jpg" alt="Fishing Reels" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Fishing Reels</h3>
                    </div>
                </a>
            </div>

            <!-- Fishing Baits -->
            <div class="category-card">
                <a href="items.php?category=FishingBaits" class="category-link" data-category="FishingBaits">
                    <div class="category-image-container">
                        <img src="assets/images/fishingbait.jpg" alt="Fishing Baits" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Fishing Baits</h3>
                    </div>
                </a>
            </div>

            <!-- Spears -->
            <div class="category-card">
                <a href="items.php?category=spears" class="category-link" data-category="spears">
                    <div class="category-image-container">
                        <img src="assets/images/spear.jpg" alt="Spears" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Spears</h3>
                    </div>
                </a>
            </div>

            <!-- Nets -->
            <div class="category-card">
                <a href="items.php?category=nets" class="category-link" data-category="nets">
                    <div class="category-image-container">
                        <img src="assets/images/neta.jpg" alt="Nets" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Nets</h3>
                    </div>
                </a>
            </div>

            <!-- Traps -->
            <div class="category-card">
                <a href="items.php?category=traps" class="category-link" data-category="traps">
                    <div class="category-image-container">
                        <img src="assets/images/fishingtraps.jpg" alt="Traps" class="category-image">
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Traps</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading class to simulate loading states
            const cards = document.querySelectorAll('.category-card');
            cards.forEach(card => {
                card.classList.add('loading');
                setTimeout(() => {  
                    card.classList.remove('loading');
                }, 1000);
            });

            // Add click event listeners to category links
            document.querySelectorAll('.category-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const category = this.getAttribute('data-category');
                    window.location.href = `items.php?category=${category}`;
                });
            });
        });
    </script>
</body>
</html>
