<?php
include '../include/db.php';

// Function to handle CREATE operation
function createContact($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['name'], $data['email'], $data['subject'], $data['comments'])) {
        $name = $data['name'];
        $email = $data['email'];
        $subject = $data['subject'];
        $comments = $data['comments'];

        $sql = "INSERT INTO contacts (name, email, subject, comments) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $subject, $comments]);

        echo "Contact added successfully!";
    } else {
        echo "Error: Missing fields in the request.";
    }
}

// Function to handle READ operation
function readContacts($conn) {
    $sql = "SELECT * FROM contacts";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($contacts);
}

// Function to handle UPDATE operation
function updateContact($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'], $data['name'], $data['email'], $data['subject'], $data['comments'])) {
        $id = $data['id'];
        $name = $data['name'];
        $email = $data['email'];
        $subject = $data['subject'];
        $comments = $data['comments'];

        $sql = "UPDATE contacts SET name = ?, email = ?, subject = ?, comments = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $subject, $comments, $id]);

        echo "Contact updated successfully!";
    } else {
        echo "Error: Missing or invalid data in the request.";
    }
}

// Function to handle DELETE operation
function deleteContact($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        $sql = "DELETE FROM contacts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo "Contact deleted successfully!";
    } else {
        echo "Error: Missing 'id' in the request.";
    }
}

// Handle different HTTP methods
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createContact($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    readContacts($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    updateContact($conn);
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    deleteContact($conn);
} else {
    echo "Error: Unsupported HTTP method.";
}
?>
