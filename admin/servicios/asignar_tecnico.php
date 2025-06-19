<?php
require_once '../../config.php';
require_once __DIR__ . '/service_functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?tab=por_asignar");
    exit();
}

$tickets = $_POST['tickets'] ?? [];
$tecnico_id = $_POST['tecnico_id'] ?? null;

if (empty($tickets) || !$tecnico_id) {
    $_SESSION['error'] = "Debes seleccionar al menos un ticket y un técnico.";
    header("Location: index.php?tab=por_asignar");
    exit();
}

$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->execute([$tecnico_id]);
$tecnico = $stmt->fetchColumn();

if (!$tecnico) {
    $_SESSION['error'] = "Técnico no válido.";
    header("Location: index.php?tab=por_asignar");
    exit();
}

$asignados = 0;
foreach ($tickets as $ticket) {
    $update = $pdo->prepare("UPDATE servicios_omnipos SET tecnico_id = ?, idc = ?, estatus = 'En Ruta' WHERE ticket = ?");
    $update->execute([$tecnico_id, $tecnico, $ticket]);

    if ($update->rowCount()) {
        logServicio($pdo, $ticket, 'Asignación', $_SESSION['usuario_nombre'], "Asignado a técnico ID $tecnico_id");
        $asignados++;
    }
}

$_SESSION['mensaje'] = "✅ Se asignaron correctamente $asignados servicios.";
header("Location: index.php?tab=por_asignar");
exit();
