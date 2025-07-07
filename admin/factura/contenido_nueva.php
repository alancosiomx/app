<?php
require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Obtener clientes y conceptos
$clientes = $pdo->query("SELECT id, razon_social FROM clientes ORDER BY razon_social")->fetchAll();
$conceptos = $pdo->query("SELECT id, descripcion, precio_unitario FROM conceptos_factura ORDER BY creado_en DESC")->fetchAll();
?>

<div class="p-6">
  <h2 class="text-2xl font-bold mb-4">Generar Nueva Factura</h2>

  <form action="procesar_factura.php" method="POST" class="space-y-6 max-w-5xl">
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

    <div>
      <label class="block font-semibold mb-2">Conceptos a facturar</label>
      <div id="conceptos-wrapper" class="space-y-3">
        <div class="grid grid-cols-3 gap-4 concepto-row mb-2">
          <select name="concepto_id[]" class="border p-2 rounded" required>
            <option value="">-- Selecciona concepto --</option>
            <?php foreach ($conceptos as $con): ?>
              <option value="<?= $con['id'] ?>">
                <?= htmlspecialchars($con['descripcion']) ?> - $<?= number_format($con['precio_unitario'], 2) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="number" name="cantidad[]" class="border p-2 rounded" placeholder="Cantidad" min="1" required>
          <button type="button" onclick="eliminarFila(this)" class="bg-red-600 text-white px-3 rounded">🗑</button>
        </div>
      </div>

      <button type="button" onclick="agregarConcepto()" class="text-sm text-blue-600 underline mt-2">+ Agregar otra línea</button>
    </div>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">
      Generar Factura
    </button>
  </form>
</div>

<script>
function agregarConcepto() {
  const wrapper = document.getElementById('conceptos-wrapper');
  const fila = wrapper.querySelector('.concepto-row');
  const clon = fila.cloneNode(true);

  // Resetear valores
  clon.querySelectorAll('select, input').forEach(el => el.value = '');
  wrapper.appendChild(clon);
}

function eliminarFila(btn) {
  const row = btn.closest('.concepto-row');
  if (document.querySelectorAll('.concepto-row').length > 1) {
    row.remove();
  }
}
</script>
