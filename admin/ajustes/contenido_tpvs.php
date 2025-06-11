<?php
// --- Crear fabricante ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_fabricante'])) {
    $nombre = trim($_POST['nombre_fabricante']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO fabricantes (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header("Location: tpvs.php");
    exit;
}

// --- Crear modelo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_modelo'])) {
    $nombre = trim($_POST['nombre_modelo']);
    $fabricante_id = (int) $_POST['fabricante_id'];
    if ($nombre !== '' && $fabricante_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO modelos (nombre, fabricante_id) VALUES (?, ?)");
        $stmt->execute([$nombre, $fabricante_id]);
    }
    header("Location: tpvs.php");
    exit;
}

// --- Eliminar fabricante ---
if (isset($_GET['eliminar_fabricante'])) {
    $id = (int) $_GET['eliminar_fabricante'];
    $pdo->prepare("DELETE FROM fabricantes WHERE id = ?")->execute([$id]);
    header("Location: tpvs.php");
    exit;
}

// --- Eliminar modelo ---
if (isset($_GET['eliminar_modelo'])) {
    $id = (int) $_GET['eliminar_modelo'];
    $pdo->prepare("DELETE FROM modelos WHERE id = ?")->execute([$id]);
    header("Location: tpvs.php");
    exit;
}

// --- Editar fabricante ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_fabricante'])) {
    $id = (int) $_POST['fabricante_id'];
    $nombre = trim($_POST['nombre_editado']);
    $pdo->prepare("UPDATE fabricantes SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
    header("Location: tpvs.php");
    exit;
}

// --- Editar modelo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_modelo'])) {
    $id = (int) $_POST['modelo_id'];
    $nombre = trim($_POST['modelo_editado']);
    $fabricante_id = (int) $_POST['nuevo_fabricante_id'];
    $pdo->prepare("UPDATE modelos SET nombre = ?, fabricante_id = ? WHERE id = ?")->execute([$nombre, $fabricante_id, $id]);
    header("Location: tpvs.php");
    exit;
}

// Obtener fabricantes y modelos actualizados
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

        <!-- Formulario nuevo -->
        <form method="post" class="mb-3">
            <input type="hidden" name="nuevo_fabricante" value="1">
            <div class="input-group">
                <input type="text" name="nombre_fabricante" class="form-control" placeholder="Nuevo fabricante" required>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>

        <table class="table table-sm table-bordered">
            <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($fabricantes as $fab): ?>
                    <tr>
                        <td><?= $fab['id'] ?></td>
                        <td>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="fabricante_id" value="<?= $fab['id'] ?>">
                                <input type="text" name="nombre_editado" class="form-control form-control-sm" value="<?= htmlspecialchars($fab['nombre']) ?>" required>
                                <button name="editar_fabricante" class="btn btn-sm btn-success">💾</button>
                                <a href="?eliminar_fabricante=<?= $fab['id'] ?>" onclick="return confirm('¿Eliminar fabricante?')" class="btn btn-sm btn-danger">🗑️</a>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h5>📋 Modelos</h5>

        <!-- Formulario nuevo -->
        <form method="post" class="mb-3">
            <input type="hidden" name="nuevo_modelo" value="1">
            <div class="mb-2">
                <input type="text" name="nombre_modelo" class="form-control" placeholder="Nuevo modelo" required>
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
                        <form method="post">
                            <td><?= $mod['id'] ?></td>
                            <td>
                                <input type="text" name="modelo_editado" class="form-control form-control-sm" value="<?= htmlspecialchars($mod['modelo']) ?>" required>
                            </td>
                            <td>
                                <select name="nuevo_fabricante_id" class="form-select form-select-sm">
                                    <?php foreach ($fabricantes as $fab): ?>
                                        <option value="<?= $fab['id'] ?>" <?= $fab['id'] == $mod['fabricante_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fab['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="modelo_id" value="<?= $mod['id'] ?>">
                                <button name="editar_modelo" class="btn btn-sm btn-success">💾</button>
                                <a href="?eliminar_modelo=<?= $mod['id'] ?>" onclick="return confirm('¿Eliminar modelo?')" class="btn btn-sm btn-danger">🗑️</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
