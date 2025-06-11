<?php
require_once '../../config.php';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico_id = $_GET['tecnico_id'] ?? '';
$ticket_busqueda = $_GET['ticket'] ?? '';

$sql = "SELECT * FROM servicios_omnipos WHERE estatus = 'Histórico' ";
$params = [];

if ($fecha_inicio) {
    $sql .= " AND fecha_atencion >= ? ";
    $params[] = $fecha_inicio . ' 00:00:00';
}
if ($fecha_fin) {
    $sql .= " AND fecha_atencion <= ? ";
    $params[] = $fecha_fin . ' 23:59:59';
}
if ($tecnico_id) {
    $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$tecnico_id]);
    $nombre_tecnico = $stmt->fetchColumn();
    $sql .= " AND idc = ? ";
    $params[] = $nombre_tecnico;
}
if ($ticket_busqueda) {
    $sql .= " AND (ticket LIKE ? OR afiliacion LIKE ?) ";
    $params[] = "%$ticket_busqueda%";
    $params[] = "%$ticket_busqueda%";
}

$sql .= " ORDER BY fecha_atencion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=historico_servicios_" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Ticket</th>
        <th>Afiliación</th>
        <th>Comercio</th>
        <th>Ciudad</th>
        <th>Fecha Atención</th>
        <th>Resultado</th>
        <th>Comentarios</th>
      </tr>";

foreach ($servicios as $s) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($s['ticket']) . "</td>";
    echo "<td>" . htmlspecialchars($s['afiliacion']) . "</td>";
    echo "<td>" . htmlspecialchars($s['comercio']) . "</td>";
    echo "<td>" . htmlspecialchars($s['ciudad']) . "</td>";
    echo "<td>" . htmlspecialchars($s['fecha_atencion']) . "</td>";
    echo "<td>" . htmlspecialchars($s['conclusion']) . "</td>";
    echo "<td>" . htmlspecialchars($s['comentarios']) . "</td>";
    echo "</tr>";
}

echo "</table>";
exit;
