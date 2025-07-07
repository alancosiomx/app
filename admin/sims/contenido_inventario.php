<?php
// app/admin/sims/contenido_inventario.php
require_once __DIR__ . '/constants.php';

$vista_actual = $_GET['vista'] ?? 'inventario';

echo '<div class="tabs mb-4">';
foreach ($tabs_sims as $key => $label) {
    $active = ($vista_actual === $key) ? 'font-bold underline text-blue-600' : 'text-gray-500';
    echo "<a href='index.php?vista=$key' class='mr-4 $active'>$label</a>";
}
echo '</div>';
?>

<h2 class="text-xl font-bold mb-4">📦 Inventario de SIMs</h2>

<div class="overflow-x-auto">
  <table id="tabla_sims" class="min-w-full divide-y divide-gray-200 text-sm text-left">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="px-4 py-2">Serie SIM</th>
        <th class="px-4 py-2">Marca</th>
        <th class="px-4 py-2">Banco</th>
        <th class="px-4 py-2">Estado</th>
        <th class="px-4 py-2">Técnico</th>
        <th class="px-4 py-2">Fecha Entrada</th>
        <th class="px-4 py-2">Días sin movimiento</th>
        <th class="px-4 py-2">Acciones</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-100" id="tbody_sims">
      <!-- Contenido generado por JS -->
    </tbody>
  </table>
</div>

<script>
$(document).ready(function() {
  $('#tabla_sims').DataTable({
    ajax: 'sims/backend_inventario.php',
    columns: [
      { data: 'serie_sim' },
      { data: 'marca' },
      { data: 'banco' },
      { data: 'estado' },
      { data: 'tecnico_actual' },
      { data: 'fecha_entrada' },
      { data: 'dias_sin_movimiento' },
      { data: null, render: function(data, type, row) {
          return `<button class='bg-blue-500 text-white px-2 py-1 rounded text-xs' onclick=\"alert('Acciones para ${row.serie_sim}')\">⚙️ Acciones</button>`;
        }}
    ],
    responsive: true,
    order: [[6, 'desc']],
    dom: 'tip'
  });
});
</script>
