<?php
require_once '../../config.php';
require_once __DIR__ . '/service_functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?tab=por_asignar");
    exit();
}

$tickets = $_POST['tickets'] ?? [];
$tecnico_id = $_POST['tecnico_id'] ?? null;

if (empty($tickets) || !$tecnico_id) {
    $_SESSION['error'] = "❌ Debes seleccionar al menos un ticket y un técnico.";
    header("Location: index.php?tab=por_asignar");
    exit();
}

// Validar técnico
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->execute([$tecnico_id]);
$tecnico_nombre = $stmt->fetchColumn();

if (!$tecnico_nombre) {
    $_SESSION['error'] = "❌ Técnico no válido.";
    header("Location: index.php?tab=por_asignar");
    exit();
}

$asignados = 0;

foreach ($tickets as $ticket) {
    $ticket = trim($ticket);
    if (!$ticket) continue;

    $update = $pdo->prepare("UPDATE servicios_omnipos SET tecnico_id = ?, idc = ?, estatus = 'En Ruta' WHERE ticket = ?");
    $update->execute([$tecnico_id, $tecnico_nombre, $ticket]);

    if ($update->rowCount()) {
        logServicio($pdo, $ticket, 'Asignación', $_SESSION['usuario_nombre'] ?? 'Sistema', "Asignado a $tecnico_nombre (ID $tecnico_id)");
        $asignados++;
    }
}

$_SESSION['mensaje'] = "✅ Se asignaron correctamente $asignados servicios a $tecnico_nombre.";
header("Location: index.php?tab=por_asignar");
exit();
