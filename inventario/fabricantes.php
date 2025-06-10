<?php
require '../auth.php';
require '../config.php';
require '../permisos.php';

if (!tienePermiso('registrar_inventario')) {
    die('⛔ Acceso denegado');
}

$error = '';
$success = '';

// Crear nuevo fabricante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_nombre'])) {
    $nombre = trim($_POST['nuevo_nombre']);

    if (empty($nombre)) {
        $error = "El nombre del fabricante no puede estar vacío.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO fabricantes (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            $success = "✅ Fabricante agregado correctamente.";
        } catch (PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}

// Obtener lista de fabricantes
$fabricantes = $pdo->query("SELECT * FROM fabricantes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

require '../includes/header.php';
require '../includes/menu.php';
?>

<div class="main-content">
    <h3>Fabricantes</h3>
<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">
        ← Volver al Inventario
    </a>
</div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4" style="max-width:400px;">
        <label class="form-label">Nuevo fabricante</label>
        <div class="input-group">
            <input type="text" name="nuevo_nombre" class="form-control" placeholder="Ej: Ingenico" required>
            <button type="submit" class="btn btn-success">Agregar</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fabricantes as $fab): ?>
                        <tr>
                            <td><?php echo $fab['id']; ?></td>
                            <td><?php echo htmlspecialchars($fab['nombre']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
