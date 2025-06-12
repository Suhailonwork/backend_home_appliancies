<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");
// DB connection
$conn = new mysqli("localhost", "root", "", "ecommerce");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Query data
$sql = "SELECT * FROM productdetails"; // change table name accordingly
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}

$conn->close();
?>