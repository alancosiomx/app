<?php
ob_start();
require_once __DIR__ . '/init.php';

$ticket = $_POST['ticket'] ?? null;
$atiende = trim($_POST['atiende'] ?? '');
$resultado = $_POST['resultado'] ?? '';
$serie_instalada = trim($_POST['serie_instalada'] ?? '');
$serie_retiro = trim($_POST['serie_retirada'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$usuario = $_SESSION['usuario_nombre'] ?? null;

if (!$ticket || !$atiende || !$usuario || !in_array($resultado, ['Éxito', 'Rechazo'])) {
    die("❌ Datos incompletos o inválidos.");
}

$stmt = $pdo->prepare("
    SELECT * FROM servicios_omnipos
    WHERE ticket = ? AND idc = ? AND estatus = 'En Ruta'
");
$stmt->execute([$ticket, $usuario]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("❌ No autorizado o servicio ya cerrado.");
}

$update = $pdo->prepare("
    UPDATE servicios_omnipos
    SET estatus = 'Histórico',
        resultado = ?,
        atiende = ?,
        serie_instalada = ?,
        serie_retiro = ?,
        comentarios = ?,
        fecha_atencion = NOW(),
        fecha_cierre = NOW()
    WHERE ticket = ? AND idc = ?
");
$update->execute([
    $resultado,
    $atiende,
    $serie_instalada,
    $serie_retiro,
    $observaciones,
    $ticket,
    $usuario
]);

header("Location: mis_servicios.php?cerrado=1");
exit;
