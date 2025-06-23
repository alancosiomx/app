<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/funciones_carga.php';
require_once __DIR__ . '/funciones_mapeo.php';
$contenido = __FILE__; // para layout tailwind

$bancos = ['bbva', 'banregio', 'azteca'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo']) && isset($_POST['banco'])) {
    $banco = strtolower($_POST['banco']);
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $nombreArchivo = $_FILES['archivo']['name'];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

    if (in_array($banco, $bancos) && in_array($extension, ['csv', 'xlsx'])) {
        // Paso 1: Cargar a tabla staging
        $mensaje_staging = cargar_a_staging($archivoTmp, $extension, $banco, $pdo);

        // Paso 2: Migrar automáticamente a OMNIPOS
        switch ($banco) {
            case 'bbva':
                $mensaje_mapeo = mapear_bbva($pdo);
                break;
            case 'banregio':
                $mensaje_mapeo = mapear_banregio($pdo);
                break;
            case 'azteca':
                $mensaje_mapeo = mapear_azteca($pdo);
                break;
            default:
                $mensaje_mapeo = '❌ Banco no reconocido.';
        }

        $mensaje = $mensaje_staging . "\n\n" . $mensaje_mapeo;
    } else {
        $mensaje = '⚠️ Formato de archivo o banco inválido.';
    }
}
?>

<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
  <h2 class="text-2xl font-bold mb-6 text-gray-800">📤 Cargar Servicios</h2>

  <form method="POST" enctype="multipart/form-data" class="space-y-6">
    <div>
      <label for="banco" class="block text-sm font-medium text-gray-700">Selecciona el banco:</label>
      <select name="banco" id="banco"
              class="mt-1 w-full rounded border border-gray-300 p-2 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              required>
        <option value="">-- Selecciona --</option>
        <?php foreach ($bancos as $b): ?>
          <option value="<?= $b ?>" <?= (isset($_POST['banco']) && $_POST['banco'] === $b) ? 'selected' : '' ?>>
            <?= strtoupper($b) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="archivo" class="block text-sm font-medium text-gray-700">Archivo CSV o Excel:</label>
      <input type="file" name="archivo" id="archivo" accept=".csv,.xlsx" required
             class="mt-1 w-full border border-gray-300 rounded p-2 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
        🚀 Subir y migrar
      </button>
    </div>
  </form>

  <?php if ($mensaje): ?>
    <div class="mt-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm shadow-sm whitespace-pre-line">
      <?= nl2br(htmlspecialchars($mensaje)) ?>
    </div>
  <?php endif; ?>
</div>
