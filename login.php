<?php
ob_start(); // Activa output buffering para evitar errores de headers

// Activar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();
require_once 'config.php'; // Tu archivo de conexión PDO

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Token CSRF inválido.';
    } else {
        $user_or_email = trim(strtolower($_POST['user_or_email'] ?? ''));
        $pass = $_POST['password'] ?? '';

        if (!empty($user_or_email) && !empty($pass)) {
        // Buscar por username O email
        $stmt = $pdo->prepare("
            SELECT * FROM usuarios 
            WHERE (LOWER(username) = :user_or_email_username OR LOWER(email) = :user_or_email_email)
            AND activo = 1 
            LIMIT 1
        ");
        $stmt->execute([
            'user_or_email_username' => $user_or_email,
            'user_or_email_email' => $user_or_email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password'])) {
    // Sesión segura
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nombre'] = $user['nombre'];
    $_SESSION['usuario_username'] = $user['username'];
    $_SESSION['usuario_email'] = $user['email'];
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['initiated'] = true;

    // Permiso especial
    $_SESSION['puede_viaticos'] = (int) ($user['puede_viaticos'] ?? 0);

    // Obtener roles
    $rol_stmt = $pdo->prepare("SELECT rol FROM usuarios_roles WHERE usuario_id = ?");
    $rol_stmt->execute([$user['id']]);
    $roles = $rol_stmt->fetchAll(PDO::FETCH_COLUMN);
    $_SESSION['usuario_roles'] = $roles;

            // Redirección según rol
            if (in_array('admin', $roles)) {
                header('Location: ../admin/');
            } elseif (in_array('idc', $roles)) {
                header('Location: ../tecnico/');
            } elseif (in_array('coordinador', $roles)) {
                header('Location: ../coordinador/home.php');
            } elseif (in_array('finanzas', $roles)) {
                header('Location: ../finanzas/pagos.php');
            } else {
                $error = "No tienes un rol asignado. Contacta al administrador.";
            }
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - OPERAVISE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h3 class="text-center mb-4">Iniciar sesión en <strong>OPERAVISE</strong></h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="user_or_email" class="form-label">Usuario o correo</label>
                    <input type="text" class="form-control" name="user_or_email" id="user_or_email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
