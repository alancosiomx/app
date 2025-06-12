<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

// Validación: ticket por GET
$ticket = $_GET['ticket'] ?? null;
if (!$ticket) {
  die("No se especificó un ticket.");
}

// Buscar datos del servicio
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$servicio) {
  die("Servicio no encontrado.");
}

// Construcción del HTML
$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Hoja de Servicio - BBVA</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; padding: 30px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    td, th { border: 1px solid #000; padding: 5px; vertical-align: top; }
    .sin-borde td { border: none; }
    .titulo { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
    .checkbox { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; margin-right: 5px; }
  </style>
</head>
<body>

  <div class="titulo">HOJA DE SERVICIO - BBVA</div>

  <table>
    <tr><td><strong>Ticket:</strong> {$servicio['ticket']}</td><td><strong>Afiliación:</strong> {$servicio['afiliacion']}</td></tr>
    <tr><td colspan="2"><strong>Comercio:</strong> {$servicio['comercio']}</td></tr>
    <tr><td colspan="2"><strong>Domicilio:</strong> {$servicio['domicilio']}, {$servicio['colonia']}, {$servicio['ciudad']}, CP {$servicio['cp']}</td></tr>
    <tr><td><strong>Técnico:</strong> {$servicio['idc']}</td><td><strong>Fecha:</strong> ".date('d/m/Y')."</td></tr>
  </table>

  <table>
    <tr><th colspan="2">Tipo de Solución</th></tr>
    <tr><td><span class="checkbox"></span> Reprogramación</td><td><span class="checkbox"></span> Sustitución de TPV</td></tr>
    <tr><td><span class="checkbox"></span> Cancelación</td><td><span class="checkbox"></span> Otro: ___________________________</td></tr>
  </table>

  <table>
    <tr><td colspan="2"><strong>Serie Instalada:</strong> {$servicio['serie_instalada']}</td></tr>
    <tr><td colspan="2"><strong>Serie Retirada:</strong> {$servicio['serie_retiro']}</td></tr>
  </table>

  <table class="sin-borde">
    <tr><td><strong>Recibió el cliente:</strong> ____________________________</td><td><strong>Firma:</strong> ____________________________</td></tr>
  </table>

</body>
</html>
HTML;

// Generar PDF con DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar o mostrar
$filename = "HS_{$servicio['ticket']}.pdf";
$dompdf->stream($filename, ["Attachment" => false]);
exit;
