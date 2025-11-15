<?php
header('Content-Type: application/json');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['dvdId'] ?? 0);
        $title = trim($_POST['dvdTitle'] ?? '');
        $genre = $_POST['dvdGenre'] ?? '';
        $year = $_POST['dvdYear'] ?? '';
        $price = $_POST['dvdPrice'] ?? '';
        $stock = $_POST['dvdStock'] ?? '';
        $language = $_POST['dvdLanguage'] ?? '';

        if ($id <= 0) {
            throw new Exception("Invalid DVD ID.");
        }

        if (empty($title) || empty($genre) || empty($year) || empty($price) || empty($stock) || empty($language)) {
            throw new Exception("All fields are required.");
        }

        if (!is_numeric($year) || $year < 1900 || $year > 2025) {
            throw new Exception("Invalid year.");
        }

        if (!is_numeric($price) || $price < 0) {
            throw new Exception("Invalid price.");
        }

        if (!is_numeric($stock) || $stock < 0) {
            throw new Exception("Invalid stock quantity.");
        }

        $checkStmt = $conn->prepare("SELECT image_path FROM dvds WHERE id = :id");
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        $existingDvd = $checkStmt->fetch();

        if (!$existingDvd) {
            throw new Exception("DVD not found.");
        }

        $imagePath = $existingDvd['image_path'];

        if (isset($_FILES['dvdImage']) && $_FILES['dvdImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($_FILES['dvdImage']['size'] > 5000000) {
                throw new Exception("File size too large. Maximum 5MB allowed.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['dvdImage']['tmp_name']);
            finfo_close($finfo);

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.");
            }

            $extension = $allowedMimeTypes[$mimeType];
            $filename = uniqid('dvd_') . '.' . $extension;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['dvdImage']['tmp_name'], $targetPath)) {
                if (!empty($imagePath) && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $imagePath = $targetPath;
            } else {
                throw new Exception("Failed to upload new image.");
            }
        }

        $stmt = $conn->prepare("UPDATE dvds 
                                SET title = :title, genre = :genre, year = :year, 
                                    price = :price, stock = :stock, language = :language, 
                                    image_path = :image_path
                                WHERE id = :id");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "DVD updated successfully!"]);
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>

