<?php
header('Content-Type: application/json');
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $imageStmt = $conn->prepare("SELECT image_path FROM dvds WHERE id = :id");
        $imageStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $imageStmt->execute();
        $dvd = $imageStmt->fetch();

        $query = "DELETE FROM dvds WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            if ($dvd && !empty($dvd['image_path']) && file_exists($dvd['image_path'])) {
                unlink($dvd['image_path']);
            }
            echo json_encode(["success" => true, "message" => "DVD deleted successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "DVD not found or already deleted."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No ID provided."]);
}
?>
