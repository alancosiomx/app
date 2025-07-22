<?php
require_once __DIR__ . '/../../../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = $_POST['ticket'] ?? '';
    $resultado = $_POST['resultado'] ?? '';
    $serie_instalada = $_POST['serie_instalada'] ?? null;
    $serie_retiro = $_POST['serie_retiro'] ?? null;
    $observaciones = $_POST['comentarios'] ?? null;
    $atiende = $_POST['recibio_cliente'] ?? '';
    $cerrado_por = $_SESSION['usuario_nombre'] ?? '';
    $reprogramado = 0; // o lógica para determinar si fue reprogramado

    // Evita duplicados
    $verifica = $pdo->prepare("SELECT id FROM cierres_servicio WHERE ticket = ?");
    $verifica->execute([$ticket]);
    if ($verifica->fetch()) {
        echo "❌ Este servicio ya fue cerrado.";
        exit;
    }

    // Guardar cierre
    $stmt = $pdo->prepare("INSERT INTO cierres_servicio 
        (ticket, atiende, resultado, serie_instalada, serie_retirada, reprogramado, observaciones, cerrado_por) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $ticket,
        $atiende,
        $resultado,
        $serie_instalada,
        $serie_retiro,
        $reprogramado,
        $observaciones,
        $cerrado_por
    ]);

    echo "✅ Servicio cerrado correctamente.";
    header("Location: /tecnico/");
    exit;
}
?>
