<?php
require_once __DIR__ . '/../init.php';

header('Content-Type: application/json');

$idc = $_GET['idc'] ?? null;

if ($idc) {
  $stmt = $pdo->prepare("SELECT ticket FROM servicios_omnipos WHERE idc = ? ORDER BY fecha_inicio DESC");
  $stmt->execute([$idc]);
  $tickets = $stmt->fetchAll(PDO::FETCH_COLUMN);
  echo json_encode($tickets);
} else {
  echo json_encode([]);
}
