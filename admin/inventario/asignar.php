<?php
require_once __DIR__ . '/../init.php';

// Seguridad CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
    die("❌ Token inválido.");
}

$tipo = $_POST['tipo'] ?? '';
$series = array_map('trim', explode("\n", $_POST['series'] ?? ''));
$tecnico = trim($_POST['tecnico'] ?? '');
$usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';

if (!in_array($tipo, ['tpv', 'sim']) || !$tecnico || empty($series)) {
    die("❌ Datos incompletos.");
}

// Tablas dinámicas según tipo
$tabla = $tipo === 'tpv' ? 'inventario_tpv' : 'inventario_sims';
$campo_serie = $tipo === 'tpv' ? 'serie' : 'serie_sim';
$log_tabla = $tipo === 'tpv' ? 'log_inventario' : 'log_inventario_sims';

// Preparar queries
$stmt = $pdo->prepare("UPDATE $tabla SET estado = 'Asignado', tecnico_actual = ?, fecha_ultimo_movimiento = NOW() WHERE $campo_serie = ? AND estado = 'Disponible'");
$log = $pdo->prepare("INSERT INTO $log_tabla ($campo_serie, tipo_movimiento, usuario, fecha, observaciones) VALUES (?, 'Asignación', ?, NOW(), ?)");

// Ejecutar asignación
$asignados = 0;
foreach ($series as $serie) {
    $serie = strtoupper($serie);
    $stmt->execute([$tecnico, $serie]);
    if ($stmt->rowCount()) {
        $log->execute([$serie, $usuario, "Asignado a $tecnico"]);
        $asignados++;
    }
}

$_SESSION['mensaje'] = "✅ Se asignaron correctamente $asignados $tipo(s).";
header("Location: index.php");
exit;
