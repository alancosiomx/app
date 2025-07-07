<?php
// app/admin/facturacion/contenido_historial.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

// Asegúrate que FISCALPOP_TOKEN esté definido en config.php
// define('FISCALPOP_TOKEN', 'tu-token-aqui');

$stmt = $pdo->query("
  SELECT 
    f.id,
    f.uuid,
    f.origen,
    f.destino,
    f.precio,
    f.fecha,
    c.razon_social AS cliente,
    u.nombre AS usuario
  FROM facturas f
  LEFT JOIN clientes c ON f.cliente_id = c.id
  LEFT JOIN usuarios u ON f.id_usuario = u.id
  ORDER BY f.fecha DESC
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
            <th class="text-left px-4 py-2 border-b">Origen</th>
            <th class="text-left px-4 py-2 border-b">Destino</th>
            <th class="text-left px-4 py-2 border-b">Precio</th>
            <th class="text-left px-4 py-2 border-b">Fecha</th>
            <th class="text-left px-4 py-2 border-b">Usuario</th>
            <th class="text-left px-4 py-2 border-b">PDF</th>
            <th class="text-left px-4 py-2 border-b">XML</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($facturas as $f):
            $pdf_url = "https://api.fiscalpop.com/api/v1/cfdi/pdf/" . $f['uuid'] . "?token=" . FISCALPOP_TOKEN;
            $xml_url = "https://api.fiscalpop.com/api/v1/cfdi/xml/" . $f['uuid'] . "?token=" . FISCALPOP_TOKEN;
          ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= htmlspecialchars($f['uuid']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($f['cliente']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($f['origen']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($f['destino']) ?></td>
              <td class="px-4 py-2 text-right">$<?= number_format($f['precio'], 2) ?></td>
              <td class="px-4 py-2"><?= date("d/m/Y H:i", strtotime($f['fecha'])) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($f['usuario']) ?></td>
              <td class="px-4 py-2">
                <a href="<?= htmlspecialchars($pdf_url) ?>" target="_blank" class="text-blue-600 hover:underline">PDF</a>
              </td>
              <td class="px-4 py-2">
                <a href="<?= htmlspecialchars($xml_url) ?>" target="_blank" class="text-blue-600 hover:underline">XML</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
