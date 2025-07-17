<?php
require_once __DIR__ . '/init.php';

$ticket = $_GET['ticket'] ?? null;

if (!$ticket) {
    die("❌ Ticket no proporcionado.");
}

// Obtener el servicio actual
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("❌ Servicio no encontrado.");
}

$afiliacion = $servicio['afiliacion'];

// Obtener historial de otros servicios concluidos con la misma afiliación
$stmt_hist = $pdo->prepare("
    SELECT fecha_atencion, telefono_contacto_1, comentarios, horario
    FROM servicios_omnipos
    WHERE afiliacion = ? AND estatus = 'Concluido' AND ticket != ?
    ORDER BY fecha_atencion DESC
");
$stmt_hist->execute([$afiliacion, $ticket]);
$historial = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

$contenido = __DIR__ . '/bloques/detalle_servicio_con_historial.php';

require_once __DIR__ . '/layout.php';
