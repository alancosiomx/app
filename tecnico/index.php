<?php
require_once __DIR__ . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Técnico';

// Consulta de servicios activos
$stmt = $pdo->prepare("
  SELECT ticket, afiliacion, comercio, ciudad, fecha_atencion 
  FROM servicios_omnipos 
  WHERE idc = ? AND estatus = 'En Ruta' 
  ORDER BY fecha_atencion DESC
");
$stmt->execute([$_SESSION['usuario_username']]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';

require_once __DIR__ . '/layout_tecnico.php';
