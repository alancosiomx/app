<?php
// app/admin/facturacion/contenido_historial.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

// Obtener últimas facturas
$stmt = $pdo->query("
  SELECT f.id, f.uuid, f.cliente_id, c.razon_social, f.total, f.fecha_creacion
  FROM facturas f
  LEFT JOIN clientes c ON f.cliente_id = c.id
  ORDER BY f.fecha_creacion DESC
  LIMIT 50
");
$facturas = $stmt->fetchAll();
?>

<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
  <h2 class="text-2xl font-bold mb-6">📜 Historial de Facturas</h2>

  <?php if (empty($facturas)): ?>
    <p class="text-gray-600">No hay facturas generadas aún.</p>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="text-left px-4 py-2 border-b">UUID</th>
            <th class="text-left px-4 py-2 border-b">Cliente</th>
            <th class="text-left px-4 py-2 border-b">Total</th>
            <th class="text-left px-4 py-2 border-b">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($facturas as $f): ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= htmlspecialchars($f['uuid']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($f['razon_social']) ?></td>
              <td class="px-4 py-2 text-right">$<?= number_format($f['total'], 2) ?></td>
              <td class="px-4 py-2"><?= date("d/m/Y H:i", strtotime($f['fecha_creacion'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
