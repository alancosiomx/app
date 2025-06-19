<?php
// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Crear banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO bancos (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header("Location: bancos.php");
    exit;
}

// Editar banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $pdo->prepare("UPDATE bancos SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
    header("Location: bancos.php");
    exit;
}

// Eliminar banco
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $pdo->prepare("DELETE FROM bancos WHERE id = ?")->execute([$id]);
    header("Location: bancos.php");
    exit;
}

// Cargar bancos
$bancos = $pdo->query("SELECT * FROM bancos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h5>🏦 Bancos</h5>

<form method="post" class="mb-3 d-flex gap-2">
    <input type="hidden" name="accion" value="crear">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="text" name="nombre" class="form-control" placeholder="Nuevo banco" required>
    <button type="submit" class="btn btn-primary">Agregar</button>
</form>

<table class="table table-sm table-bordered">
    <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
    <tbody>
        <?php foreach ($bancos as $banco): ?>
            <tr>
                <td><?= $banco['id'] ?></td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="id" value="<?= $banco['id'] ?>">
                        <input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($banco['nombre']) ?>" required>
                        <button class="btn btn-sm btn-success">💾</button>
                        <a href="?eliminar=<?= $banco['id'] ?>" onclick="return confirm('¿Eliminar banco?')" class="btn btn-sm btn-danger">🗑️</a>
                    </form>
                </td>
                <td></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
