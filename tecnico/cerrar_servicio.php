<?php
require_once __DIR__ . '/../init.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Método no permitido.");
}

$ticket = $_POST['ticket'] ?? null;
$atiende = $_POST['atiende'] ?? null;
$resultado = $_POST['resultado'] ?? null;
$serie_instalada = $_POST['serie_instalada'] ?? null;
$serie_retirada = $_POST['serie_retirada'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$cerrado_por = $_SESSION['usuario_nombre'] ?? 'sistema';

if (!$ticket || !$atiende || !$resultado) {
    die("❌ Faltan datos obligatorios.");
}

// Evita cierres duplicados
$check = $pdo->prepare("SELECT id FROM cierres_servicio WHERE ticket = ?");
$check->execute([$ticket]);
if ($check->fetch()) {
    die("⚠️ Este servicio ya fue cerrado previamente.");
}

// Guardar cierre
$stmt = $pdo->prepare("
    INSERT INTO cierres_servicio 
    (ticket, atiende, resultado, serie_instalada, serie_retirada, observaciones, cerrado_por) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $ticket,
    $atiende,
    $resultado,
    $serie_instalada,
    $serie_retirada,
    $observaciones,
    $cerrado_por
]);

// (Opcional) Marcar como Histórico en servicios_omnipos
$pdo->prepare("UPDATE servicios_omnipos SET estatus = 'Histórico' WHERE ticket = ?")->execute([$ticket]);

header("Location: /app/tecnico/?cierre=ok");
exit;
