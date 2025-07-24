<?php
require_once __DIR__ . '/init.php';

if (!isset($pdo)) {
    die("❌ ERROR: Conexión PDO no inicializada");
}

$idc = $_SESSION['usuario_nombre'] ?? null;
echo "<!-- IDC: '$idc' -->";


if (!$idc) {
    header("Location: /login.php");
    exit;
}

$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';
$mensaje = null;
if (isset($_GET['cerrado'])) {
    $mensaje = '✅ Servicio cerrado correctamente.';
}

$stmt = $pdo->prepare("
SELECT ticket, afiliacion, comercio, ciudad, telefono_contacto_1, servicio, vim, fecha_cita, banco
    FROM servicios_omnipos
    WHERE idc = ? AND estatus = 'En Ruta'
    ORDER BY id DESC
");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layout.php';
