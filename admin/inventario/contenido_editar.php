<?php
// Validar ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<div class='alert alert-danger'>ID inválido</div>";
    return;
}

// Cargar terminal
$stmt = $pdo->prepare("
    SELECT * FROM inventario_tpv WHERE id = ?
");
$stmt->execute([$id]);
$terminal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$terminal) {
    echo "<div class='alert alert-warning'>Terminal no encontrada</div>";
    return;
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'actualizar') {
    $serie = trim($_POST['serie']);
    $modelo_id = intval($_POST['modelo_id']);
    $banco = trim($_POST['banco']);
    $estado = $_POST['estado'];
    $observaciones = trim($_POST['observaciones']);

    if ($serie && $modelo_id && $banco && $estado) {
        $stmt = $pdo->prepare("UPDATE inventario_tpv SET serie = ?, modelo_id = ?, banco = ?, estado = ?, observaciones = ? WHERE id = ?");
        $stmt->execute([$serie, $modelo_id, $banco, $estado, $observaciones, $id]);

        header("Location: index.php");
        exit;
    } else {
        echo "<div class='alert alert-warning'>Faltan campos obligatorios</div>";
    }
}

// Modelos
$modelos = $pdo->query("
    SELECT m.id, CONCAT(f.nombre, ' - ', m.nombre) AS nombre_completo
    FROM modelos m
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h4>✏️ Editar Terminal #<?= $terminal['id'] ?></h4>

<form method="post" class="mt-4">
    <input type="hidden" name="accion" value="actualizar">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="serie" class="form-label">Número de Serie *</label>
        <input type="text" name="serie" id="serie" class="form-control" value="<?= htmlspecialchars($terminal['serie']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="modelo_id" class="form-label">Modelo *</label>
        <select name="modelo_id" id="modelo_id" class="form-select" required>
            <option value="">-- Selecciona un modelo --</option>
            <?php foreach ($modelos as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $m['id'] == $terminal['modelo_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['nombre_completo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="banco" class="form-label">Banco *</label>
        <input type="text" name="banco" id="banco" class="form-control" value="<?= htmlspecialchars($terminal['banco']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="estado" class="form-label">Estado *</label>
        <select name="estado" id="estado" class="form-select" required>
            <?php
            $estados = ['Disponible', 'Asignado', 'Instalado', 'Retirado', 'Dañado', 'Devuelto'];
            foreach ($estados as $estado) {
                $selected = $estado === $terminal['estado'] ? 'selected' : '';
                echo "<option value=\"$estado\" $selected>$estado</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" class="form-control"><?= htmlspecialchars($terminal['observaciones']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
