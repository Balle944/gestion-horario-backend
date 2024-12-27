<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
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

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["name"]) || empty($data["email"]) || empty($data["password"]) || empty($data["role"]) || empty($data["company_id"])) {
    echo json_encode(["message" => "Faltan datos obligatorios"]);
    exit;
}

$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_BCRYPT);
$role = (int)$data["role"];
$company_id = (int)$data["company_id"];

// Verificar si el company_id existe
$queryCheckCompany = "SELECT id FROM companies WHERE id = $company_id";
$resultCheckCompany = $conn->query($queryCheckCompany);

if ($resultCheckCompany->num_rows === 0) {
    echo json_encode([
        "message" => "El ID de la empresa no es válido.",
        "company_id" => $company_id,
    ]);
    exit;
}

// Verificar si el correo ya está registrado
$queryCheckEmail = "SELECT id FROM users WHERE email = '$email'";
$resultCheckEmail = $conn->query($queryCheckEmail);

if ($resultCheckEmail->num_rows > 0) {
    echo json_encode(["message" => "El correo ya está registrado"]);
    exit;
}

// Insertar usuario
$queryInsertUser = "INSERT INTO users (name, email, password, role_id, company_id) VALUES ('$name', '$email', '$password', $role, $company_id)";
if ($conn->query($queryInsertUser) === TRUE) {
    echo json_encode(["message" => "Empleado registrado con éxito"]);
} else {
    echo json_encode(["message" => "Error al registrar el empleado", "error" => $conn->error]);
}

$conn->close();
?>
