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
if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["message" => "Faltan datos obligatorios"]);
    exit;
}

$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

// Verificar si el usuario existe
$queryUser = "SELECT users.id, users.name, users.password, users.role_id, companies.name AS company_name 
              FROM users 
              LEFT JOIN companies ON users.company_id = companies.id 
              WHERE users.email = '$email'";
$resultUser = $conn->query($queryUser);

if ($resultUser->num_rows === 0) {
    echo json_encode(["message" => "Credenciales incorrectas"]);
    exit;
}

$user = $resultUser->fetch_assoc();

// Verificar contraseña
if (!password_verify($password, $user["password"])) {
    echo json_encode(["message" => "Credenciales incorrectas"]);
    exit;
}

// Enviar respuesta con información del usuario
echo json_encode([
    "message" => "Inicio de sesión exitoso",
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "role_id" => $user["role_id"],
        "company_name" => $user["company_name"]
    ]
]);

$conn->close();
?>
