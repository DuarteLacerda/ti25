<?php
header('Content-Type: application/json');

// Pasta onde as imagens estão guardadas
$folder = __DIR__ . '/webcam/imagens';  // Ajusta conforme o teu caminho real

// Verifica se a pasta existe
if (!is_dir($folder)) {
    echo json_encode([]);
    exit;
}

// Pega todos os ficheiros de imagem (jpg, png, jpeg, gif)
$files = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// Ordena ficheiros por data de modificação, descendente (mais recentes primeiro)
usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Limita a 10 imagens mais recentes
$files = array_slice($files, 0, 10);

$result = [];

foreach ($files as $file) {
    $filename = basename($file);

    // Cria a URL da imagem para o frontend (ajusta conforme tua estrutura)
    $url = "webcam/imagens/" . rawurlencode($filename);

    // Data/hora da última modificação (formato Y-m-d H:i:s)
    $datetime = date("Y-m-d H:i:s", filemtime($file));

    // Exemplo de campo "alert" (podes omitir ou personalizar)
    $alert = ""; // Ou alguma lógica para gerar alertas se quiseres

    $result[] = [
        "image" => $url,
        "datetime" => $datetime,
        "alert" => $alert
    ];
}

echo json_encode($result);
