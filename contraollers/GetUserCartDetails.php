<?php
session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");

include("../connection/database.php");

// Check if the user is logged in
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get user information
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// If user not found in DB
if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found in the database'
    ]);
    exit;
}

// Get cart items
$cart_stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$cart_stmt->execute([$user_id]);
$cart = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response
echo json_encode([
    'status' => 'success',
    'message' => 'User and cart data retrieved successfully',
    'user' => $user,
    'cart' => $cart
]);
?>
