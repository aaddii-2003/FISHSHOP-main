<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Ensure proper error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishmart');
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get category and page from request
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Prepare the base query
$where_clause = $category !== 'all' ? 'WHERE category = ?' : '';
$count_sql = "SELECT COUNT(*) as total FROM fishitem " . $where_clause;
$items_sql = "SELECT * FROM fishitem " . $where_clause . " LIMIT ? OFFSET ?";

// Get total items count
try {
    $count_stmt = $conn->prepare($count_sql);
    if ($category !== 'all') {
        $count_stmt->bind_param('s', $category);
    }
    $count_stmt->execute();
    $total_result = $count_stmt->get_result()->fetch_assoc();
    $total_items = $total_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Get items for current page
    $items_stmt = $conn->prepare($items_sql);
    if ($category !== 'all') {
        $items_stmt->bind_param('sii', $category, $items_per_page, $offset);
    } else {
        $items_stmt->bind_param('ii', $items_per_page, $offset);
    }
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();

    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = [
            'id' => $item['id'],
            'title' => htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'),
            'price' => floatval($item['price']),
            'stock' => intval($item['stock']),
            'image' => htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8')
        ];
    }

    // Return JSON response
    echo json_encode([
        'items' => $items,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'items_per_page' => $items_per_page,
            'total_items' => $total_items
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();