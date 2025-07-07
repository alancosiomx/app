<?php
require_once __DIR__ . '/../../config.php';

// Obtener clientes
$clientes = $pdo->query("SELECT id, razon_social FROM clientes ORDER BY razon_social")->fetchAll();
?>

<div class="p-6">
  <h2 class="text-2xl font-bold mb-4">Generar Nueva Factura</h2>

  <form action="procesar_factura.php" method="POST" class="space-y-4 max-w-4xl">
  <div>
    <label class="block font-semibold mb-1">Cliente</label>
    <select name="cliente_id" class="w-full border p-2 rounded" required>
      <option value="">-- Selecciona un cliente --</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['razon_social']) ?></option>
      <?php endforeach; ?>
    </select>
    <a href="index.php?vista=clientes" class="text-sm text-blue-500 underline mt-1 inline-block">+ Agregar nuevo cliente</a>
  </div>

  <!-- Tabla dinámica de conceptos -->
  <div class="space-y-2">
    <div id="conceptos-wrapper">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 concepto-row mb-2">
        <input type="text" name="origen[]" placeholder="Origen" class="border p-2 rounded w-full" required>
        <input type="text" name="destino[]" placeholder="Destino" class="border p-2 rounded w-full" required>
        <input type="number" name="cantidad[]" placeholder="Cantidad" class="border p-2 rounded w-full" min="1" required>
        <input type="number" name="precio[]" placeholder="Precio unitario" class="border p-2 rounded w-full" step="0.01" required>
      </div>
    </div>

    <button type="button" onclick="agregarConcepto()" class="text-sm text-blue-600 underline">+ Agregar otra línea</button>
  </div>

  <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">
    Generar Factura
  </button>
</form>

<script>
function agregarConcepto() {
  const wrapper = document.getElementById('conceptos-wrapper');
  const nuevaFila = document.createElement('div');
  nuevaFila.classList.add('grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4', 'concepto-row', 'mb-2');
  nuevaFila.innerHTML = `
    <input type="text" name="origen[]" placeholder="Origen" class="border p-2 rounded w-full" required>
    <input type="text" name="destino[]" placeholder="Destino" class="border p-2 rounded w-full" required>
    <input type="number" name="cantidad[]" placeholder="Cantidad" class="border p-2 rounded w-full" min="1" required>
    <input type="number" name="precio[]" placeholder="Precio unitario" class="border p-2 rounded w-full" step="0.01" required>
  `;
  wrapper.appendChild(nuevaFila);
}
</script>

</div>
