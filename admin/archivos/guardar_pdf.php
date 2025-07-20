<?php
ob_start();
require_once __DIR__ . '/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('❌ Método no permitido.');
}

// Validar token CSRF si lo estás usando
if ($_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die('Error de seguridad. Por favor recarga la página.');
}

$idc = $_POST['idc'] ?? '';
$archivos = $_FILES['archivos'] ?? null;

if (!$idc || !$archivos) {
    die('❌ Técnico o archivos no proporcionados.');
}

$ruta_carpeta = __DIR__ . '/../../archivos_tecnicos/' . $idc;
if (!is_dir($ruta_carpeta)) {
    mkdir($ruta_carpeta, 0777, true);
}

for ($i = 0; $i < count($archivos['name']); $i++) {
    $nombre = $archivos['name'][$i];
    $tmp = $archivos['tmp_name'][$i];

    if (mime_content_type($tmp) !== 'application/pdf') {
        continue;
    }

    $nombre_seguro = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nombre);
    move_uploaded_file($tmp, $ruta_carpeta . '/' . $nombre_seguro);
}

header("Location: subir_pdf.php?ok=1");
exit;

ob_end_flush();
