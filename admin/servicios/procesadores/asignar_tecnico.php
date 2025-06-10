<?php
ob_start(); // Iniciar buffer de salida

require_once __DIR__ . '/../../../config.php'; // Ruta correcta al config
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tecnico_id = trim($_POST['tecnico_id']);
    $tickets = $_POST['tickets'] ?? [];

    if (empty($tecnico_id) || empty($tickets)) {
        echo "Faltan datos para procesar.";
        exit;
    }

    // Buscar nombre del técnico
    $stmtTecnico = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmtTecnico->execute([$tecnico_id]);
    $tecnicoData = $stmtTecnico->fetch(PDO::FETCH_ASSOC);

    if (!$tecnicoData) {
        echo "Técnico no encontrado.";
        exit;
    }

    $nombreTecnico = $tecnicoData['nombre'];

    // Actualizar servicios
    $inQuery = implode(',', array_fill(0, count($tickets), '?'));
    $params = array_merge([$nombreTecnico], $tickets);

    $stmt = $pdo->prepare("UPDATE servicios_omnipos SET idc = ?, estatus = 'En Ruta' WHERE ticket IN ($inQuery)");
    $stmt->execute($params);

    // 🚀 Redirigir después de asignar
    header("Location: " . BASE_URL . "/admin/servicios/servicios.php?tab=por_asignar&success=1");
    exit;
}

ob_end_flush(); // Finalizar buffer de salida
?>
