<?php

require_once './db.php';
require_once './config.php';

class Clients {
    public $client_id;
    public $client_email;
    public $client_password;
    public $client_name;
    public $contact_number;
    public $detailed_address;
    public $city;
    public $province;
}

$db = new Connection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT * FROM clients";
    if (isset($_GET["client_id"])) {
        $query .= "WHERE client_id = ?";
        $stmt = $db->ready($query, [$_GET["client_id"]]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Clients");
        $result = $stmt->fetch();
        echo json_encode($result, JSON_PRETTY_PRINT);
    }else {
        $stmt = $db->ready($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Clients");
        $result = $stmt->fetchAll();
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    print_r($_POST);
    $_POST = json_decode(file_get_contents("php://input"), true);
    
    $requiredFields = ["client_name", "client_email", "client_password", "contact_number", "detailed_address", "city", "province"];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            http_response_code(400);
            echo json_encode(array("message" => "Missing required field: $field"));
            exit;
        }
    }
    $query = "INSERT INTO clients (client_name, client_email, client_password, contact_number, detailed_address, city, province) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $db->ready($query, [
        $_POST["client_name"],
        $_POST["client_email"],
        $_POST["client_password"],
        $_POST["contact_number"],
        $_POST["detailed_address"],
        $_POST["city"],
        $_POST["province"],
    ]);
    
    $id = $db->connection->lastInsertId();
    $query = "SELECT * FROM clients WHERE client_id = ?";
    $stmt = $db->ready($query, [$id]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, "Clients");
    $result = $stmt->fetch();
    echo json_encode(array("message" => "Client created successfully", "client" => $result), JSON_PRETTY_PRINT);

} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    parse_str(file_get_contents("php://input"), $_PUT);
    if (isset($_PUT["cleint_id"])) {
        $query = "UPDATE clients SET client_id = ?, client_email = ?, client_password = ?, client_name = ?, contact_nemuber = ?, detailed_address = ?, city = ?, province =? WHERE client_id = ?";
        $stmt = $db->ready($query, [
        $_PUT["client_email"],
        $_PUT["client_password"],
        $_PUT["client_name"],
        $_PUT["contact_number"],
        $_PUT["detailed_address"],
        $_PUT["city"],
        $_PUT["province"],
        $_PUT["client_id"],
        ]);
        echo json_encode(array("message" => "Employee successfully updated"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Missing Information"), JSON_PRETTY_PRINT);
    }

} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (isset($_DELETE["client_id"])) {
        $query = "DELETE FROM client WHERE client_id = ?";
        $stmt = $db->ready($query, [
            $_DELETE["client_id"]
        ]);
        echo json_encode(array("message" => "You deleted your account successfully"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => " Missing information"), JSON_PRETTY_PRINT);
    }
}

?>
