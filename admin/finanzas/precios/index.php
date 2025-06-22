<?php
require_once __DIR__ . '/../../init.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$current_tab = $_GET['vista'] ?? 'precios';

// Guardar nuevo precio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idc = $_POST['idc'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $resultado = $_POST['resultado'] ?? '';
    $banco = $_POST['banco'] ?? '';
    $monto = $_POST['monto'] ?? 0;
    $comentarios = $_POST['comentarios'] ?? '';

    if ($idc && $servicio && $resultado && $banco && is_numeric($monto)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM precios_idc WHERE idc = ? AND servicio = ? AND resultado = ? AND banco = ?");
        $check->execute([$idc, $servicio, $resultado, $banco]);
        if ($check->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO precios_idc (idc, servicio, resultado, banco, monto, comentarios) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$idc, $servicio, $resultado, $banco, $monto, $comentarios]);
        }
    }
}

// Obtener registros
$precios = $pdo->query("SELECT * FROM precios_idc ORDER BY creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
$tecnicos = $pdo->query("SELECT DISTINCT nombre FROM usuarios WHERE roles LIKE '%idc%' ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
?>

<h1 class="text-xl font-bold mb-4 text-blue-700">⚙️ Configurar Precios por Técnico</h1>

<form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-white p-4 rounded-xl shadow">
  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico</label>
    <select name="idc" class="w-full border-gray-300 rounded-md">
      <option value="">Selecciona</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>"><?= $t ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Servicio</label>
    <input name="servicio" class="w-full border-gray-300 rounded-md" required>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Resultado</label>
    <select name="resultado" class="w-full border-gray-300 rounded-md">
      <option>Exito</option>
      <option>Rechazo</option>
      <option>Visita</option>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Banco</label>
    <select name="banco" class="w-full border-gray-300 rounded-md">
      <option>BBVA</option>
      <option>BANREGIO</option>
      <option>AZTECA</option>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Monto</label>
    <input type="number" name="monto" step="0.01" class="w-full border-gray-300 rounded-md" required>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Comentarios</label>
    <input name="comentarios" class="w-full border-gray-300 rounded-md">
  </div>
  <div class="md:col-span-3 text-right">
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
  </div>
</form>

<table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow overflow-hidden">
  <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2">Técnico</th>
      <th class="px-4 py-2">Servicio</th>
      <th class="px-4 py-2">Resultado</th>
      <th class="px-4 py-2">Banco</th>
      <th class="px-4 py-2">Monto</th>
      <th class="px-4 py-2">Comentarios</th>
      <th class="px-4 py-2">Fecha</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-100 text-sm">
    <?php foreach ($precios as $p): ?>
      <tr>
        <td class="px-4 py-2"><?= htmlspecialchars($p['idc']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($p['servicio']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($p['resultado']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($p['banco']) ?></td>
        <td class="px-4 py-2 text-right font-semibold text-blue-700">\$<?= number_format($p['monto'], 2) ?></td>
        <td class="px-4 py-2 text-gray-500"><?= htmlspecialchars($p['comentarios']) ?></td>
        <td class="px-4 py-2 text-sm text-gray-400"><?= date('d/m/Y H:i', strtotime($p['creado_en'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
