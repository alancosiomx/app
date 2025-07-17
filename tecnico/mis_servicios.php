<?php
require_once __DIR__ . '/init.php';

if (!isset($pdo)) {
    die("❌ ERROR: Conexión PDO no inicializada");
}

$idc = $_SESSION['usuario_username'] ?? null;
echo "<!-- IDC: '$idc' -->";


if (!$idc) {
    header("Location: /login.php");
    exit;
}

$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';

$stmt = $pdo->prepare("
    SELECT ticket, afiliacion, comercio, ciudad
    FROM servicios_omnipos
    WHERE idc = ? AND estatus = 'En Ruta'
    ORDER BY id DESC
");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layout.php';
