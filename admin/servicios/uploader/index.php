<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../layout.php';

$bancos = ['bbva', 'banregio', 'azteca'];
$mensaje = '';
$archivoProcesado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo']) && isset($_POST['banco'])) {
    $banco = strtolower($_POST['banco']);
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $nombreArchivo = $_FILES['archivo']['name'];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

    if (in_array($banco, $bancos) && ($extension === 'csv' || $extension === 'xlsx')) {
        require_once __DIR__ . "/funciones_carga.php";
        $mensaje = cargar_a_staging($archivoTmp, $extension, $banco, $pdo);
        $archivoProcesado = true;
    } else {
        $mensaje = '⚠️ Formato de archivo o banco inválido.';
    }
}
?>

<div class="container mt-5">
    <h2>Cargar Servicios</h2>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="banco" class="form-label">Selecciona el banco:</label>
            <select name="banco" id="banco" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($bancos as $b) : ?>
                    <option value="<?= $b ?>"><?= strtoupper($b) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="archivo" class="form-label">Archivo CSV o Excel:</label>
            <input type="file" name="archivo" id="archivo" class="form-control" accept=".csv,.xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">Cargar archivo</button>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($archivoProcesado): ?>
        <form method="POST" action="importar_omnipos.php">
            <input type="hidden" name="banco" value="<?= htmlspecialchars($_POST['banco']) ?>">
            <button type="submit" class="btn btn-success">✅ Importar a OMNIPOS</button>
        </form>
    <?php endif; ?>
</div>
