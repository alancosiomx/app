<?php
require_once dirname(__DIR__, 2) . '/admin/init.php';

// Función para resetear contraseña
function hashPassword($plain) {
    return password_hash($plain, PASSWORD_DEFAULT);
}

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, username, password, activo, creado_en) VALUES (?, ?, ?, ?, 1, NOW())");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['email'],
        $_POST['username'],
        hashPassword($_POST['password'] ?? 'uno')
    ]);
    $usuario_id = $pdo->lastInsertId();
    foreach ($_POST['roles'] as $rol) {
        $pdo->prepare("INSERT INTO usuarios_roles (usuario_id, rol) VALUES (?, ?)")->execute([$usuario_id, $rol]);
    }
    header("Location: tecnicos.php");
    exit;
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $pdo->prepare("DELETE FROM usuarios_roles WHERE usuario_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
    header("Location: tecnicos.php");
    exit;
}

// Reset password
if (isset($_GET['reset'])) {
    $id = intval($_GET['reset']);
    $pass = hashPassword("uno");
    $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?")->execute([$pass, $id]);
    header("Location: tecnicos.php");
    exit;
}

// Cargar datos
$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
$roles_usuario = [];
$roles = ['admin', 'idc', 'coordinador', 'finanzas'];
foreach ($pdo->query("SELECT * FROM usuarios_roles") as $rol) {
    $roles_usuario[$rol['usuario_id']][] = $rol['rol'];
}

// Modulariza
$contenido = __DIR__ . '/contenido_tecnicos.php';
require_once dirname(__DIR__, 2) . '/admin/layout.php';
