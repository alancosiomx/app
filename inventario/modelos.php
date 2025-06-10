<?php
require '../auth.php';
require '../config.php';
require '../permisos.php';

if (!tienePermiso('registrar_inventario')) {
    die('⛔ Acceso denegado');
}

// Devolver modelos en formato JSON para las peticiones AJAX
if (isset($_GET['fabricante_id'])) {
    $stmt = $pdo->prepare('SELECT id, nombre FROM modelos WHERE fabricante_id = ? ORDER BY nombre');
    $stmt->execute([$_GET['fabricante_id']]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$error = '';
$success = '';

// Obtener lista de fabricantes
$fabricantes = $pdo->query("SELECT id, nombre FROM fabricantes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Crear nuevo modelo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $fabricante_id = $_POST['fabricante_id'] ?? '';

    if (empty($nombre) || empty($fabricante_id)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO modelos (nombre, fabricante_id) VALUES (?, ?)");
            $stmt->execute([$nombre, $fabricante_id]);
            $success = "✅ Modelo agregado correctamente.";
        } catch (PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}

// Obtener lista de modelos con su fabricante
$stmt = $pdo->query("
    SELECT m.id, m.nombre AS modelo, f.nombre AS fabricante
    FROM modelos m
    INNER JOIN fabricantes f ON m.fabricante_id = f.id
    ORDER BY f.nombre, m.nombre
");
$modelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../includes/header.php';
require '../includes/menu.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Modelos</h3>
        <a href="index.php" class="btn btn-secondary">← Volver al Inventario</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4" style="max-width: 500px;">
        <div class="mb-3">
            <label for="fabricante_id" class="form-label">Fabricante</label>
            <select name="fabricante_id" id="fabricante_id" class="form-select" required>
                <option value="">-- Selecciona fabricante --</option>
                <?php foreach ($fabricantes as $fab): ?>
                    <option value="<?php echo $fab['id']; ?>"><?php echo htmlspecialchars($fab['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del modelo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Move 2500" required>
        </div>

        <button type="submit" class="btn btn-success">Agregar modelo</button>
    </form>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fabricante</th>
                        <th>Modelo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modelos as $row): ?>
                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['fabricante']); ?></td>
                            <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
