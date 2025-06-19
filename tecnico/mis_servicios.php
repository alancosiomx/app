<?php
require_once __DIR__ . '/../config.php';
session_start();

$idc = $_SESSION['usuario_nombre'] ?? '';

// Consulta de servicios asignados al técnico en estado EN RUTA
$stmt = $pdo->prepare("SELECT ticket, afiliacion, comercio, ciudad, fecha_atencion FROM servicios_omnipos WHERE idc = ? AND estatus = 'En Ruta' ORDER BY fecha_atencion DESC");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inyectamos en el layout
$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';
include __DIR__ . '/layout_tecnico.php';
