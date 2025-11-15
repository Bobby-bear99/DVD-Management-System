<?php
header('Content-Type: application/json');
include 'db.php';

try {
    $stmt = $conn->prepare("
        SELECT id, title, language, genre, year, price, stock, image_path 
        FROM dvds 
        ORDER BY id DESC
    ");
    $stmt->execute();

    $dvds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dvds, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>

