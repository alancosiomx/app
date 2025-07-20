<?php
require_once __DIR__ . '/../config.php';
session_start();

$idc = $_SESSION['usuario_nombre'] ?? '';
$carpeta = __DIR__ . '/../archivos_tecnicos/' . $idc;
$archivos = [];

if (is_dir($carpeta)) {
    foreach (scandir($carpeta) as $file) {
        if ($file === '.' || $file === '..') continue;

        $ruta = $carpeta . '/' . $file;
        $modificado = filemtime($ruta);

        // Si tiene menos de 72 horas
        if (time() - $modificado <= 72 * 3600) {
            $archivos[] = [
                'nombre' => $file,
                'fecha' => date('d M Y H:i', $modificado),
                'ruta_relativa' => '/archivos_tecnicos/' . urlencode($idc) . '/' . urlencode($file)
            ];
        }
    }
}

$contenido = __DIR__ . '/bloques/descargar_hs_lista.php';
include __DIR__ . '/layout_tecnico.php';
