<?php
$usuario = $_SESSION['usuario_nombre'] ?? '';
$alertas_pendientes = 0;

if ($usuario && isset($pdo)) {
  $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM alertas a
    LEFT JOIN alertas_leidas l ON a.id = l.id_alerta AND l.usuario = ?
    WHERE (a.destinatario IS NULL OR a.destinatario = ?) AND l.id IS NULL
  ");
  $stmt->execute([$usuario, $usuario]);
  $alertas_pendientes = $stmt->fetchColumn();
}
?>

<!-- Footer / Menú inferior tipo app -->
<footer class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white shadow-inner rounded-t-2xl px-6 py-3 flex justify-around text-xl text-gray-500">
  <a href="index.php" class="text-purple-600">🏠</a>
  <a href="mis_servicios.php">🧾</a>
  <a href="inventario.php">📦</a>
  <a href="alertas.php" class="relative">
    🔔
    <?php if ($alertas_pendientes > 0): ?>
      <span class="absolute top-0 right-0 bg-red-600 text-white text-xs rounded-full px-1 leading-none">!</span>
    <?php endif; ?>
  </a>
</footer>
