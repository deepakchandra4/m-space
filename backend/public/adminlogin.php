<?php
// Include the database connection
include('db.php');

// Query to fetch admin credentials
$sql = "SELECT username, password FROM admins WHERE id = 1";  // Assuming there's only one admin user

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Output the admin credentials as JSON
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($admin);
    } else {
        echo json_encode(['error' => 'Admin credentials not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}

$conn = null;
?>
