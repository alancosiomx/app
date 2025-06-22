<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$current_tab = $_GET['vista'] ?? 'viaticos';

// Procesar envío de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idc'])) {
  $idc = $_POST['idc'];
  $monto = floatval($_POST['monto'] ?? 0);
  $comentarios = $_POST['comentarios'] ?? '';

  $stmt = $pdo->prepare("INSERT INTO viaticos (idc, monto, comentarios, fecha_registro) VALUES (?, ?, ?, NOW())");
  $stmt->execute([$idc, $monto, $comentarios]);
  echo "<div class='bg-green-100 text-green-800 p-4 mb-4 rounded'>✅ Viático registrado para $idc</div>";
}

$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

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

<h1 class="text-xl font-bold mb-4 text-blue-700">✈ Registro de Viáticos</h1>

<form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico (IDC)</label>
    <select name="idc" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
      <option value="">Selecciona un técnico</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Monto</label>
    <input type="number" step="0.01" name="monto" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-600">Comentarios</label>
    <input type="text" name="comentarios" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div class="md:col-span-4 flex justify-end">
    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Guardar Viático</button>
  </div>
</form>

<?php
$result = $pdo->query("SELECT * FROM viaticos ORDER BY fecha_registro DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
if ($result):
?>
  <div class="bg-white shadow overflow-hidden rounded-xl">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
        <tr>
          <th class="px-4 py-2 text-left">Técnico</th>
          <th class="px-4 py-2 text-left">Monto</th>
          <th class="px-4 py-2 text-left">Comentarios</th>
          <th class="px-4 py-2 text-left">Fecha</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-sm">
        <?php foreach ($result as $row): ?>
          <tr>
            <td class="px-4 py-2"><?= htmlspecialchars($row['idc']) ?></td>
            <td class="px-4 py-2">$<?= number_format($row['monto'], 2) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['comentarios']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['fecha_registro']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <p class="text-gray-500">No hay viáticos registrados recientemente.</p>
<?php endif; ?>
