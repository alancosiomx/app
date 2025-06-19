<?php
require_once __DIR__ . '/../../config.php';
session_start();

// Guardar nueva solución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $banco = trim($_POST['banco']);
    $servicio = trim($_POST['servicio']);
    $solucion = trim($_POST['solucion']);
    $solucion_especifica = trim($_POST['solucion_especifica']);

    if ($banco && $servicio && $solucion && $solucion_especifica) {
        $stmt = $pdo->prepare("INSERT INTO servicio_soluciones (banco, servicio, solucion, solucion_especifica) VALUES (?, ?, ?, ?)");
        $stmt->execute([$banco, $servicio, $solucion, $solucion_especifica]);
    }
}

// Baja lógica
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $pdo->prepare("UPDATE servicio_soluciones SET activo = 0 WHERE id = ?")->execute([$id]);
}

// Obtener registros activos
$soluciones = $pdo->query("SELECT * FROM servicio_soluciones WHERE activo = 1 ORDER BY banco, servicio, solucion")->fetchAll(PDO::FETCH_ASSOC);

// Cargar opciones únicas
// Cargar bancos únicos desde servicios_omnipos
$bancos = $pdo->query("SELECT DISTINCT banco FROM servicios_omnipos WHERE banco IS NOT NULL AND banco != '' ORDER BY banco")->fetchAll(PDO::FETCH_COLUMN);

// Cargar servicios únicos desde servicios_omnipos
$servicios = $pdo->query("SELECT DISTINCT servicio FROM servicios_omnipos WHERE servicio IS NOT NULL AND servicio != '' ORDER BY servicio")->fetchAll(PDO::FETCH_COLUMN);


include __DIR__ . '/../layout.php';
?>

<div class="container mx-auto p-4">
  <h2 class="mb-4 text-xl font-bold">🛠 Catálogo de Soluciones por Servicio</h2>

  <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-white p-4 rounded shadow">
    <select name="banco" required class="border p-2 rounded w-full">
      <option value="">Selecciona banco</option>
      <?php foreach ($bancos as $b): ?>
        <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="servicio" required class="border p-2 rounded w-full">
      <option value="">Selecciona servicio</option>
      <?php foreach ($servicios as $s): ?>
        <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
      <?php endforeach; ?>
    </select>

    <input type="text" name="solucion" placeholder="Solución General" required class="border p-2 rounded w-full">
    <input type="text" name="solucion_especifica" placeholder="Solución Específica" required class="border p-2 rounded w-full">

    <div class="md:col-span-4 text-right">
      <button type="submit" name="guardar" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        ➕ Agregar Solución
      </button>
    </div>
  </form>

  <table class="min-w-full bg-white text-sm rounded shadow overflow-hidden">
    <thead class="bg-gray-200 text-gray-700">
      <tr>
        <th class="px-4 py-2 text-left">Banco</th>
        <th class="px-4 py-2 text-left">Servicio</th>
        <th class="px-4 py-2 text-left">Solución</th>
        <th class="px-4 py-2 text-left">Solución Específica</th>
        <th class="px-4 py-2 text-left">🗑️</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($soluciones as $s): ?>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-4 py-2"><?= htmlspecialchars($s['banco']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['servicio']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['solucion']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['solucion_especifica']) ?></td>
          <td class="px-4 py-2">
            <a href="?eliminar=<?= $s['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar esta solución?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
