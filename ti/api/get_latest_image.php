<?php
$dir = 'webcam/imagens/';
$imagens = glob($dir . 'webcam_*.jpg');

if (!$imagens) {
    http_response_code(404);
    echo json_encode(['error' => 'Nenhuma imagem encontrada.']);
    exit;
}

// Ordenar da mais recente para a mais antiga
usort($imagens, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Eliminar imagens extras (manter só as 10 mais recentes)
if (count($imagens) > 10) {
    $aEliminar = array_slice($imagens, 10); // pega as que estão da 11ª em diante
    foreach ($aEliminar as $img) {
        unlink($img);
    }
    // Atualiza a lista de imagens
    $imagens = array_slice($imagens, 0, 10);
}

$ultimaImagem = $imagens[0];
$dataHora = date("Y-m-d H:i:s", filemtime($ultimaImagem));

echo json_encode([
    'path' => $ultimaImagem,
    'hora' => $dataHora
]);
