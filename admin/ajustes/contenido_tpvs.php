<?php
// Generar token si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Crear fabricante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear_fabricante') {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO fabricantes (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header("Location: tpvs.php");
    exit;
}

// Crear modelo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear_modelo') {
    $nombre = trim($_POST['nombre']);
    $fabricante_id = intval($_POST['fabricante_id']);
    if ($nombre !== '' && $fabricante_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO modelos (nombre, fabricante_id) VALUES (?, ?)");
        $stmt->execute([$nombre, $fabricante_id]);
    }
    header("Location: tpvs.php");
    exit;
}

// Eliminar fabricante
if (isset($_GET['eliminar_fabricante'])) {
    $id = intval($_GET['eliminar_fabricante']);
    $pdo->prepare("DELETE FROM fabricantes WHERE id = ?")->execute([$id]);
    header("Location: tpvs.php");
    exit;
}

// Eliminar modelo
if (isset($_GET['eliminar_modelo'])) {
    $id = intval($_GET['eliminar_modelo']);
    $pdo->prepare("DELETE FROM modelos WHERE id = ?")->execute([$id]);
    header("Location: tpvs.php");
    exit;
}

// Editar fabricante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar_fabricante') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $pdo->prepare("UPDATE fabricantes SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
    header("Location: tpvs.php");
    exit;
}

// Editar modelo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar_modelo') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $fabricante_id = intval($_POST['fabricante_id']);
    $pdo->prepare("UPDATE modelos SET nombre = ?, fabricante_id = ? WHERE id = ?")->execute([$nombre, $fabricante_id, $id]);
    header("Location: tpvs.php");
    exit;
}

// Cargar datos
$fabricantes = $pdo->query("SELECT * FROM fabricantes ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$modelos = $pdo->query("
    SELECT m.id, m.nombre AS modelo, f.nombre AS fabricante, m.fabricante_id
    FROM modelos m
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre, m.nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-6">
        <h5>📦 Fabricantes</h5>
        <form method="post" class="mb-3 d-flex gap-2">
            <input type="hidden" name="accion" value="crear_fabricante">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="nombre" class="form-control" placeholder="Nuevo fabricante" required>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>

        <table class="table table-sm table-bordered">
            <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($fabricantes as $fab): ?>
                    <tr>
                        <td><?= $fab['id'] ?></td>
                        <td>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="accion" value="editar_fabricante">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id" value="<?= $fab['id'] ?>">
                                <input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($fab['nombre']) ?>" required>
                                <button class="btn btn-sm btn-success">💾</button>
                                <a href="?eliminar_fabricante=<?= $fab['id'] ?>" onclick="return confirm('¿Eliminar fabricante?')" class="btn btn-sm btn-danger">🗑️</a>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h5>📋 Modelos</h5>
        <form method="post" class="mb-3">
            <input type="hidden" name="accion" value="crear_modelo">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-2">
                <input type="text" name="nombre" class="form-control" placeholder="Nuevo modelo" required>
            </div>
            <div class="mb-2">
                <select name="fabricante_id" class="form-select" required>
                    <option value="">Selecciona fabricante</option>
                    <?php foreach ($fabricantes as $fab): ?>
                        <option value="<?= $fab['id'] ?>"><?= htmlspecialchars($fab['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>

        <table class="table table-sm table-bordered">
            <thead><tr><th>ID</th><th>Modelo</th><th>Fabricante</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($modelos as $mod): ?>
                    <tr>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="accion" value="editar_modelo">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id" value="<?= $mod['id'] ?>">
                            <td><?= $mod['id'] ?></td>
                            <td><input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($mod['modelo']) ?>" required></td>
                            <td>
                                <select name="fabricante_id" class="form-select form-select-sm" required>
                                    <?php foreach ($fabricantes as $fab): ?>
                                        <option value="<?= $fab['id'] ?>" <?= $fab['id'] == $mod['fabricante_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fab['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success">💾</button>
                                <a href="?eliminar_modelo=<?= $mod['id'] ?>" onclick="return confirm('¿Eliminar modelo?')" class="btn btn-sm btn-danger">🗑️</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
