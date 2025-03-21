<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const urlParams = new URLSearchParams(window.location.search);
    let category = urlParams.get('category') || 'all';

    function loadItems(page) {
        const itemsGrid = document.getElementById('items-grid');
        const paginationElement = document.getElementById('pagination');
        itemsGrid.innerHTML = '<div class="loading">Loading items...</div>';

        fetch(`fetchitems.php?category=${encodeURIComponent(category)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);

                // Display items
                if (!data.items || data.items.length === 0) {
                    itemsGrid.innerHTML = '<div class="no-items">No items found in this category.</div>';
                } else {
                    itemsGrid.innerHTML = data.items.map(item => `
                        <div class="item-card">
                            <div class="item-image-container">
                                <img src="${item.image}" alt="${item.title}" class="item-image">
                            </div>
                            <div class="item-details">
                                <h3 class="item-title">${item.title}</h3>
                                <p class="item-description">${item.description}</p>
                                <div class="item-meta">
                                    <div class="item-price">₹${parseFloat(item.price).toFixed(2)}</div>
                                    
                                </div>
                                <div class="item-actions">
                                    <form method="POST" action="cart.php" class="d-inline">
                                        <input type="hidden" name="id" value="${item.id}">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-success">Add to Cart</button>
                                    </form>
                                    <a href="cart.php?action=buy&id=${item.id}" class="btn btn-primary">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                // Update pagination
                updatePagination(data.pagination);
                updateURL(page);
            })
            .catch(error => {
                console.error('Error:', error);
                itemsGrid.innerHTML = `<div class="error">Error loading items: ${error.message}</div>`;
            });
    }

    function updatePagination(pagination) {
        const paginationElement = document.getElementById('pagination');
        if (!pagination || pagination.total_pages <= 1) {
            paginationElement.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <button class="pagination-btn ${pagination.current_page === 1 ? 'disabled' : ''}" 
                ${pagination.current_page === 1 ? 'disabled' : ''}
                onclick="loadItems(${pagination.current_page - 1})">
                <i class="fas fa-chevron-left"></i> Previous
            </button>`;

        const maxVisiblePages = 5;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(pagination.total_pages, startPage + maxVisiblePages - 1);

        if (startPage > 1) {
            paginationHTML += `<button class="pagination-btn" onclick="loadItems(1)">1</button>
            ${startPage > 2 ? '<span class="pagination-ellipsis">...</span>' : ''}`;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `<button class="pagination-btn ${i === pagination.current_page ? 'active' : ''}"
                onclick="loadItems(${i})">${i}</button>`;
        }

        if (endPage < pagination.total_pages) {
            paginationHTML += `${endPage < pagination.total_pages - 1 ? '<span class="pagination-ellipsis">...</span>' : ''}
            <button class="pagination-btn" onclick="loadItems(${pagination.total_pages})">${pagination.total_pages}</button>`;
        }

        paginationHTML += `
            <button class="pagination-btn ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}"
                ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}
                onclick="loadItems(${pagination.current_page + 1})">
                Next <i class="fas fa-chevron-right"></i>
            </button>`;

        paginationElement.innerHTML = paginationHTML;
    }

    function updateURL(page) {
        const newURL = `?category=${encodeURIComponent(category)}&page=${page}`;
        history.pushState({ page: page }, '', newURL);
    }

    window.loadItems = loadItems;

    loadItems(currentPage);
});
</script>

<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FishShop - Browse Items</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/items.css">
    <link rel="stylesheet" href="styles/footer.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="items-container">
            <div id="items-grid" class="items-grid">
                <!-- Items will be loaded here dynamically -->
            </div>
            <div id="pagination" class="pagination">
                <!-- Pagination will be loaded here dynamically -->
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
