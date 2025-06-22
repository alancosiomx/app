<?php
require_once __DIR__ . '/../init.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$current_tab = $_GET['vista'] ?? 'precios';

// Guardar nuevo precio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idc = $_POST['idc'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $resultado = $_POST['resultado'] ?? '';
    $banco = $_POST['banco'] ?? '';
    $monto = $_POST['monto'] ?? 0;
    $comentarios = $_POST['comentarios'] ?? '';

    if ($idc && $servicio && $resultado && $banco && is_numeric($monto)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM precios_idc WHERE idc = ? AND servicio = ? AND resultado = ? AND banco = ?");
        $check->execute([$idc, $servicio, $resultado, $banco]);
        if ($check->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO precios_idc (idc, servicio, resultado, banco, monto, comentarios) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$idc, $servicio, $resultado, $banco, $monto, $comentarios]);
        }
    }
}

// Obtener registros
$precios = $pdo->query("SELECT * FROM precios_idc ORDER BY creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL ORDER BY idc")->fetchAll(PDO::FETCH_COLUMN);
$servicios = $pdo->query("SELECT DISTINCT servicio FROM servicios_omnipos ORDER BY servicio")->fetchAll(PDO::FETCH_COLUMN);
$resultados = ['Exito', 'Rechazo', 'Visita'];
$bancos = $pdo->query("SELECT DISTINCT banco FROM servicios_omnipos ORDER BY banco")->fetchAll(PDO::FETCH_COLUMN);

$contenido = __DIR__ . '/precios.php';
require_once __DIR__ . '/layout.php';
