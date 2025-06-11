<?php
require_once __DIR__ . '/../init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $banco = trim($_POST['banco']);
    $series = preg_split('/\r\n|\r|\n/', trim($_POST['series']));
    $insertadas = 0;
    $rechazadas = 0;

    foreach ($series as $serie) {
        $serie = trim($serie);
        if ($serie === '') continue;

        $existe = $pdo->prepare("SELECT COUNT(*) FROM inventario_tpv WHERE serie = ?");
        $existe->execute([$serie]);

        if ($existe->fetchColumn() > 0) {
            $rechazadas++;
            continue;
        }

        $stmt = $pdo->prepare("INSERT INTO inventario_tpv (serie, banco, estado, fecha_entrada) VALUES (?, ?, 'Disponible', NOW())");
        $stmt->execute([$serie, $banco]);
        $insertadas++;
    }

    $mensaje = "Se ingresaron $insertadas nuevas TPVs.";
    if ($rechazadas > 0) $mensaje .= " $rechazadas fueron rechazadas por duplicadas.";

    header("Location: index.php?msg=" . urlencode($mensaje));
    exit;
}

$contenido = __DIR__ . '/contenido_nuevo.php';
require_once __DIR__ . '/../layout.php';
