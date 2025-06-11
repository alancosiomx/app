<?php
// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'marcar_danado') {
    $series = array_map('trim', explode("\n", $_POST['series']));
    $motivo = trim($_POST['motivo']);
    $usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';

    if (!empty($series) && $motivo !== '') {
        $stmt = $pdo->prepare("UPDATE inventario_tpv SET estado = 'Dañado', tecnico_actual = NULL WHERE serie = ? AND estado IN ('Asignado', 'Instalado', 'Retirado')");
        $log = $pdo->prepare("INSERT INTO log_inventario (serie, tipo_movimiento, usuario, observaciones) VALUES (?, 'Dañado', ?, ?)");

        foreach ($series as $serie) {
            $serie = strtoupper($serie);
            $stmt->execute([$serie]);
            if ($stmt->rowCount()) {
                $log->execute([$serie, $usuario, $motivo]);
            }
        }

        header("Location: index.php");
        exit;
    } else {
        echo "<div class='alert alert-warning'>Completa todos los campos.</div>";
    }
}

// Terminales asignadas, instaladas o retiradas
$en_campo = $pdo->query("
    SELECT serie FROM inventario_tpv 
    WHERE estado IN ('Asignado', 'Instalado', 'Retirado')
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_COLUMN);
?>

<h4>🛠️ Recibir Terminales Dañadas</h4>

<form method="post" class="mt-4">
    <input type="hidden" name="accion" value="marcar_danado">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="motivo" class="form-label">Motivo *</label>
        <input type="text" name="motivo" id="motivo" class="form-control" placeholder="Ej: No enciende, pantalla rota..." required>
    </div>

    <div class="mb-3">
        <label for="series" class="form-label">Series dañadas *</label>
        <textarea name="series" id="series" class="form-control" rows="6" placeholder="Una serie por línea" required></textarea>
        <small class="text-muted">Terminales en campo: <?= count($en_campo) ?></small>
    </div>

    <button type="submit" class="btn btn-danger">Marcar como Dañadas</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
