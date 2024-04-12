<?php

require_once "./db.php";
require_once "./config.php";

class Employees {
    public $emp_id;
    public $emp_name;
    public $emp_age;
    public $emp_location;
    public $emp_contact_number;
    public $emp_position;
    public $emp_bio;
}

$db = new Connection();


if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT * FROM employee";
    if (isset($_GET["emp_id"])) {
        $query .= " WHERE emp_id = ?";
        $stmt = $db->ready($query, [$_GET["emp_id"]]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Employees");
        $result = $stmt->fetch();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        $stmt = $db->ready($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Employees");
        $result = $stmt->fetchAll();
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_POST = json_decode(file_get_contents("php://input"), true);
    $query = "INSERT INTO employee (emp_name, emp_age, emp_location, emp_contact_number, emp_position, emp_bio) VALUES (?, ?, ?, ?, ?, ?)";
    $db->ready($query, [
        $_POST["emp_name"],
        $_POST["emp_age"],
        $_POST["emp_location"],
        $_POST["emp_contact_number"],
        $_POST["emp_position"],
        $_POST["emp_bio"],
    ]);
    $id = $db->connection->lastInsertId();
    $query = "SELECT * FROM employee WHERE emp_id = ?";
    $stmt = $db->ready($query, [$id]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, "Employees");
    $result = $stmt->fetch();
    echo json_encode($result, JSON_PRETTY_PRINT);
    echo json_encode(array("message" => "Employee created successfully"), JSON_PRETTY_PRINT);

} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    parse_str(file_get_contents("php://input"), $_PUT);
    if (isset($_PUT["emp_id"])) {
        $query = "UPDATE employee SET emp_name = ?, emp_age = ?, emp_location = ?, emp_contact_number = ?, emp_position = ?, emp_bio = ? WHERE emp_id = ?";
        $stmt = $db->ready($query, [
            $_PUT["emp_name"],
            $_PUT["emp_age"],
            $_PUT["emp_location"],
            $_PUT["emp_contact_number"],
            $_PUT["emp_position"],
            $_PUT["emp_bio"],
            $_PUT["emp_id"]
        ]);
        echo json_encode(array("message" => "Employee updated successfully"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Missing information"), JSON_PRETTY_PRINT);
    }

} else if ($method === "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (isset($_DELETE["emp_id"])) {
        $query = "DELETE FROM employee WHERE emp_id = ?";
        $stmt = $db->ready($query, [$_DELETE["emp_id"]]);
        echo json_encode(array("message" => "Employee deleted successfully"), JSON_PRETTY_PRINT);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Missing information"), JSON_PRETTY_PRINT);
    }
}
?>





 