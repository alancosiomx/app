<?php
require '../auth.php';
require '../config.php';
require '../permisos.php';

if (!tienePermiso('asignar_inventario')) {
    die('⛔ Acceso denegado');
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("UPDATE inventario_disponible SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
?>
