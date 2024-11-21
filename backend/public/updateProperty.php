<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../include/db.php';

// Function to handle UPDATE operation
function updateProperty($conn)
{
    try {
        // Decode the JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if 'id' is provided in the request
        if (!isset($data['ID'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Property ID is required."]);
            return;
        }

        // Check if the property exists
        $id = $data['ID'];
        $sql = "SELECT * FROM property WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() == 0) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Property not found."]);
            return;
        }

        // If image is provided, handle the upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
                return;
            }

            $imagePath = $imageName;  // Store the image path
        } else {
            // If no new image is uploaded, retain the old image path
            $stmt = $conn->prepare("SELECT images FROM property WHERE id = ?");
            $stmt->execute([$id]);
            $property = $stmt->fetch(PDO::FETCH_ASSOC);
            $imagePath = $property['images'];
        }

        // Check if all required fields are provided for the update
        if (!isset($data['location'], $data['squarefeet'], $data['beds'], $data['baths'], $data['description'], $data['price'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
            return;
        }

        // Update the property data in the database
        $sql = "UPDATE property SET location = ?, squarefeet = ?, beds = ?, baths = ?, description = ?, images = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['location'],
            $data['squarefeet'],
            $data['beds'],
            $data['baths'],
            $data['description'],
            $imagePath,  // This will be the new image if provided, or the old image if not
            $data['price'],
            $id
        ]);

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Property updated successfully!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    updateProperty($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Error: Unsupported HTTP method."]);
}
?>
