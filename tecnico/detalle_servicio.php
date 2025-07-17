<?php
require_once __DIR__ . '/init.php';

$ticket = $_GET['ticket'] ?? null;

if (!$ticket) {
    die("❌ Ticket no proporcionado.");
}

// Obtener el servicio actual
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("❌ Servicio no encontrado.");
}

$contenido = __DIR__ . '/bloques/detalle_servicio_contenido.php';

require_once __DIR__ . '/layout.php';
