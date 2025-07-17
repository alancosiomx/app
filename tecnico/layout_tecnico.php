<?php
session_start();
include __DIR__ . '/includes/head_tecnico.php';
include __DIR__ . '/includes/menu_tecnico.php';
?>

<main class="p-4 space-y-4 max-w-5xl mx-auto">
  <!-- Botón de regreso -->
  <div>
    <a href="index.php" class="inline-block text-blue-600 hover:underline text-sm">← Regresar al Panel</a>
  </div>

  <?php include $contenido; ?>
</main>

<?php include __DIR__ . '/includes/footer_tecnico.php'; ?>
