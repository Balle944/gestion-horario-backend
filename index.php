<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header("Content-Type: application/json; charset=UTF-8");

echo json_encode(["message" => "API conectada con éxito"], JSON_UNESCAPED_UNICODE);
?>
