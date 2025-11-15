<?php
header('Content-Type: application/json');
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $saleStmt = $conn->prepare("SELECT dvd_id, quantity FROM sales WHERE id = :id");
        $saleStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $saleStmt->execute();
        $sale = $saleStmt->fetch();

        if (!$sale) {
            throw new Exception("Sale record not found.");
        }

        $conn->beginTransaction();

        $restoreStock = $conn->prepare("UPDATE dvds SET stock = stock + :quantity WHERE id = :dvd_id");
        $restoreStock->bindParam(':quantity', $sale['quantity'], PDO::PARAM_INT);
        $restoreStock->bindParam(':dvd_id', $sale['dvd_id'], PDO::PARAM_INT);
        $restoreStock->execute();

        $deleteStmt = $conn->prepare("DELETE FROM sales WHERE id = :id");
        $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteStmt->execute();

        $conn->commit();

        echo json_encode(["success" => true, "message" => "Sale deleted and stock restored!"]);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No ID provided."]);
}
?>
