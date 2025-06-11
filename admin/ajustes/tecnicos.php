<?php
require_once dirname(__DIR__, 2) . '/admin/init.php';

// Función para resetear contraseña
function hashPassword($plain) {
    return password_hash($plain, PASSWORD_DEFAULT);
}

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
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

// Restablecer contraseña
if (isset($_GET['reset'])) {
    $id = intval($_GET['reset']);
    $pass = hashPassword("uno");
    $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?")->execute([$pass, $id]);
    header("Location: tecnicos.php");
    exit;
}

// Obtener usuarios
$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

// Obtener roles por usuario
$roles_usuario = [];
$roles = ['admin', 'idc', 'coordinador', 'finanzas'];
$roles_stmt = $pdo->query("SELECT * FROM usuarios_roles");
foreach ($roles_stmt as $rol) {
    $roles_usuario[$rol['usuario_id']][] = $rol['rol'];
}
?>

<!DOCTYPE html>
if (!defined('HEAD') || !defined('MENU') || !defined('FOOT')) {
    require_once dirname(__DIR__, 2) . '/config.php';
}
require HEAD;
require MENU;
?>

<div class="main-content">
    <h2>👨‍🔧 Gestión de Técnicos / Usuarios</h2>

    <div class="card mt-4">
    <h2>👨‍🔧 Gestión de Técnicos / Usuarios</h2>

    <div class="card mt-4">
        <div class="card-header">Crear nuevo usuario</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="row mb-2">
                    <div class="col">
                        <input name="nombre" required class="form-control" placeholder="Nombre completo">
                    </div>
                    <div class="col">
                        <input name="email" required type="email" class="form-control" placeholder="Correo electrónico">
                    </div>
                    <div class="col">
                        <input name="username" required class="form-control" placeholder="Usuario">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="form-label">Roles</label>
                        <?php foreach ($roles as $rol): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $rol ?>">
                                <label class="form-check-label"><?= ucfirst($rol) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <input name="password" class="form-control" placeholder="Contraseña (opcional, por defecto 'uno')">
                    </div>
                    <div class="col text-end">
                        <button class="btn btn-success">Crear Usuario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr>

    <table class="table table-bordered table-striped mt-4 bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Usuario</th>
                <th>Activo</th>
                <th>Roles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= $u['activo'] ? '✅' : '❌' ?></td>
                <td>
                    <?php
                    $roles_str = $roles_usuario[$u['id']] ?? [];
                    echo implode(', ', $roles_str);
                    ?>
                </td>
                <td>
                    <a href="?reset=<?= $u['id'] ?>" class="btn btn-sm btn-warning" title="Reset password">🔑</a>
                    <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>
<?php require FOOT; ?>
