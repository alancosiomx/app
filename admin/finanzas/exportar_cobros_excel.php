<?php
require_once __DIR__ . '/../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desde = $_POST['desde'] . ' 00:00:00';
    $hasta = $_POST['hasta'] . ' 23:59:59';
    $tecnico_filtro = $_POST['tecnico'] ?? '';

    $sql = "SELECT s.ticket, s.comercio, s.idc, s.servicio, s.resultado, s.sla, s.fecha_atencion, 
                   p.monto AS pago 
            FROM servicios_omnipos s
            LEFT JOIN precios_idc p ON s.idc = p.idc AND s.servicio = p.servicio AND s.resultado = p.resultado AND s.banco = p.banco
            WHERE s.resultado IN ('Exito', 'Rechazo') 
            AND s.fecha_atencion BETWEEN ? AND ?";

    $params = [$desde, $hasta];

    if (!empty($tecnico_filtro)) {
        $sql .= " AND s.idc = ?";
        $params[] = $tecnico_filtro;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        exit("No hay datos para exportar.");
    }

    // Agrupar por técnico
    $agrupado = [];
    foreach ($rows as $row) {
        $agrupado[$row['idc']][] = $row;
    }

    $spreadsheet = new Spreadsheet();

    // Hoja 1: RESUMEN
    $resumen = $spreadsheet->getActiveSheet();
    $resumen->setTitle('Resumen');
    $resumen->fromArray(['Técnico', 'Servicios', 'Total Pago'], null, 'A1');

    $fila = 2;
    foreach ($agrupado as $idc => $registros) {
        $total = count($registros);
        $pago_total = array_sum(array_column($registros, 'pago'));
        $resumen->setCellValue("A{$fila}", $idc);
        $resumen->setCellValue("B{$fila}", $total);
        $resumen->setCellValue("C{$fila}", $pago_total);
        $fila++;
    }

    // Hojas por técnico
    foreach ($agrupado as $idc => $datos) {
        $hoja = $spreadsheet->createSheet();
        $hoja->setTitle(substr($idc, 0, 31)); // Max 31 caracteres

        $hoja->fromArray(['Ticket', 'Comercio', 'Servicio', 'Resultado', 'SLA', 'Fecha Atención', 'Pago'], null, 'A1');
        $fila = 2;
        foreach ($datos as $d) {
            $hoja->setCellValue("A{$fila}", $d['ticket']);
            $hoja->setCellValue("B{$fila}", $d['comercio']);
            $hoja->setCellValue("C{$fila}", $d['servicio']);
            $hoja->setCellValue("D{$fila}", $d['resultado']);
            $hoja->setCellValue("E{$fila}", $d['sla']);
            $hoja->setCellValue("F{$fila}", $d['fecha_atencion']);
            $hoja->setCellValue("G{$fila}", $d['pago']);
            $fila++;
        }
    }

    // Descargar archivo
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="reporte_cobros.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
