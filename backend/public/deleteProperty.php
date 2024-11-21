<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

include '../include/db.php';

// Function to handle DELETE operation
function deleteProperty($conn)
{
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $property_id = $_GET['id'];

        if (filter_var($property_id, FILTER_VALIDATE_INT)) {
            $sql = "DELETE FROM property WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$property_id]);

            echo json_encode(["status" => "success", "message" => "Property deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid property ID."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Property ID is required."]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    deleteProperty($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Error: Unsupported HTTP method."]);
}
?>
