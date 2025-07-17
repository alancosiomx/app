<?php
require_once __DIR__ . '/init.php';

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('❌ Método no permitido.');
}

$ticket = $_POST['ticket'] ?? null;
$usuario = $_SESSION['usuario_username'] ?? null;
echo "<!-- Usuario actual: $usuario -->";
echo "<!-- Ticket recibido: $ticket -->";



if (!$ticket || !$usuario) {
    http_response_code(400);
    exit('❌ Faltan datos.');
}

// Verificar que el ticket esté asignado al técnico y siga en ruta
$stmt = $pdo->prepare("
    SELECT * FROM servicios_omnipos
    WHERE ticket = ? AND idc = ? AND estatus = 'En Ruta'
");
$stmt->execute([$ticket, $usuario]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    http_response_code(403);
    exit('❌ No tienes permiso para cerrar este servicio, o ya fue concluido.');
}

// Actualizar estatus y registrar fecha de atención
$update = $pdo->prepare("
    UPDATE servicios_omnipos
    SET estatus = 'Concluido',
        fecha_atencion = NOW()
    WHERE ticket = ?
");
$update->execute([$ticket]);

// Redirigir de vuelta con mensaje opcional (flash)
header("Location: mis_servicios.php?cerrado=1");
exit;
