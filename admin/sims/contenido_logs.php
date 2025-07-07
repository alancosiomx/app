<?php
// app/admin/sims/contenido_logs.php
require_once __DIR__ . '/constants.php';

$vista_actual = $_GET['vista'] ?? 'logs';

echo '<div class="tabs mb-4">';
foreach ($tabs_sims as $key => $label) {
    $active = ($vista_actual === $key) ? 'font-bold underline text-blue-600' : 'text-gray-500';
    echo "<a href='index.php?vista=$key' class='mr-4 $active'>$label</a>";
}
echo '</div>';
?>

<h2 class="text-xl font-bold mb-4">📜 Historial de Movimientos de SIMs</h2>

<div class="overflow-x-auto">
  <table id="tabla_logs" class="min-w-full divide-y divide-gray-200 text-sm text-left">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="px-4 py-2">Fecha</th>
        <th class="px-4 py-2">Serie SIM</th>
        <th class="px-4 py-2">Movimiento</th>
        <th class="px-4 py-2">Usuario</th>
        <th class="px-4 py-2">Observaciones</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-100" id="tbody_logs">
      <!-- Contenido generado por JS -->
    </tbody>
  </table>
</div>

<script>
$(document).ready(function() {
  $('#tabla_logs').DataTable({
    ajax: 'backend_logs.php',
    columns: [
      { data: 'fecha' },
      { data: 'serie_sim' },
      { data: 'tipo_movimiento' },
      { data: 'usuario' },
      { data: 'observaciones' }
    ],
    responsive: true,
    order: [[0, 'desc']],
    dom: 'tip'
  });
});
</script>
