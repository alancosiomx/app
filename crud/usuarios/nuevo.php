<?php
require '../../auth.php';
require '../../config.php';

// Verifica si el usuario tiene el rol 'admin'
if (!in_array('admin', $_SESSION['usuario_roles'])) {
    header("Location: ../../dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roles = isset($_POST['roles']) ? implode(',', $_POST['roles']) : '';

    if (empty($roles)) {
        $error = "Debes seleccionar al menos un rol.";
    } elseif (empty($username)) {
        $error = "El campo 'username' es obligatorio.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, username, password, roles) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $email, $username, $password, $roles])) {
                $success = "✅ Usuario creado correctamente.";
            } else {
                $error = "❌ Error al crear el usuario.";
            }
        } catch (PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}

require '../../includes/header.php';
require '../../includes/menu.php';
?>

<div class="main-content">
    <h3>Nuevo Usuario</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario (username)</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Roles</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="tecnico" id="rol_tecnico">
                <label class="form-check-label" for="rol_tecnico">Técnico</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="coordinador" id="rol_coordinador">
                <label class="form-check-label" for="rol_coordinador">Coordinador</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="admin" id="rol_admin">
                <label class="form-check-label" for="rol_admin">Administrador</label>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require '../../includes/footer.php'; ?>
