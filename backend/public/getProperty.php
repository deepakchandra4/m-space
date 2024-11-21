<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../include/db.php';

// Function to handle READ operation
function readProperty($conn)
{
    $sql = "SELECT * FROM property";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($properties as &$property) {
        // Check if 'images' contains multiple images (comma-separated)
        $images = explode(',', $property['images']); // Split into an array of image filenames
        $imageUrls = array_map(function($image) {
            return 'uploads/' . trim($image); // Create the full URL for each image
        }, $images);

        // Use only the first image in the array
        $property['images'] = $imageUrls[0]; // Set to the first image URL
    }

    // Return the modified property list
    echo json_encode($properties);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    readProperty($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Error: Unsupported HTTP method."]);
}
?>
