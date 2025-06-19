<?php
require '../../auth.php';
require '../../config.php';

if (!in_array('admin', $_SESSION['usuario_roles'])) {
    header("Location: ../../dashboard.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
