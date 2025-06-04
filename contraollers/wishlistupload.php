<?php
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));

session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include("../connection/database.php");

// Ensure session has user_id
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$action = $_GET['action'] ?? '';

// Function to escape and sanitize input
function get_post($key) {
    return isset($_POST[$key]) ? intval($_POST[$key]) : null;
}

switch ($action) {
    case 'get':
        $sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $wishlist = [];

        while ($row = $result->fetch_assoc()) {
            $wishlist[] = $row;
        }

        echo json_encode($wishlist);
        break;

    case 'add':
        $product_id = get_post('product_id');
        if (!$product_id) {
            echo json_encode(['error' => 'Product ID missing']);
            exit;
        }

        // Check if already exists
        $check = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $user_id, $product_id);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ($exists) {
            echo json_encode(['status' => 'already exists']);
            exit;
        }

        $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();

        echo json_encode(['status' => 'added']);
        break;

    case 'remove':
        $product_id = get_post('product_id');
        if (!$product_id) {
            echo json_encode(['error' => 'Product ID missing']);
            exit;
        }

        $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();

        echo json_encode(['status' => 'removed']);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
