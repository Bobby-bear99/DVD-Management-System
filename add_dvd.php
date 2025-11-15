<?php
header('Content-Type: application/json');
include 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['dvdTitle'] ?? '');
        $genre = $_POST['dvdGenre'] ?? '';
        $year = $_POST['dvdYear'] ?? '';
        $price = $_POST['dvdPrice'] ?? '';
        $stock = $_POST['dvdStock'] ?? '';
        $language = $_POST['dvdLanguage'] ?? '';

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

        $imagePath = '';

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
                $imagePath = $targetPath;
            } else {
                throw new Exception("Failed to upload image.");
            }
        }

        $stmt = $conn->prepare("INSERT INTO dvds (title, genre, year, price, stock, language, image_path)
                                VALUES (:title, :genre, :year, :price, :stock, :language, :image_path)");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "DVD added successfully!", "id" => $conn->lastInsertId()]);
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
