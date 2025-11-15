<?php
header('Content-Type: application/json');
include 'db.php';

try {
    $dateFrom = $_GET['dateFrom'] ?? null;
    $dateTo = $_GET['dateTo'] ?? null;

    $query = "SELECT s.id, s.dvd_id, s.quantity, s.price, s.total, s.sale_date, d.title as dvd_title
              FROM sales s
              INNER JOIN dvds d ON s.dvd_id = d.id";
    
    $conditions = [];
    $params = [];

    if ($dateFrom) {
        $conditions[] = "s.sale_date >= :dateFrom";
        $params[':dateFrom'] = $dateFrom;
    }

    if ($dateTo) {
        $conditions[] = "s.sale_date <= :dateTo";
        $params[':dateTo'] = $dateTo;
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY s.sale_date DESC, s.id DESC";

    $stmt = $conn->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sales, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
