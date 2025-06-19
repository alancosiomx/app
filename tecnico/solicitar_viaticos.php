<?php
require_once __DIR__ . '/../config.php';
session_start();

$tecnico = $_SESSION['usuario_nombre'] ?? '';
$mensaje = null;
$error = null;

// Tickets concluidos por el técnico
$stmt = $pdo->prepare("SELECT ticket FROM servicios_omnipos WHERE idc = ? AND estatus = 'Histórico' ORDER BY fecha_cierre DESC");
$stmt->execute([$tecnico]);
$tickets = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Procesar nueva solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = $_POST['ticket'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if (!$ticket || !$monto || !$motivo) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!is_numeric($monto) || $monto <= 0) {
        $error = "Monto inválido.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO viaticos (ticket, idc, monto, motivo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$ticket, $tecnico, $monto, $motivo]);
        $mensaje = "✅ Solicitud enviada correctamente.";
    }
}

// Obtener historial de solicitudes
$stmt = $pdo->prepare("SELECT * FROM viaticos WHERE idc = ? ORDER BY fecha_solicitud DESC");
$stmt->execute([$tecnico]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contenido = __FILE__;
include __DIR__ . '/layout_tecnico.php';
?>

<div class="p-4">
  <h2 class="text-lg font-bold mb-3">💰 Solicitar Viáticos</h2>

  <?php if ($mensaje): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4"><?= $mensaje ?></div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="space-y-4 mb-6">
    <label class="block font-medium">Ticket relacionado:</label>
    <select name="ticket" required class="w-full border p-2 rounded">
      <option value="">-- Selecciona ticket --</option>
      <?php foreach ($tickets as $t): ?>
        <option value="<?= $t ?>"><?= $t ?></option>
      <?php endforeach; ?>
    </select>

    <label class="block font-medium">Monto solicitado (MXN):</label>
    <input type="number" step="0.01" name="monto" required class="w-full border p-2 rounded" placeholder="Ej. 300.00" />

    <label class="block font-medium">Motivo:</label>
    <textarea name="motivo" rows="3" required class="w-full border p-2 rounded" placeholder="Ej. Transporte a zona rural"></textarea>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enviar Solicitud</button>
  </form>

  <?php if ($solicitudes): ?>
    <h3 class="text-md font-semibold mb-2">🧾 Historial de Solicitudes</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white text-sm rounded shadow">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Ticket</th>
            <th class="px-4 py-2 text-left">Monto</th>
            <th class="px-4 py-2 text-left">Motivo</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($solicitudes as $s): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($s['ticket']) ?></td>
              <td class="px-4 py-2">$<?= number_format($s['monto'], 2) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($s['motivo']) ?></td>
              <td class="px-4 py-2">
                <?php
                echo match($s['estado']) {
                  'Aprobado' => '✅ Aprobado',
                  'Rechazado' => '❌ Rechazado',
                  default => '⏳ Pendiente',
                };
                ?>
              </td>
              <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-sm text-gray-500">Aún no has hecho solicitudes de viáticos.</p>
  <?php endif; ?>
</div>
