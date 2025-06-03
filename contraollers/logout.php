<?php
session_start();
include("../connection/database.php");

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");

session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
setcookie("PHPSESSID", "", time() - 3600, "/"); // Optional: clear PHPSESSID cookie

echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
?>
