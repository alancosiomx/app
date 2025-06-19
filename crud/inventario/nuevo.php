<?php
require '../../auth.php';
require '../../config.php';

if (!in_array('admin', $_SESSION['usuario_roles'])) {
    header("Location: ../../dashboard.php");
    exit;
}

$error = '';
$success = '';

// Obtener todos los fabricantes
$fabricantes = $pdo->query("SELECT id, nombre FROM fabricantes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fabricante_id = $_POST['fabricante_id'] ?? '';
    $modelo_id = $_POST['modelo_id'] ?? '';
    $banco = trim($_POST['banco'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (!$fabricante_id || !$modelo_id || !$banco) {
        $error = "Todos los campos obligatorios deben completarse.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO inventario_disponible (modelo_id, banco, descripcion) VALUES (?, ?, ?)");
        if ($stmt->execute([$modelo_id, $banco, $descripcion])) {
            $success = "✅ Terminal registrada correctamente.";
        } else {
            $error = "❌ Error al registrar la terminal.";
        }
    }
}

require '../../includes/header.php';
require '../../includes/menu.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Nueva Terminal</h3>
        <a href="index.php" class="btn btn-secondary">← Volver al Inventario</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" class="card p-3 shadow-sm" style="max-width: 600px;">
        <div class="mb-3">
            <label for="fabricante" class="form-label">Fabricante</label>
            <select name="fabricante_id" id="fabricante" class="form-select" required>
                <option value="">-- Selecciona fabricante --</option>
                <?php foreach ($fabricantes as $fab): ?>
                    <option value="<?php echo $fab['id']; ?>"><?php echo htmlspecialchars($fab['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo</label>
            <select name="modelo_id" id="modelo" class="form-select" required>
                <option value="">-- Primero selecciona fabricante --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="banco" class="form-label">Banco</label>
            <input type="text" name="banco" id="banco" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción (opcional)</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- JS para cargar modelos dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fabricante = document.getElementById('fabricante');
    const modelo = document.getElementById('modelo');

    if (fabricante && modelo) {
        fabricante.addEventListener('change', function () {
            const fabricanteId = this.value;
            modelo.innerHTML = '<option value="">Cargando modelos...</option>';

            fetch('modelos.php?fabricante_id=' + fabricanteId)
                .then(res => {
                    if (!res.ok) throw new Error('Error de red');
                    return res.json();
                })
                .then(data => {
                    modelo.innerHTML = '<option value="">-- Selecciona modelo --</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nombre;
                        modelo.appendChild(opt);
                    });
                })
                .catch(() => {
                    modelo.innerHTML = '<option value="">Error al cargar modelos</option>';
                });
        });
    }
});
</script>

<?php require '../../includes/footer.php'; ?>
