<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Conexión a la base de datos
$host = "localhost";
$db_name = "gestion_horario";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die(json_encode(["message" => "Error al conectar con la base de datos"]));
}

// Leer datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Validar datos obligatorios
if (empty($data["name"]) || empty($data["email"]) || empty($data["password"]) || empty($data["company_name"])) {
    echo json_encode(["message" => "Faltan datos obligatorios"]);
    exit;
}

$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_BCRYPT);
$role = 2; // Rol fijo como "Empresa"
$company_name = $conn->real_escape_string($data["company_name"]);

// Verificar si el correo ya está registrado
$queryCheckEmail = "SELECT id FROM users WHERE email = '$email'";
$resultCheckEmail = $conn->query($queryCheckEmail);

if ($resultCheckEmail->num_rows > 0) {
    echo json_encode(["message" => "El correo ya está registrado"]);
    exit;
}

// Verificar si la empresa ya está registrada
$queryCheckCompany = "SELECT id FROM companies WHERE name = '$company_name'";
$resultCheckCompany = $conn->query($queryCheckCompany);

if ($resultCheckCompany->num_rows > 0) {
    $company = $resultCheckCompany->fetch_assoc();
    $company_id = $company["id"];
} else {
    // Insertar nueva empresa
    $queryInsertCompany = "INSERT INTO companies (name) VALUES ('$company_name')";
    if (!$conn->query($queryInsertCompany)) {
        echo json_encode(["message" => "Error al registrar la empresa", "error" => $conn->error]);
        exit;
    }
    $company_id = $conn->insert_id;
}

// Insertar usuario vinculado a la empresa
$queryInsertUser = "INSERT INTO users (name, email, password, role_id, company_id) VALUES ('$name', '$email', '$password', $role, $company_id)";
if ($conn->query($queryInsertUser) === TRUE) {
    echo json_encode(["message" => "Usuario registrado con éxito"]);
} else {
    echo json_encode(["message" => "Error al registrar el usuario", "error" => $conn->error]);
}

$conn->close();
?>
