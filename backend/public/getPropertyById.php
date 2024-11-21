<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../include/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM property WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch properties

        if ($properties) {
            foreach ($properties as &$property) {
                // Check if 'images' contains multiple images (comma-separated)
                $images = explode(',', $property['images']); // Split into an array of image filenames
                $imageUrls = array_map(function ($image) {
                    return 'uploads/' . trim($image); // Create the full URL for each image
                }, $images);

                // Replace the 'images' with the array of image URLs
                $property['images'] = $imageUrls;
            }

            // Return the modified property list with images
            echo json_encode($properties);
        } else {
            echo json_encode(["status" => "error", "message" => "No property found."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Property ID is required."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error: Unsupported HTTP method."]);
}
?>
