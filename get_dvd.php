<?php
header('Content-Type: application/json');
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $conn->prepare("SELECT * FROM dvds WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $dvd = $stmt->fetch();

        if ($dvd) {
            echo json_encode(["success" => true, "dvd" => $dvd]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "DVD not found"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No ID provided"]);
}
?>

