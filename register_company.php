<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$db_name = "gestion_horario";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error al conectar con la base de datos."]);
    exit;
}

// Leer datos enviados
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["companyName"]) || empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios."]);
    exit;
}

// Insertar en la tabla companies
$companyName = $conn->real_escape_string($data["companyName"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_BCRYPT);

// Verificar si la empresa ya existe
$queryCheck = "SELECT id FROM companies WHERE name = '$companyName'";
$resultCheck = $conn->query($queryCheck);
if ($resultCheck->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "La empresa ya está registrada."]);
    exit;
}

// Insertar la nueva empresa
$queryInsert = "INSERT INTO companies (name, email, password) VALUES ('$companyName', '$email', '$password')";
if ($conn->query($queryInsert) === TRUE) {
    echo json_encode(["success" => true, "message" => "Empresa registrada con éxito."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar la empresa.", "error" => $conn->error]);
}

$conn->close();
?>
