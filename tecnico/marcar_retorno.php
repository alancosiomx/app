<?php
require_once __DIR__ . '/../config.php';
session_start();

$serie = $_POST['serie'] ?? '';
$tecnico = $_SESSION['usuario_nombre'] ?? '';

if ($serie) {
    $stmt = $pdo->prepare("UPDATE inventario_tpv SET observaciones = CONCAT('[RETORNO] ', IFNULL(observaciones, '')) WHERE serie = ? AND tecnico_actual = ?");
    $stmt->execute([$serie, $tecnico]);
}

header("Location: inventario.php?estado=Dañado");
exit;
