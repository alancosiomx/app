<?php
require_once __DIR__ . '/../config.php';
session_start();

// Verifica sesión activa
if (!isset($_SESSION['usuario_username'])) {
  header("Location: ../login.php");
  exit;
}

$idc = $_SESSION['usuario_username'];

// Consulta de servicios asignados al técnico en estado En Ruta
$stmt = $pdo->prepare("
  SELECT ticket, afiliacion, comercio, ciudad, fecha_atencion 
  FROM servicios_omnipos 
  WHERE idc = ? AND estatus = 'En Ruta' 
  ORDER BY fecha_atencion DESC
");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Carga la vista como tarjeta
$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';
include __DIR__ . '/layout_tecnico.php';
