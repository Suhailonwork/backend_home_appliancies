<?php
session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");

include("../connection/database.php");

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$email = $_SESSION['user_email'];

// Add image column here (change image_path to your actual column name)
$query = "SELECT order_id, item_name, quantity, total_price, order_date, status, image_path FROM my_order WHERE user_email = ? ORDER BY order_date DESC";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

echo json_encode(['status' => 'success', 'orders' => $orders]);
