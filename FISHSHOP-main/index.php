<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/footer.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Custom Styles for Categories Header */
        .categories-header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <!-- Categories Header Section -->
    <div class="categories-header">
        <h2>Categories</h2>
    </div>

    <!-- Categories Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/fishingrod.png" alt="Fiction" height="300px" width="300px"> 
                    <h3><a href="fishingrod.php">Fishing Rods</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/fishingreel.jpg" alt="Non-Fiction" height="300px" width="300px">
                    <h3><a href="non-fiction.html">Fishing Reels</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/fishingbait.jpg" alt="Business">
                    <h3><a href="fishingitem.php">Fishing Baits</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/spear.jpg" alt="Romance">
                    <h3><a href="spear.php">Spears</a></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/neta.jpg" alt="Motivation">
                    <h3><a href="nets.php">Nets</a></h3>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/fishingtraps.jpg" alt="Discipline">
                    <h3><a href="discipline.html">Traps</a></h3>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="col-md-4">
                <div class="card">
                    <img src="assets/images/selfimprovement.jpeg" alt="Self Improvement">
                    <h3><a href="self-improvement.html">Self Improvement</a></h3>
                </div>
            </div> 
    -->
    <!-- Include Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php include('footer.php'); ?>
