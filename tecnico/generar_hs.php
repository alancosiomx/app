<?php
ob_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

$ticket = $_GET['ticket'] ?? null;
if (!$ticket) {
  die("No se especificó un ticket.");
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
  die("Servicio no encontrado.");
}

// Cargar la plantilla HTML
$template = file_get_contents(__DIR__ . '/plantillas_html/hoja_servicio_bbva.html');

// Reemplazar los campos
$placeholders = [
  '{{ticket}}' => $servicio['ticket'] ?? '',
  '{{afiliacion}}' => $servicio['afiliacion'] ?? '',
  '{{fecha}}' => date('d/m/Y'),
  '{{telefono_contacto_1}}' => $servicio['telefono_contacto_1'] ?? '',
  '{{comercio}}' => $servicio['comercio'] ?? '',
  '{{domicilio}}' => $servicio['domicilio'] ?? '',
  '{{colonia}}' => $servicio['colonia'] ?? '',
  '{{cp}}' => $servicio['cp'] ?? '',
  '{{ciudad}}' => $servicio['ciudad'] ?? '',
  '{{referencia}}' => $servicio['referencia'] ?? '',
  '{{tipo_tpv}}' => $servicio['tipo_tpv'] ?? '',
  '{{vim}}' => $servicio['vim'] ?? '',
  '{{serie_instalada}}' => $servicio['serie_instalada'] ?? '',
  '{{serie_retiro}}' => $servicio['serie_retiro'] ?? '',
  '{{cantidad_insumos}}' => $servicio['cantidad_insumos'] ?? '',
  '{{idc}}' => $servicio['idc'] ?? '',
  '{{servicio}}' => $servicio['servicio'] ?? '',
  '{{comentarios}}' => $servicio['comentarios'] ?? '',
];

$html = strtr($template, $placeholders);

// Generar PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("HS_{$servicio['ticket']}.pdf", ["Attachment" => false]);
ob_end_flush();
exit;
