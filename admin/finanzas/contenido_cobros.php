<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$current_tab = $_GET['vista'] ?? 'cobros';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$tecnico = $_GET['tecnico'] ?? '';

$condiciones = ["actual_status = 'Histórico'", "resultado IN ('Exito', 'Rechazo')"];
$parametros = [];

if ($desde && $hasta) {
  $condiciones[] = "fecha_atencion BETWEEN ? AND ?";
  $parametros[] = "$desde 00:00:00";
  $parametros[] = "$hasta 23:59:59";
}

if ($tecnico) {
  $condiciones[] = "idc = ?";
  $parametros[] = $tecnico;
}

$where = count($condiciones) ? "WHERE " . implode(' AND ', $condiciones) : "";
$sql = "SELECT * FROM servicios_omnipos $where ORDER BY fecha_atencion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos únicos
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL ORDER BY idc")->fetchAll(PDO::FETCH_COLUMN);
?>
<!-- 🔗 TABS DE FINANZAS -->
<div class="mb-4 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= $current_tab === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<h1 class="text-xl font-bold mb-4 text-blue-700"><?= TABS_FINANZAS['cobros'] ?></h1>
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="vista" value="cobros">
  <div>
    <label class="block text-sm font-medium text-gray-600">Desde</label>
    <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>" class="w-full border-gray-300 rounded-md">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Hasta</label>
    <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>" class="w-full border-gray-300 rounded-md">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico</label>
    <select name="tecnico" class="w-full border-gray-300 rounded-md">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= $t ?>" <?= $t === $tecnico ? 'selected' : '' ?>><?= $t ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex items-end">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Filtrar</button>
  </div>
</form>
