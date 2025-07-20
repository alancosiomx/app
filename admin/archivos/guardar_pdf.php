<?php
require_once __DIR__ . '/../init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('❌ Método no permitido.');
}

$idc = $_POST['idc'] ?? '';
$archivos = $_FILES['archivos'] ?? null;

if (!$idc || !$archivos) {
    die('❌ Técnico o archivos no proporcionados.');
}

// Crear carpeta si no existe
$ruta_carpeta = __DIR__ . '/../../archivos_tecnicos/' . $idc;
if (!is_dir($ruta_carpeta)) {
    mkdir($ruta_carpeta, 0777, true);
}

// Procesar cada archivo
for ($i = 0; $i < count($archivos['name']); $i++) {
    $nombre = $archivos['name'][$i];
    $tmp = $archivos['tmp_name'][$i];
    $tipo = $archivos['type'][$i];

    // Validar que sea PDF
    if (mime_content_type($tmp) !== 'application/pdf') {
        continue; // ignorar si no es pdf real
    }

    // Generar nombre seguro
    $nombre_seguro = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nombre);
    move_uploaded_file($tmp, $ruta_carpeta . '/' . $nombre_seguro);
}

header("Location: subir_pdf.php?ok=1");
exit;
