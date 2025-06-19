<?php
require_once __DIR__ . '/../../config.php';

$ticket = $_GET['ticket'] ?? '';
$solucion = $_GET['solucion'] ?? '';

if (!$ticket || !$solucion) {
    echo json_encode([]);
    exit;
}

// Obtener banco y servicio del ticket
$stmt = $pdo->prepare("SELECT banco, servicio FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode([]);
    exit;
}

// Buscar soluciones específicas
$stmt = $pdo->prepare("SELECT solucion_especifica FROM servicio_soluciones WHERE banco = ? AND servicio = ? AND solucion = ? AND activo = 1 ORDER BY solucion_especifica");
$stmt->execute([$row['banco'], $row['servicio'], $solucion]);
$resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($resultados);
