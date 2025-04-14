<?php
function loadSensorData($nome)
{
    $base = "$nome/";
    return [
        "valor" => file_get_contents($base . "valor.txt"),
        "hora" => file_get_contents($base . "hora.txt")
    ];
}

$sensores = ["temperatura", "humidade", "distancia", "ventoinha", "cancela", "led"];
$data = [];

foreach ($sensores as $sensor) {
    $data[$sensor] = loadSensorData($sensor);
}

header('Content-Type: application/json');
echo json_encode($data);
