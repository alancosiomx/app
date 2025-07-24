<?php
ob_start();
require_once __DIR__ . '/init.php';

$ticket = $_POST['ticket'] ?? null;
$atiende = trim($_POST['atiende'] ?? '');
$resultado = $_POST['resultado'] ?? '';
$serie_instalada = trim($_POST['serie_instalada'] ?? '');
$serie_retiro = trim($_POST['serie_retirada'] ?? '');
$solucion = trim($_POST['solucion'] ?? '');
$solucion_especifica = trim($_POST['solucion_especifica'] ?? '');
$motivo_rechazo = trim($_POST['motivo_rechazo'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$latitud = $_POST['latitud'] ?? null;
$longitud = $_POST['longitud'] ?? null;
$usuario = $_SESSION['usuario_nombre'] ?? null;

if (!$ticket || !$atiende || !$usuario || !in_array($resultado, ['Éxito', 'Rechazo'])) {
    die("❌ Datos incompletos o inválidos.");
}

// Validación: servicio existe, en ruta, y asignado al técnico
$stmt = $pdo->prepare("
    SELECT * FROM servicios_omnipos
    WHERE ticket = ? AND idc = ? AND estatus = 'En Ruta'
");
$stmt->execute([$ticket, $usuario]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("❌ No autorizado o servicio ya cerrado.");
}

// Validación de serie si es éxito
if ($resultado === 'Éxito') {
    if ($serie_instalada) {
        $val = $pdo->prepare("
            SELECT id FROM inventario_tpv 
            WHERE serie = ? AND estado = 'Asignado' AND tecnico_actual = ?
        ");
        $val->execute([$serie_instalada, $usuario]);
        if (!$val->fetch()) {
            die("⚠️ Esta serie no está disponible en tu inventario.");
        }
    } else {
        die("❌ Debes capturar la serie instalada.");
    }
} else {
    $serie_instalada = null;
    $serie_retiro = null;
    $solucion = null;
    $solucion_especifica = null;
}

// Insertar en tabla cierres
$insert = $pdo->prepare("
    INSERT INTO cierres_servicio 
    (ticket, atiende, resultado, serie_instalada, serie_retirada, solucion, solucion_especifica, motivo_rechazo, observaciones, cerrado_por, fecha_cierre, latitud, longitud)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
");
$insert->execute([
    $ticket,
    $atiende,
    $resultado,
    $serie_instalada ?: null,
    $serie_retiro ?: null,
    $solucion ?: null,
    $solucion_especifica ?: null,
    $motivo_rechazo ?: null,
    $observaciones,
    $usuario,
    $latitud,
    $longitud
]);

// Log temporal del cierre
$pdo->prepare("
    INSERT INTO log_cierre_interactivo (ticket, idc, evento, valor, timestamp)
    VALUES (?, ?, 'cierre_completo', ?, NOW())
")->execute([
    $ticket,
    $usuario,
    json_encode($_POST)
]);

// Actualizar estado en servicios_omnipos
$update = $pdo->prepare("
    UPDATE servicios_omnipos SET
        estatus = 'Histórico',
        resultado = ?,
        atiende = ?,
        serie_instalada = ?,
        serie_retiro = ?,
        solucion = ?,
        solucion_especifica = ?,
        motivo_rechazo = ?,
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
    $solucion ?: null,
    $solucion_especifica ?: null,
    $motivo_rechazo ?: null,
    $observaciones,
    $ticket,
    $usuario
]);

header("Location: mis_servicios.php?cerrado=1");
exit;
