<?php
// search_suggestions.php
include 'config.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$like = "%{$q}%";

// Проверка дали има is_promo колона
$check_sql = "SHOW COLUMNS FROM books LIKE 'is_promo'";
$check_result = $conn->query($check_sql);
$has_promo_column = $check_result && $check_result->num_rows > 0;

if ($has_promo_column) {
    $sql = "
        SELECT 
            id,
            title,
            author,
            price,
            image,
            'book' AS type,
            is_promo
        FROM books
        WHERE (title LIKE ? OR author LIKE ?)
        
        UNION ALL
        
        SELECT
            id,
            title,
            author,
            price,
            cover_image AS image,
            'upcoming' AS type,
            0 AS is_promo
        FROM upcoming_books
        WHERE (title LIKE ? OR author LIKE ?) AND status = 'upcoming'
        
        ORDER BY 
            CASE 
                WHEN is_promo = 1 THEN 0  -- Промо книги първи
                ELSE 1
            END,
            title ASC
        LIMIT 15
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    // Ако няма is_promo колона, използвай стария SQL
    $sql = "
        SELECT 
            id,
            title,
            author,
            price,
            image,
            'book' AS type
        FROM books
        WHERE title LIKE ? OR author LIKE ?
        
        UNION ALL
        
        SELECT
            id,
            title,
            author,
            price,
            cover_image AS image,
            'upcoming' AS type
        FROM upcoming_books
        WHERE (title LIKE ? OR author LIKE ?) AND status = 'upcoming'
        
        LIMIT 12
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $like, $like, $like, $like);
}

$stmt->execute();
$res = $stmt->get_result();

$data = [];

while ($row = $res->fetch_assoc()) {
    $price_display = number_format($row['price'], 2) . ' лв.';
    
    // Ако книгата е промо, добави "(Промо)" към цената
    if (isset($row['is_promo']) && $row['is_promo'] == 1) {
        $price_display .= ' <span style="color:#e60000; font-weight:bold;">(Промо)</span>';
    }
    
    $data[] = [
        'id'     => (int)$row['id'],
        'title'  => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
        'author' => htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'),
        'price'  => $price_display,
        'image'  => $row['image'] ?: 'https://via.placeholder.com/40x55',
        'type'   => $row['type'],
        'is_promo' => isset($row['is_promo']) ? $row['is_promo'] : 0,
        'url'    => $row['type'] === 'book'
            ? "book.php?id=" . $row['id']
            : "upcomingbooks-details.php?id=" . $row['id']
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);

$conn->close();
?>