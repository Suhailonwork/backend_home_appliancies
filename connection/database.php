<?php
$host = "localhost";
$user = "root";
// u309740424_SummitHome
$pass = "";
// SummitHome@123
$db_name = "ecommerce";
// u309740424_SummitHome

 
$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
