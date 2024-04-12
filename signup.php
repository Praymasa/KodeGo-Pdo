<?php
require_once "./db.php";
require_once "./config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['client_name'];
    $email = $_POST['client_email'];
    $password = $_POST['client_password'];
    $contactNumber = $_POST['client_contact_number'];
    $detailedAddress = $_POST['client_detailed_address'];
    $city = $_POST['city'];
    $province = $_POST['province'];

    
    $db = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    $stmt = $db->prepare("INSERT INTO users (name, email, password, contact_number, detailed_address, city, province) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $contactNumber, $detailedAddress, $city, $province]);

    $response = ['success' => true, 'message' => 'User signed up successfully'];
    echo json_encode($response);
} else {
    
    http_response_code(405); 
    $response = ['error' => 'Method not allowed'];
    echo json_encode($response);
}
?>
