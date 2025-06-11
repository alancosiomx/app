<?php
// Cargar bancos
$bancos = $pdo->query("SELECT nombre FROM bancos ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
?>

<h4>➕ Agregar Terminales</h4>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<form method="post">
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
