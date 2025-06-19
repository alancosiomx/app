<?php
require_once __DIR__ . '/../config.php';
session_start();

$idc = $_SESSION['usuario_nombre'] ?? '';

// Obtener tickets concluidos por el técnico
$stmt = $pdo->prepare("SELECT ticket, afiliacion, comercio, fecha_cierre FROM servicios_omnipos WHERE idc = ? AND estatus = 'Histórico' ORDER BY fecha_cierre DESC");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inyectar en layout
$contenido = __DIR__ . '/bloques/descargar_hs_lista.php';
include __DIR__ . '/layout_tecnico.php';
