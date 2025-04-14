<?php
header("Content-Type: application/json");

$sensor = $_GET['sensor'] ?? null;
$pastas = ["temperatura", "humidade", "distancia", "cancela", "ventoinha", "led"];
$baseDir = __DIR__ . "/{$sensor}/log.txt";

$resultado = [];

if ($sensor && in_array($sensor, $pastas) && file_exists($baseDir)) {
    $log = file_get_contents($baseDir);
    $resultado[$sensor] = ["log" => $log];
}

echo json_encode($resultado);
