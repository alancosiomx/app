<?php
require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_POST['ticket'])) {
    echo json_encode(['success' => false, 'message' => 'Ticket no recibido']);
    exit;
}

$ticket = $_POST['ticket'];

try {
    $stmt = $pdo->prepare("UPDATE servicios_omnipos SET 
        estado = 'EN RUTA',
        conclusion = NULL,
        fecha_cierre = NULL,
        sla = NULL
        WHERE ticket = ?");
    $stmt->execute([$ticket]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Servicio reabierto correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró el ticket o ya estaba en ruta.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
