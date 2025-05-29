<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");

include("../connection/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Check if user already exists
    $checkQuery = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Query error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        exit;
    }

    // Validate password format
    if (!preg_match('/^(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{6,}$/', $password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters, include one uppercase letter and one special character.']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $insertQuery = "INSERT INTO user (name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Insert query error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'User registered']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
