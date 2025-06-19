<?php
require_once '../../config.php';
require_once '../../vendor/autoload.php';

use TCPDF;

session_start();

$tickets = $_POST['tickets'] ?? [];
if (empty($tickets)) {
    die("❌ No se seleccionaron tickets.");
}

$tmpDir = sys_get_temp_dir() . '/hs_pdfs_' . uniqid();
mkdir($tmpDir);

$pdfPaths = [];

foreach ($tickets as $ticket) {
    // Obtener datos
    $stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
    $stmt->execute([$ticket]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servicio) continue;

    // Crear PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('OMNIPOS');
    $pdf->SetAuthor('OMNIPOS');
    $pdf->SetTitle("Hoja de Servicio - Ticket $ticket");
    $pdf->SetMargins(15, 25, 15);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // Aquí puedes llamar a una función para crear el contenido de cada PDF
    // Para ahorrar espacio, copio sólo encabezado ejemplo:
    $pdf->Cell(0, 10, "Hoja de Servicio - Ticket: " . $servicio['ticket'], 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->MultiCell(0, 7, "Comercio: " . $servicio['comercio'], 0, 'L');
    $pdf->MultiCell(0, 7, "Dirección: " . $servicio['domicilio'], 0, 'L');
    // ... añadir más contenido igual que en generar_hs.php

    $filePath = "$tmpDir/HS_{$ticket}.pdf";
    $pdf->Output($filePath, 'F');
    $pdfPaths[] = $filePath;
}

// Crear ZIP
$zipName = "Hojas_Servicio_" . date('Ymd_His') . ".zip";
$zipPath = sys_get_temp_dir() . '/' . $zipName;

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
    die("❌ No se pudo crear archivo ZIP");
}

foreach ($pdfPaths as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

// Enviar ZIP al navegador
header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"$zipName\"");
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);

// Limpiar archivos temporales
foreach ($pdfPaths as $file) {
    unlink($file);
}
rmdir($tmpDir);
unlink($zipPath);

exit;
