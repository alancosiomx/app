<?php
// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'asignar') {
    $series = array_map('trim', explode("\n", $_POST['series']));
    $tecnico = trim($_POST['tecnico']);
    $usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';

    if ($tecnico && !empty($series)) {
        $stmt = $pdo->prepare("UPDATE inventario_tpv SET estado = 'Asignado', tecnico_actual = ? WHERE serie = ? AND estado = 'Disponible'");
        $log = $pdo->prepare("INSERT INTO log_inventario (serie, tipo_movimiento, usuario, observaciones) VALUES (?, 'Asignación', ?, ?)");

        foreach ($series as $serie) {
            $serie = strtoupper($serie);
            $stmt->execute([$tecnico, $serie]);
            if ($stmt->rowCount()) {
                $log->execute([$serie, $usuario, "Asignado a $tecnico"]);
            }
        }

        header("Location: index.php");
        exit;
    }
}

// Terminales disponibles
$disponibles = $pdo->query("SELECT serie FROM inventario_tpv WHERE estado = 'Disponible' ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);

// Técnicos disponibles
$tecnicos = $pdo->query("
    SELECT u.nombre FROM usuarios u
    JOIN usuarios_roles r ON r.usuario_id = u.id
    WHERE r.rol = 'idc' OR r.rol = 'tecnico'
    ORDER BY u.nombre
")->fetchAll(PDO::FETCH_COLUMN);
?>

<h4>🔁 Asignar Terminales a Técnico</h4>

<form method="post" class="mt-4">
    <input type="hidden" name="accion" value="asignar">
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
        <small class="text-muted">Terminales disponibles: <?= count($disponibles) ?></small>
    </div>

    <button type="submit" class="btn btn-primary">Asignar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
