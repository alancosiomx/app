<?php
// Insertar fabricante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_fabricante'])) {
    $nombre = trim($_POST['nombre_fabricante']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO fabricantes (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header("Location: tpvs.php");
    exit;
}

// Insertar modelo
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

// Obtener todos los fabricantes
$fabricantes = $pdo->query("SELECT * FROM fabricantes ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los modelos
$modelos = $pdo->query("
    SELECT m.id, m.nombre AS modelo, f.nombre AS fabricante
    FROM modelos m
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre, m.nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-6">
        <h5>📦 Fabricantes</h5>
        <form method="post" class="mb-3">
            <input type="hidden" name="nuevo_fabricante" value="1">
            <div class="input-group">
                <input type="text" name="nombre_fabricante" class="form-control" placeholder="Nuevo fabricante" required>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>

        <table class="table table-sm table-bordered">
            <thead><tr><th>ID</th><th>Nombre</th></tr></thead>
            <tbody>
                <?php foreach ($fabricantes as $fab): ?>
                    <tr>
                        <td><?= $fab['id'] ?></td>
                        <td><?= htmlspecialchars($fab['nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h5>📋 Modelos</h5>
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
            <thead><tr><th>ID</th><th>Modelo</th><th>Fabricante</th></tr></thead>
            <tbody>
                <?php foreach ($modelos as $mod): ?>
                    <tr>
                        <td><?= $mod['id'] ?></td>
                        <td><?= htmlspecialchars($mod['modelo']) ?></td>
                        <td><?= htmlspecialchars($mod['fabricante']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
