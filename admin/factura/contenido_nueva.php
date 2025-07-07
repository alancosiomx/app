<?php
require_once __DIR__ . '/../../config.php';

$clientes = $conn->query("SELECT id, razon_social FROM clientes ORDER BY razon_social");
?>

<div class="p-6">
  <h2 class="text-2xl font-bold mb-4">Generar Nueva Factura</h2>

  <form action="procesar_factura.php" method="POST" class="space-y-4 max-w-3xl">
    <div>
      <label class="block font-semibold mb-1">Cliente</label>
      <select name="cliente_id" class="w-full border p-2 rounded" required>
        <option value="">-- Selecciona un cliente --</option>
        <?php while($c = $clientes->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['razon_social']) ?></option>
        <?php endwhile; ?>
      </select>
      <a href="index.php?vista=clientes" class="text-sm text-blue-500 underline mt-1 inline-block">+ Agregar nuevo cliente</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block font-semibold mb-1">Origen</label>
        <input type="text" name="origen" class="w-full border p-2 rounded" required>
      </div>
      <div>
        <label class="block font-semibold mb-1">Destino</label>
        <input type="text" name="destino" class="w-full border p-2 rounded" required>
      </div>
      <div>
        <label class="block font-semibold mb-1">Precio (MXN)</label>
        <input type="number" name="precio" step="0.01" class="w-full border p-2 rounded" required>
      </div>
    </div>

    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">Generar Factura</button>
  </form>
</div>
