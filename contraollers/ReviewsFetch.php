<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173"); // Adjust if needed
header("Access-Control-Allow-Credentials: true");

include("../connection/database.php");

// Optional: Get product_id from query string if needed
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $product_id);
} else {
    $stmt = $conn->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();

$reviews = [];

while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(["status" => "success", "reviews" => $reviews]);

$stmt->close();
$conn->close();
?>
