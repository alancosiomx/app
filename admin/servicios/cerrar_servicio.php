<?php
require_once '../../config.php';
require_once __DIR__ . '/service_functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?tab=en_ruta");
    exit();
}

$tickets = $_POST['tickets'] ?? [];
$resultado = trim($_POST['resultado'] ?? '');

if (empty($tickets) || !in_array($resultado, ['Exito', 'Rechazo', 'Reasignar', 'Cancelado'])) {
    $_SESSION['error'] = "Debes seleccionar al menos un ticket y un resultado válido.";
    header("Location: index.php?tab=en_ruta");
    exit();
}

$actualizados = 0;

foreach ($tickets as $ticket) {
    if ($resultado === 'Reasignar') {
        $sql = "UPDATE servicios_omnipos SET estatus = 'Por Asignar', tecnico_id = NULL, idc = NULL WHERE ticket = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ticket]);
        logServicio($pdo, $ticket, 'Reasignación', $_SESSION['usuario_nombre'], "Devuelto a Por Asignar");
    } else {
        $sql = "UPDATE servicios_omnipos SET estatus = 'Histórico', resultado = ?, fecha_atencion = NOW() WHERE ticket = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$resultado, $ticket]);
        logServicio($pdo, $ticket, 'Cierre', $_SESSION['usuario_nombre'], "Resultado: $resultado");
    }

    $actualizados++;
}

$_SESSION['mensaje'] = "✅ Se procesaron $actualizados servicios correctamente.";
header("Location: index.php?tab=en_ruta");
exit();
