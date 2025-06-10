<?php
require '../../auth.php';
require '../../config.php';

if (!in_array('admin', $_SESSION['usuario_roles'])) {
    header("Location: ../../dashboard.php");
    exit;
}

$id = $_GET['id'] ?? null;
$error = '';
$success = '';

if (!$id) {
    header("Location: index.php");
    exit;
}

// Obtener usuario actual
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Convertir los roles del usuario en un array
$roles_actuales = array_map('trim', explode(',', $usuario['roles']));

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $roles = isset($_POST['roles']) ? implode(',', $_POST['roles']) : '';

    if (empty($roles)) {
        $error = "Debes seleccionar al menos un rol.";
    } elseif (empty($username)) {
        $error = "El campo 'username' es obligatorio.";
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, username = ?, roles = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $email, $username, $roles, $id])) {
            $success = "Usuario actualizado correctamente.";

            // Actualizar array local para que el formulario muestre los nuevos valores
            $usuario['nombre'] = $nombre;
            $usuario['email'] = $email;
            $usuario['username'] = $username;
            $usuario['roles'] = $roles;
            $roles_actuales = explode(',', $roles);
        } else {
            $error = "Error al actualizar el usuario.";
        }
    }
}

require '../../includes/header.php';
require '../../includes/menu.php';
?>

<div class="main-content">
    <h3>Editar Usuario</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario (username)</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Roles</label>
            <?php
            $todos_los_roles = ['tecnico', 'coordinador', 'admin'];
            foreach ($todos_los_roles as $rol) {
                $checked = in_array($rol, $roles_actuales) ? 'checked' : '';
                echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='roles[]' value='$rol' id='rol_$rol' $checked>
                        <label class='form-check-label' for='rol_$rol'>" . ucfirst($rol) . "</label>
                      </div>";
            }
            ?>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</div>

<?php require '../../includes/footer.php'; ?>
