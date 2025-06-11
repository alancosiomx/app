<?php
// Procesamiento
$feedback = '';
$errores = 0;
$insertadas = 0;

// Obtener modelos y bancos
$modelos = $pdo->query("SELECT id, nombre FROM modelos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$bancos = $pdo->query("SELECT DISTINCT banco FROM inventario_tpv ORDER BY banco")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo_id = intval($_POST['modelo_id']);
    $banco = trim($_POST['banco'] ?? '');
    $series = explode(PHP_EOL, trim($_POST['series'] ?? ''));
    $series = array_map('trim', $series);
    $series = array_filter($series); // Quitar vacíos

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM inventario_tpv WHERE serie = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO inventario_tpv (serie, modelo_id, banco, estado, fecha_entrada) VALUES (?, ?, ?, 'Disponible', NOW())");

    foreach ($series as $serie) {
        $stmtCheck->execute([$serie]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errores++;
            continue;
        }
        $stmtInsert->execute([$serie, $modelo_id, $banco]);
        $insertadas++;
    }

    $feedback = "✅ Se ingresaron <strong>$insertadas</strong> TPV nuevas. ";
    if ($errores > 0) {
        $feedback .= "⚠️ $errores fueron rechazadas por duplicadas.";
    }
}
?>

<h3>+ Nueva Terminal</h3>

<?php if ($feedback): ?>
  <div class="alert alert-info"><?= $feedback ?></div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="modelo_id" class="form-label">Modelo</label>
        <select name="modelo_id" id="modelo_id" class="form-select" required>
            <option value="">-- Selecciona modelo --</option>
            <?php foreach ($modelos as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="banco" class="form-label">Banco</label>
        <select name="banco" id="banco" class="form-select" required>
            <option value="">-- Selecciona banco --</option>
            <?php foreach ($bancos as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="series" class="form-label">Series (una por línea)</label>
        <textarea name="series" id="series" rows="6" class="form-control" placeholder="Ejemplo:
123ABC
456DEF
789XYZ" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Guardar terminales</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
