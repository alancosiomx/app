<?php
require_once __DIR__ . '/../init.php';

$usuario = $_SESSION['usuario_nombre'] ?? '';
if (!$usuario) {
  header('Location: /login.php');
  exit;
}

// Obtener alertas del admin
$stmt = $pdo->prepare("
  SELECT a.id, a.mensaje, a.fecha_creacion,
    (SELECT 1 FROM alertas_leidas WHERE id_alerta = a.id AND usuario = ?) AS leida
  FROM alertas a
  WHERE a.destinatario IS NULL OR a.destinatario = ?
  ORDER BY a.fecha_creacion DESC
");
$stmt->execute([$usuario, $usuario]);
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Marcar como leídas (solo si aún no estaban leídas)
$noLeidas = array_filter($alertas, fn($a) => !$a['leida']);
if (!empty($noLeidas)) {
  $stmtInsert = $pdo->prepare("INSERT IGNORE INTO alertas_leidas (id_alerta, usuario) VALUES (?, ?)");
  foreach ($noLeidas as $alerta) {
    $stmtInsert->execute([$alerta['id'], $usuario]);
  }
}

$contenido = __DIR__ . '/_contenido_alertas.php';
include __DIR__ . '/../layouts/tecnico_layout.php'; // O como se llame tu layout
