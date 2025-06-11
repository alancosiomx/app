<?php
// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar envío a CEDIS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'devolver') {
    $series = array_map('trim', explode("\n", $_POST['series']));
    $observacion = trim($_POST['observacion']);
    $usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';

    if (!empty($series)) {
        $stmt = $pdo->prepare("UPDATE inventario_tpv SET estado = 'Devuelto', tecnico_actual = NULL WHERE serie = ? AND estado = 'Dañado'");
        $log = $pdo->prepare("INSERT INTO log_inventario (serie, tipo_movimiento, usuario, observaciones) VALUES (?, 'Devolución', ?, ?)");

        foreach ($series as $serie) {
            $serie = strtoupper($serie);
            $stmt->execute([$serie]);
            if ($stmt->rowCount()) {
                $log->execute([$serie, $usuario, $observacion ?: 'Empacado para CEDIS']);
            }
        }

        header("Location: index.php");
        exit;
    } else {
        echo "<div class='alert alert-warning'>Debes ingresar al menos una serie.</div>";
    }
}

// Terminales marcadas como dañadas
$danadas = $pdo->query("SELECT serie FROM inventario_tpv WHERE estado = 'Dañado' ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
?>

<h4>📦 Preparar Terminales para Envío al CEDIS</h4>

<form method="post" class="mt-4">
    <input type="hidden" name="accion" value="devolver">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="observacion" class="form-label">Observación (opcional)</label>
        <input type="text" name="observacion" id="observacion" class="form-control" placeholder="Ej: Lote #23, empaquetadas con refacciones...">
    </div>

    <div class="mb-3">
        <label for="series" class="form-label">Series a devolver *</label>
        <textarea name="series" id="series" class="form-control" rows="6" placeholder="Una serie por línea" required></textarea>
        <small class="text-muted">Terminales dañadas detectadas: <?= count($danadas) ?></small>
    </div>

    <button type="submit" class="btn btn-primary">Marcar como Devueltas</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
