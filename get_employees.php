<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$db_name = "gestion_horario";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(["message" => "Error al conectar con la base de datos"]);
    exit;
}

if (!isset($_GET["company_id"]) || !is_numeric($_GET["company_id"])) {
    echo json_encode(["message" => "Parámetro company_id faltante o inválido"]);
    exit;
}

$company_id = (int)$_GET["company_id"];
$query = "SELECT id, name, email, role_id FROM users WHERE company_id = $company_id";

$result = $conn->query($query);
$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

echo json_encode(["employees" => $employees]);
$conn->close();
?>
