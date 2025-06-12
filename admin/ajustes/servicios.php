<?php
require_once __DIR__ . '/../../config.php';
session_start();

// Guardar nueva solución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $banco = trim($_POST['banco']);
    $servicio = trim($_POST['servicio']);
    $solucion = trim($_POST['solucion']);
    $solucion_especifica = trim($_POST['solucion_especifica']);

    if ($banco && $servicio && $solucion && $solucion_especifica) {
        $stmt = $pdo->prepare("INSERT INTO servicio_soluciones (banco, servicio, solucion, solucion_especifica) VALUES (?, ?, ?, ?)");
        $stmt->execute([$banco, $servicio, $solucion, $solucion_especifica]);
    }
}

// Baja lógica
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $pdo->prepare("UPDATE servicio_soluciones SET activo = 0 WHERE id = ?")->execute([$id]);
}

// Obtener registros activos
$soluciones = $pdo->query("SELECT * FROM servicio_soluciones WHERE activo = 1 ORDER BY banco, servicio, solucion")->fetchAll(PDO::FETCH_ASSOC);

$contenido = __DIR__ . '/../../admin/bloques/crud_soluciones_servicios.php';
include __DIR__ . '/../layout.php';
