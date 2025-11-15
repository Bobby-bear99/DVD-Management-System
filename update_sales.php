<?php
header('Content-Type: application/json');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $saleId = intval($_POST['saleId'] ?? 0);
        $dvdId = intval($_POST['dvdId'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $saleDate = $_POST['saleDate'] ?? date('Y-m-d');

        if ($saleId <= 0) {
            throw new Exception("Invalid sale ID.");
        }

        if ($dvdId <= 0) {
            throw new Exception("Please select a DVD.");
        }

        if ($quantity <= 0) {
            throw new Exception("Quantity must be at least 1.");
        }

        if ($price < 0) {
            throw new Exception("Invalid price.");
        }

        $oldSaleStmt = $conn->prepare("SELECT dvd_id, quantity FROM sales WHERE id = :id");
        $oldSaleStmt->bindParam(':id', $saleId, PDO::PARAM_INT);
        $oldSaleStmt->execute();
        $oldSale = $oldSaleStmt->fetch();

        if (!$oldSale) {
            throw new Exception("Sale record not found.");
        }

        $dvdStmt = $conn->prepare("SELECT title, price, stock FROM dvds WHERE id = :id");
        $dvdStmt->bindParam(':id', $dvdId, PDO::PARAM_INT);
        $dvdStmt->execute();
        $dvd = $dvdStmt->fetch();

        if (!$dvd) {
            throw new Exception("Selected DVD not found.");
        }

        $availableStock = $dvd['stock'];
        if ($oldSale['dvd_id'] == $dvdId) {
            $availableStock += $oldSale['quantity'];
        }

        if ($availableStock < $quantity) {
            throw new Exception("Insufficient stock. Available: " . $availableStock);
        }

        $calculatedTotal = $price * $quantity;
        if (abs($calculatedTotal - $total) > 0.01) {
            $total = $calculatedTotal;
        }

        $conn->beginTransaction();

        if ($oldSale['dvd_id'] != $dvdId) {
            $restoreStock = $conn->prepare("UPDATE dvds SET stock = stock + :quantity WHERE id = :id");
            $restoreStock->bindParam(':quantity', $oldSale['quantity'], PDO::PARAM_INT);
            $restoreStock->bindParam(':id', $oldSale['dvd_id'], PDO::PARAM_INT);
            $restoreStock->execute();

            $updateStock = $conn->prepare("UPDATE dvds SET stock = stock - :quantity WHERE id = :id");
            $updateStock->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $updateStock->bindParam(':id', $dvdId, PDO::PARAM_INT);
            $updateStock->execute();
        } else {
            $stockDiff = $quantity - $oldSale['quantity'];
            if ($stockDiff != 0) {
                $updateStock = $conn->prepare("UPDATE dvds SET stock = stock - :diff WHERE id = :id");
                $updateStock->bindParam(':diff', $stockDiff, PDO::PARAM_INT);
                $updateStock->bindParam(':id', $dvdId, PDO::PARAM_INT);
                $updateStock->execute();
            }
        }

        $stmt = $conn->prepare("UPDATE sales 
                                SET dvd_id = :dvd_id, quantity = :quantity, price = :price, 
                                    total = :total, sale_date = :sale_date
                                WHERE id = :id");

        $stmt->bindParam(':dvd_id', $dvdId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':sale_date', $saleDate);
        $stmt->bindParam(':id', $saleId, PDO::PARAM_INT);
        $stmt->execute();

        $conn->commit();

        echo json_encode(["success" => true, "message" => "Sale updated successfully!"]);
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
