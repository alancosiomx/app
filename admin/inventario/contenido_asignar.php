<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$tipo = $_GET['tipo'] ?? 'tpv';
if (!in_array($tipo, ['tpv', 'sim'])) {
    echo "<p class='text-red-600'>❌ Tipo inválido.</p>";
    return;
}

$tecnicos = $pdo->query("SELECT u.nombre FROM usuarios u JOIN usuarios_roles r ON r.usuario_id = u.id WHERE r.rol IN ('idc','tecnico') ORDER BY u.nombre")->fetchAll(PDO::FETCH_COLUMN);

$query = $tipo === 'tpv'
    ? "SELECT serie FROM inventario_tpv WHERE estado = 'Disponible' ORDER BY id DESC"
    : "SELECT serie_sim AS serie FROM inventario_sims WHERE estado = 'Disponible' ORDER BY id DESC";

$disponibles = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
?>

<h4>📦 Asignar <?= strtoupper($tipo) ?> a Técnico</h4>

<form method="post" action="asignar.php" class="mt-4">
    <input type="hidden" name="accion" value="asignar">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="tecnico" class="form-label">Técnico *</label>
        <select name="tecnico" id="tecnico" class="form-select" required>
            <option value="">-- Selecciona técnico --</option>
            <?php foreach ($tecnicos as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="series" class="form-label">Series a Asignar *</label>
        <textarea name="series" id="series" class="form-control" rows="6" placeholder="Una serie por línea" required></textarea>
        <small class="text-muted">Disponibles: <?= count($disponibles) ?></small>
    </div>

    <button type="submit" class="btn btn-primary">Asignar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
