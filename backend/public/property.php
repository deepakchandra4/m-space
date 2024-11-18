<?php
include '../include/db.php';

// Function to handle CREATE operation
function createProperty($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['location'], $data['squarefeet'], $data['beds'], $data['baths'], $data['description'], $data['images'], $data['price'])) {
        $location = $data['location'];
        $squarefeet = $data['squarefeet'];
        $beds = $data['beds'];
        $baths = $data['baths'];
        $description = $data['description'];
        $images = $data['images'];
        $price = $data['price'];

        // Insert into the database
        $sql = "INSERT INTO property (location, squarefeet, beds, baths, description, images, price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$location, $squarefeet, $beds, $baths, $description, $images, $price]);

        echo "Property added successfully!";
    } else {
        echo "Error: Missing fields in the request.";
    }
}

// Function to handle READ operation
function readproperty($conn) {
    $sql = "SELECT * FROM property";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $property = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($property);
}

// Function to handle UPDATE operation
function updateProperty($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'], $data['location'], $data['squarefeet'], $data['beds'], $data['baths'], $data['description'], $data['images'], $data['price'])) {
        $id = $data['id'];
        $location = $data['location'];
        $squarefeet = $data['squarefeet'];
        $beds = $data['beds'];
        $baths = $data['baths'];
        $description = $data['description'];
        $images = $data['images'];
        $price = $data['price'];

        // Update the property
        $sql = "UPDATE property SET location = ?, squarefeet = ?, beds = ?, baths = ?, description = ?, images = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$location, $squarefeet, $beds, $baths, $description, $images, $price, $id]);

        echo "Property updated successfully!";
    } else {
        echo "Error: Missing or invalid data in the request.";
    }
}

// Function to handle DELETE operation
function deleteProperty($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        // Delete the property
        $sql = "DELETE FROM property WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo "Property deleted successfully!";
    } else {
        echo "Error: Missing 'id' in the request.";
    }
}

// Handle different HTTP methods
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createProperty($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    readproperty($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    updateProperty($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    deleteProperty($conn);
} else {
    echo "Error: Unsupported HTTP method.";
}
?>
