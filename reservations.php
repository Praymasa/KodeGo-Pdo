<?php

require_once "./db.php";
require_once "./config.php";

class Reservations {
    public $res_id;
    public $client_id;
    public $emp_id;
    public $emp_name;
    public $service_id;
    public $service_title;
    public $term_id;
    public $contact_term;
    public $date;
    public $time;
}

$db = new Connection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT * FROM reservations";
    if (isset($_GET["res_id"])) {
        $query .= " WHERE res_id = ?";
        $stmt = $db->ready($query, [$_GET["res_id"]]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Reservations");
        $result = $stmt->fetch();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        $stmt = $db->ready($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Reservations");
        $result = $stmt->fetchAll();
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_POST = json_decode(file_get_contents("php://input"), true);
    $query = "INSERT INTO reservations (client_name, client_number, client_detailedAdd, client_city,client_province, emp_id, emp_name, service_title, contract_term, date, time) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $db->ready($query, [
        $_POST["client_name"],
        $_POST["client_number"],
        $_POST["client_detailedAdd"],
        $_POST["client_city"],
        $_POST["client_province"],
        $_POST["emp_id"],
        $_POST["emp_name"],
        $_POST["service_title"],
        $_POST["contract_term"],
        $_POST["date"],
        $_POST["time"],
    ]);
    $id = $db->connection->lastInsertId();
    $query = "SELECT * FROM reservations WHERE res_id = ?";
    $stmt = $db->ready($query, [$id]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, "Reservations");
    $result = $stmt->fetch();
    echo json_encode($result, JSON_PRETTY_PRINT);
    echo json_encode(array("message" => "Your reservation has been created"), JSON_PRETTY_PRINT);

} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    parse_str(file_get_contents("php://input"), $_PUT);
    if (isset($_PUT["res_id"])) {
        $query = "UPDATE reservations 
                  SET client_name = ?, client_number = ?, client_detailedAdd = ?, client_city = ?, client_province = ?, emp_id = ?, emp_name = ?, service_id = ?, service_title = ?, 
                      term_id = ?, contract_term = ?, date = ?, time = ? 
                  WHERE res_id = ?";
        $stmt = $db->ready($query, [
            $_PUT["client_name"],
            $_PUT["client_number"],
            $_PUT["client_detailedAdd"],
            $_PUT["client_city"],
            $_PUT["client_province"],
            $_PUT["emp_id"],
            $_PUT["emp_name"],
            $_PUT["service_id"], // Add service_id here
            $_PUT["service_title"],
            $_PUT["contract_term"],
            $_PUT["date"],
            $_PUT["time"],
            $_PUT["res_id"]
        ]);
        echo json_encode(array("message" => "Updated successfully"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Missing information"), JSON_PRETTY_PRINT);
    }

} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $res_id = $_GET["res_id"]; // Retrieve res_id from query string
    if (isset($res_id)) {
        $query = "DELETE FROM reservations WHERE res_id = ?";
        $stmt = $db->ready($query, [$res_id]);
        echo json_encode(array("message" => "Your reservation has been canceled"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Failed to cancel the reservation"));
    }
}

