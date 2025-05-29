<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");

include("../connection/database.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $quantity = $data['quantity'];
    $product_name = $data['product_name'];
    $product_price = $data['product_price'];
    $image = $data['image'];

    // Check if product already in cart for user
    $stmt = $conn->prepare("SELECT sno, quantity FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity and total
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $total = $new_quantity * $product_price;
        $update = $conn->prepare("UPDATE cart SET quantity=?, total=? WHERE sno=?");
        $update->bind_param("idi", $new_quantity, $total, $row['sno']);
        $update->execute();
    } else {
        // Insert new cart record
        $total = $quantity * $product_price;
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity, total, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("iisdiis", $user_id, $product_id, $product_name, $product_price, $quantity, $total, $image);
        $insert->execute();
    }
    echo json_encode(['success' => true]);
} elseif ($method === 'GET') {
    // Fetch cart for this user
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($cartItems);
}
?>
