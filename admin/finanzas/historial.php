<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$current_tab = $_GET['vista'] ?? 'historial';

$historial = $pdo->query("SELECT * FROM viaticos ORDER BY fecha_registro DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
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

<h1 class="text-xl font-bold mb-4 text-blue-700">📂 Historial de Viáticos</h1>

<table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow overflow-hidden">
  <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2">Técnico</th>
      <th class="px-4 py-2">Afiliación</th>
      <th class="px-4 py-2">Población</th>
      <th class="px-4 py-2">Colonia</th>
      <th class="px-4 py-2">Ciudad</th>
      <th class="px-4 py-2">Monto</th>
      <th class="px-4 py-2">Comentarios</th>
      <th class="px-4 py-2">Fecha</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-100 text-sm">
    <?php foreach ($historial as $v): ?>
      <tr>
        <td class="px-4 py-2"><?= htmlspecialchars($v['idc']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['afiliacion']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['poblacion']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['colonia']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['ciudad']) ?></td>
        <td class="px-4 py-2">\$<?= number_format($v['monto'], 2) ?></td>
        <td class="px-4 py-2"><?= nl2br(htmlspecialchars($v['comentarios'])) ?></td>
        <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($v['fecha_registro'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
