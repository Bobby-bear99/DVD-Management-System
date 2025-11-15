<?php
header('Content-Type: application/json');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dvdId = intval($_POST['dvdId'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $saleDate = $_POST['saleDate'] ?? date('Y-m-d');

        if ($dvdId <= 0) {
            throw new Exception("Please select a DVD.");
        }

        if ($quantity <= 0) {
            throw new Exception("Quantity must be at least 1.");
        }

        if ($price < 0) {
            throw new Exception("Invalid price.");
        }

        $dvdStmt = $conn->prepare("SELECT title, price, stock FROM dvds WHERE id = :id");
        $dvdStmt->bindParam(':id', $dvdId, PDO::PARAM_INT);
        $dvdStmt->execute();
        $dvd = $dvdStmt->fetch();

        if (!$dvd) {
            throw new Exception("Selected DVD not found.");
        }

        if ($dvd['stock'] < $quantity) {
            throw new Exception("Insufficient stock. Available: " . $dvd['stock']);
        }

        $calculatedTotal = $price * $quantity;
        if (abs($calculatedTotal - $total) > 0.01) {
            $total = $calculatedTotal;
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO sales (dvd_id, quantity, price, total, sale_date)
                                VALUES (:dvd_id, :quantity, :price, :total, :sale_date)");

        $stmt->bindParam(':dvd_id', $dvdId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':sale_date', $saleDate);
        $stmt->execute();

        $updateStock = $conn->prepare("UPDATE dvds SET stock = stock - :quantity WHERE id = :id");
        $updateStock->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $updateStock->bindParam(':id', $dvdId, PDO::PARAM_INT);
        $updateStock->execute();

        $conn->commit();

        echo json_encode([
            "success" => true, 
            "message" => "Sale recorded successfully!",
            "id" => $conn->lastInsertId()
        ]);
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
