<?php
// Cargar bancos
$bancos = $pdo->query("SELECT nombre FROM bancos ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);

// Cargar modelos con fabricante
$modelos = $pdo->query("
    SELECT m.id, CONCAT(f.nombre, ' - ', m.nombre) AS nombre
    FROM modelos m
    JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre, m.nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo_id = intval($_POST['modelo_id'] ?? 0);
    $banco = trim($_POST['banco'] ?? '');
    $series = array_filter(array_map('trim', explode("\n", $_POST['series'] ?? '')));

    $insertadas = 0;
    $duplicadas = 0;

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM inventario_tpv WHERE serie = ?");
    $stmt_insert = $pdo->prepare("
        INSERT INTO inventario_tpv (serie, modelo_id, banco, estado, fecha_entrada)
        VALUES (?, ?, ?, 'Disponible', NOW())
    ");

    foreach ($series as $serie) {
        $stmt_check->execute([$serie]);
        if ($stmt_check->fetchColumn() > 0) {
            $duplicadas++;
        } else {
            $stmt_insert->execute([$serie, $modelo_id, $banco]);
            $insertadas++;
        }
    }

    $msg = "$insertadas terminales ingresadas. $duplicadas duplicadas.";
    header("Location: nuevo.php?msg=" . urlencode($msg));
    exit;
}
?>

<h4>➕ Agregar Terminales</h4>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="modelo_id" class="form-label">Modelo</label>
        <select name="modelo_id" id="modelo_id" class="form-select" required>
            <option value="">-- Selecciona un modelo --</option>
            <?php foreach ($modelos as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="banco" class="form-label">Banco</label>
        <select name="banco" id="banco" class="form-select" required>
            <option value="">-- Selecciona un banco --</option>
            <?php foreach ($bancos as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="series" class="form-label">Series de TPV (una por línea)</label>
        <textarea name="series" id="series" rows="6" class="form-control" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
