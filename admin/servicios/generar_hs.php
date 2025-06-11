<?php
require_once '../../config.php';
require_once '../../vendor/autoload.php';

use TCPDF;

// Obtener ticket
$ticket = $_GET['ticket'] ?? null;
if (!$ticket) die("❌ Falta ticket.");

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$servicio) die("❌ Ticket no encontrado.");

// Crear PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('OMNIPOS');
$pdf->SetAuthor('OMNIPOS');
$pdf->SetTitle("Hoja de Servicio - Ticket $ticket");
$pdf->SetMargins(15, 25, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// --- Logo arriba ---
$logo_file = __DIR__ . '/../../assets/img/logo_bbva.png'; // Ajusta ruta y nombre
if (file_exists($logo_file)) {
    $pdf->Image($logo_file, 15, 10, 40); // x, y, ancho en mm
}

// --- Encabezado ---
$pdf->SetXY(60, 10);
$pdf->SetFont('', 'B', 14);
$pdf->Cell(0, 8, "Hoja de Servicio - Ticket: " . $servicio['ticket'], 0, 1);

$pdf->Ln(8);

// --- Datos principales en tabla simple ---
$pdf->SetFont('', '', 10);

$datos = [
    ['Afiliación', $servicio['afiliacion']],
    ['Fecha', $servicio['fecha_inicio']],
    ['Teléfono', $servicio['telefono_contacto_1']],
    ['Comercio', $servicio['comercio']],
    ['Referencia', $servicio['referencia']],
    ['Horario', $servicio['horario']],
    ['Calle y Núm', $servicio['domicilio']],
    ['Colonia', $servicio['colonia']],
    ['C.P.', $servicio['cp']],
    ['Población y Estado', $servicio['ciudad']],
];

foreach ($datos as [$label, $value]) {
    $pdf->SetFont('', 'B');
    $pdf->Cell(40, 7, "$label:", 0, 0);
    $pdf->SetFont('', '');
    $pdf->MultiCell(0, 7, $value ?: '-', 0, 'L');
}

// --- Casilla con X helper ---
function drawCheckBox(TCPDF $pdf, float $x, float $y, bool $checked = false) {
    $pdf->Rect($x, $y, 5, 5);
    if ($checked) {
        $pdf->SetXY($x + 1, $y - 0.5);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->Cell(5, 5, chr(52), 0, 0, 'C'); // Check mark
        $pdf->SetFont('helvetica', '', 10);
    }
}

// --- Tipos de servicio con casillas gráficas ---
$pdf->Ln(6);
$pdf->SetFont('', 'B', 11);
$pdf->Cell(0, 7, "Tipo de Servicio:", 0, 1);

$tipos_servicio = [
    'INSTALACION DE TPV',
    'FALLA DE TPV',
    'RETIRO DE TPV',
    'ENTREGA DE INSUMOS',
    'CAPACITACION',
    'INSTALACION',
    'SUSTITUCION',
    'REPROGRAMACION',
    'ENTREGA DE ROLLOS',
    'ENTREGA DE PUBLICIDAD',
    'CAMBIO DE SIM',
    'CAMBIO DE ELIMINADOR',
    'CAMBIO DE BATERIA',
    'RETIRO',
    'CAPACITACION',
    'INSTALACION S/CAPACITACION'
];

$servicio_upper = strtoupper($servicio['servicio'] ?? '');
$x_start = $pdf->GetX();
$y_start = $pdf->GetY();

$col_width = 60;
$row_height = 7;
$cols = 2;
$count = 0;

foreach ($tipos_servicio as $tipo) {
    $checked = (strpos($servicio_upper, $tipo) !== false);
    $x = $x_start + ($count % $cols) * $col_width;
    $y = $y_start + floor($count / $cols) * $row_height;

    drawCheckBox($pdf, $x, $y, $checked);
    $pdf->SetXY($x + 7, $y);
    $pdf->Cell(0, $row_height, $tipo, 0, 0);

    $count++;
}
$pdf->Ln((ceil($count / $cols)) * $row_height + 4);

// --- Calificación Calidad del Servicio ---
$pdf->SetFont('', 'B', 11);
$pdf->Cell(0, 7, "Calificación de la Calidad del Servicio Técnico:", 0, 1);

$calificaciones = ['MALO', 'BUENO', 'EXCELENTE'];
$nivel_servicio = strtoupper($servicio['nivel_servicio'] ?? '');

$x_start = $pdf->GetX();
$y_start = $pdf->GetY();
$col_width = 40;

foreach ($calificaciones as $calif) {
    $checked = ($nivel_servicio === $calif);
    $x = $x_start;
    $y = $y_start;

    drawCheckBox($pdf, $x, $y, $checked);
    $pdf->SetXY($x + 7, $y);
    $pdf->Cell(0, $row_height, $calif, 0, 0);

    $pdf->Ln($row_height);
}

// --- Espacio para firmas ---
$pdf->Ln(10);
$pdf->Cell(90, 7, "Nombre y firma del Cliente:", 'T', 0, 'C');
$pdf->Cell(90, 7, "Nombre y firma del IDC:", 'T', 1, 'C');

// --- Texto legal al pie ---
$pdf->Ln(15);
$texto_legal = "HACEMOS DE SU CONOCIMIENTO QUE EL SERVICIO QUE USTED RECIBE POR PARTE DE BBVA NO GENERA NINGÚN COSTO ADICIONAL, ASÍ COMO LA TERMINAL Y LOS INSUMOS QUE USTED SOLICITE POR LO QUE EL PERSONAL QUE LO VISITA NO PUEDE REALIZAR NINGÚN COBRO O SOLICITAR ALGÚN PAGO POR EL MISMO.";
$pdf->SetFont('', '', 8);
$pdf->MultiCell(0, 5, $texto_legal, 0, 'C');

// --- Output ---
$pdf->Output("HS_Ticket_{$servicio['ticket']}.pdf", 'I');

