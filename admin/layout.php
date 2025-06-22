<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // Evita problemas con header()

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<div class="min-h-screen flex"> <div class="hidden md:block w-64 bg-white border-r z-40">
    <?php require_once __DIR__ . '/includes/menu.php'; ?>
  </div>

  <div class="md:pl-64 flex flex-col flex-1"> <header class="bg-white shadow sticky top-0 z-30">
      <div class="flex items-center justify-between px-4 py-3">
        <button onclick="toggleSidebar()" class="text-xl md:hidden">☰</button>
        <span class="text-sm text-gray-700">👋 ¡Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>!</span>
      </div>
    </header>

    <main class="p-4 flex-grow overflow-y-auto"> <?php
      if (isset($contenido) && file_exists($contenido)) {
          include $contenido;
      } else {
          echo '<div class="text-red-600 font-semibold">❌ Error: contenido no encontrado.</div>';
      }
      ?>
    </main>

  </div>
</div>

<script>
  function toggleSidebar() {
    const sidebar = document.querySelector('.w-64.bg-white.border-r'); // Seleccionamos el sidebar por sus clases
    sidebar.classList.toggle('hidden'); // Para móvil, simplemente lo ocultamos/mostramos
    // Si quieres un efecto de deslizamiento, necesitarías una clase como '-translate-x-full'
    // y una clase para resetearla, además de un elemento específico para el sidebar móvil.
  }
</script>

<?php require_once __DIR__ . '/includes/foot.php'; ?>
<?php ob_end_flush(); ?>

<?php if (str_contains($_SERVER['REQUEST_URI'], '/servicios/')): ?>
  <div id="modal-container"></div>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(e) {
      const btn = e.target.closest('.ver-detalle');
      if (!btn) return;

      e.preventDefault();
      const ticket = btn.dataset.ticket;

      fetch('servicios/detalle_servicio.php?ticket=' + encodeURIComponent(ticket))
        .then(res => res.text())
        .then(html => {
          const anterior = document.getElementById('modalDetalleServicio');
          if (anterior) anterior.remove();

          document.getElementById('modal-container').innerHTML = html;

          const modal = new bootstrap.Modal(document.getElementById('modalDetalleServicio'));
          modal.show();
        })
        .catch(err => {
          alert('Error al cargar el detalle del servicio.');
          console.error(err);
        });
    });
  });
  </script>
<?php endif; ?>

</body>
</html>
