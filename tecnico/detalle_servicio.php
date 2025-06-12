<?php
require_once __DIR__ . '/../config.php';
session_start();

$ticket = $_GET['ticket'] ?? null;

if (!$ticket) {
  echo "Ticket inválido.";
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
  echo "Servicio no encontrado.";
  exit;
}

$contenido = __DIR__ . '/bloques/detalle_servicio_ficha.php';
include __DIR__ . '/layout_tecnico.php';
