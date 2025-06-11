<?php
// Generar CSRF token si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'guardar') {
    $serie = trim($_POST['serie']);
    $modelo_id = intval($_POST['modelo_id']);
    $banco = trim($_POST['banco']);
    $estado = $_POST['estado'];
    $observaciones = trim($_POST['observaciones']);

    // Validación básica
    if ($serie && $modelo_id && $banco && $estado) {
        // Revisar si la serie ya existe
        $existe = $pdo->prepare("SELECT COUNT(*) FROM inventario_tpv WHERE serie = ?");
        $existe->execute([$serie]);
        if ($existe->fetchColumn() > 0) {
            echo "<div class='alert alert-danger'>⚠️ Serie duplicada: {$serie}</div>";
        } else {
            // Guardar
            $stmt = $pdo->prepare("INSERT INTO inventario_tpv (serie, modelo_id, banco, estado, fecha_entrada, observaciones) VALUES (?, ?, ?, ?, NOW(), ?)");
            $stmt->execute([$serie, $modelo_id, $banco, $estado, $observaciones]);

            // Redirigir a index
            header("Location: index.php");
            exit;
        }
    } else {
        echo "<div class='alert alert-warning'>Completa todos los campos obligatorios.</div>";
    }
}

// Cargar modelos y fabricantes
$modelos = $pdo->query("
    SELECT m.id, CONCAT(f.nombre, ' - ', m.nombre) AS nombre_completo
    FROM modelos m
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h4>➕ Registrar Nueva Terminal</h4>

<form method="post" class="mt-4">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="serie" class="form-label">Número de Serie *</label>
        <input type="text" name="serie" id="serie" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="modelo_id" class="form-label">Modelo *</label>
        <select name="modelo_id" id="modelo_id" class="form-select" required>
            <option value="">-- Selecciona un modelo --</option>
            <?php foreach ($modelos as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="banco" class="form-label">Banco *</label>
        <input type="text" name="banco" id="banco" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="estado" class="form-label">Estado *</label>
        <select name="estado" id="estado" class="form-select" required>
            <option value="Disponible">Disponible</option>
            <option value="Dañado">Dañado</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar Terminal</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
