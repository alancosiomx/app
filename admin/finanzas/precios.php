<?php
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$current_tab = $_GET['vista'] ?? 'precios';

// Validación de seguridad para formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || ($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
        die("Error de seguridad. Por favor recarga la página.");
    }

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

// Generar CSRF token si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener registros
$precios = $pdo->query("SELECT * FROM precios_idc ORDER BY creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL ORDER BY idc")->fetchAll(PDO::FETCH_COLUMN);
$servicios = $pdo->query("SELECT DISTINCT servicio FROM servicios_omnipos ORDER BY servicio")->fetchAll(PDO::FETCH_COLUMN);
$resultados = ['Exito', 'Rechazo', 'Visita'];
$bancos = $pdo->query("SELECT DISTINCT banco FROM servicios_omnipos ORDER BY banco")->fetchAll(PDO::FETCH_COLUMN);

$csrf_input = '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

$contenido = __DIR__ . '/precios.php';
require_once __DIR__ . '/layout.php';
