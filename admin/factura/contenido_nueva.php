<?php
// app/admin/facturacion/contenido_nueva.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

// Obtener clientes
$clientes = $pdo->query("SELECT id, razon_social FROM clientes ORDER BY razon_social ASC")->fetchAll();

// Obtener conceptos disponibles
$conceptos = $pdo->query("SELECT * FROM conceptos_factura ORDER BY descripcion ASC")->fetchAll();
?>

<div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
  <h2 class="text-2xl font-bold mb-6">🧾 Generar Nueva Factura</h2>

  <form action="procesar_factura.php" method="POST" class="space-y-4">

    <!-- CSRF -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <!-- Cliente -->
    <div>
      <label for="cliente_id" class="block font-semibold mb-1">Cliente</label>
      <select name="cliente_id" id="cliente_id" required class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Selecciona un cliente --</option>
        <?php foreach ($clientes as $cli): ?>
          <option value="<?= $cli['id'] ?>"><?= htmlspecialchars($cli['razon_social']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="index.php?vista=clientes" class="text-sm text-blue-600 mt-1 inline-block">+ Agregar nuevo cliente</a>
    </div>

    <!-- Conceptos dinámicos -->
    <div id="conceptos-container" class="space-y-3">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 concepto-item">
        <div>
          <label class="block text-sm font-semibold mb-1">Concepto</label>
          <select name="concepto_id[]" class="w-full border border-gray-300 rounded px-2 py-1" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($conceptos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['descripcion']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Cantidad</label>
          <input type="number" name="cantidad[]" class="w-full border border-gray-300 rounded px-2 py-1" value="1" min="1" required>
        </div>
        <div class="flex items-end">
          <button type="button" onclick="eliminarConcepto(this)" class="text-red-600 text-sm">🗑 Quitar</button>
        </div>
      </div>
    </div>

    <button type="button" onclick="agregarConcepto()" class="text-sm text-blue-700 mt-2">+ Agregar otro concepto</button>

    <!-- Submit -->
    <div>
      <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
        Generar Factura
      </button>
    </div>

  </form>
</div>

<script>
function agregarConcepto() {
  const container = document.getElementById('conceptos-container');
  const item = container.querySelector('.concepto-item');
  const clone = item.cloneNode(true);
  clone.querySelectorAll('input, select').forEach(el => {
    if (el.tagName === 'SELECT') el.selectedIndex = 0;
    if (el.tagName === 'INPUT') el.value = 1;
  });
  container.appendChild(clone);
}

function eliminarConcepto(btn) {
  const item = btn.closest('.concepto-item');
  const container = document.getElementById('conceptos-container');
  if (container.children.length > 1) {
    item.remove();
  }
}
</script>
