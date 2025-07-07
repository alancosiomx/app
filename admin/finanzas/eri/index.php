<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../constants.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

ob_start();

$current_tab = $_GET['vista'] ?? 'eri';
?>

<div class="mb-6 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= $current_tab === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<h1 class="text-2xl font-bold mb-6 text-blue-700">📊 Estado de Resultados Interno (ERI)</h1>

<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
  <div>
    <label class="block text-sm font-medium text-gray-700">Desde</label>
    <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Hasta</label>
    <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Técnico</label>
    <select name="idc" class="mt-1 block w-full border-gray-300 rounded-md">
      <option value="">Todos</option>
      <?php
      $tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL ORDER BY idc")->fetchAll(PDO::FETCH_COLUMN);
      foreach ($tecnicos as $idc): ?>
        <option value="<?= $idc ?>" <?= ($_GET['idc'] ?? '') === $idc ? 'selected' : '' ?>><?= $idc ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="flex items-end">
    <button type="submit" name="vista" value="eri" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
      Filtrar
    </button>
  </div>
</form>

<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow">
    <thead class="bg-gray-100 text-xs font-semibold text-gray-700">
      <tr>
        <th class="px-4 py-3 text-left">Técnico</th>
        <th class="px-4 py-3 text-left">Servicios</th>
        <th class="px-4 py-3 text-left">Ingresos</th>
        <th class="px-4 py-3 text-left">Viáticos</th>
        <th class="px-4 py-3 text-left">Otros Gastos</th>
        <th class="px-4 py-3 text-left">Utilidad</th>
        <th class="px-4 py-3 text-left">Margen %</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
      <!-- Datos dinámicos aquí -->
    </tbody>
  </table>
</div>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';
