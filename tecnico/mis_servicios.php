<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

if (!isset($pdo)) {
    die("❌ ERROR: Conexión PDO no inicializada");
}

$idc = $_SESSION['usuario_username'] ?? null;

if (!$idc) {
    header("Location: /login.php");
    exit;
}

$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';

// Consulta de servicios activos del técnico
$stmt = $pdo->prepare("
    SELECT ticket, afiliacion, comercio, ciudad, fecha_atencion 
    FROM servicios_omnipos 
    WHERE idc = ? AND estatus = 'En Ruta' 
    ORDER BY fecha_atencion DESC
");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Renderiza layout con contenido
require_once __DIR__ . '/layout.php';
