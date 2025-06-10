<?php
require('fpdf.php'); // Asegúrate de tener la librería FPDF instalada o descargarla de http://www.fpdf.org/

// Verificar que se reciben datos POST del formulario masivo
if (isset($_POST['movimiento_tipo']) && isset($_POST['detalles_movimiento'])) {
    $movimiento_tipo = $_POST['movimiento_tipo'];
    $detalles_movimiento = json_decode($_POST['detalles_movimiento'], true);

    // Crear un nuevo PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Título
    $pdf->Cell(0, 10, 'Reporte de Movimiento Masivo: ' . $movimiento_tipo, 0, 1, 'C');
    $pdf->Ln(10);

    // Agregar detalles del movimiento
    $pdf->SetFont('Arial', '', 12);
    foreach ($detalles_movimiento as $key => $value) {
        $pdf->Cell(0, 10, utf8_decode("$key: $value"), 0, 1);
    }

    // Nombre del archivo
    $nombre_archivo = 'reporte_' . $movimiento_tipo . '_' . date('YmdHis') . '.pdf';
    $pdf->Output('D', $nombre_archivo);
} else {
    echo "No se han recibido datos para generar el PDF.";
}
?>
