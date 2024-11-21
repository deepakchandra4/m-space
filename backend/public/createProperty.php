<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../include/db.php';

// Function to handle CREATE operation with multiple images
function createProperty($conn)
{
    try {
        // Check if files are uploaded
        if (!isset($_FILES['images']) || count($_FILES['images']['name']) == 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "At least one image file is required."]);
            return;
        }

        // Set the upload directory
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imagePaths = [];

        // Loop through each file and handle the upload
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            // Check for errors
            if ($_FILES['images']['error'][$i] != UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Error uploading image."]);
                return;
            }

            // Generate a unique file name for each image
            $imageName = uniqid() . '-' . basename($_FILES['images']['name'][$i]);
            $uploadFile = $uploadDir . $imageName;

            // Move the file to the upload directory
            if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploadFile)) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
                return;
            }

            // Store the image path
            $imagePaths[] = $imageName;
        }

        // Convert the image paths array to a comma-separated string
        $imagePathsStr = implode(",", $imagePaths);

        // Check if required fields are set
        if (!isset($_POST['location'], $_POST['squarefeet'], $_POST['beds'], $_POST['baths'], $_POST['description'], $_POST['price'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
            return;
        }

        // Insert the property details into the database
        $sql = "INSERT INTO property (location, squarefeet, beds, baths, description, images, price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_POST['location'], $_POST['squarefeet'], $_POST['beds'], $_POST['baths'], $_POST['description'], $imagePathsStr, $_POST['price']]);

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Property added successfully!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createProperty($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Error: Unsupported HTTP method."]);
}
?>
