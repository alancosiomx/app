<?php
$dir = __DIR__ . '/../archivos_tecnicos'; // Ruta real a la carpeta

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
    if ($file->isFile()) {
        $horas_pasadas = (time() - $file->getMTime()) / 3600;

        if ($horas_pasadas > 72) {
            unlink($file->getPathname());
        }
    }
}
