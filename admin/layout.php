<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-white w-64 hidden md:block border-r border-gray-300 fixed inset-y-0 left-0 z-40">
    <?php require_once __DIR__ . '/includes/menu.php'; ?>
  </aside>

  <!-- Contenido principal -->
  <div class="flex-1 md:ml-64 w-full">

    <!-- Barra superior -->
    <header class="bg-white shadow sticky top-0 z-30">
      <div class="flex items-center justify-between px-4 py-3">
        <button onclick="toggleSidebar()" class="text-2xl md:hidden">☰</button>
        <span class="text-sm text-gray-700">👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong></span>
      </div>
    </header>

    <!-- Contenido -->
    <!-- Contenido -->
<main class="p-4">
  <?php
  // Cargar el contenido dinámico
  if (isset($contenido) && file_exists($contenido)) {
      include $contenido;
  } else {
      echo '<div class="text-red-600 font-semibold">❌ Error: contenido no encontrado.</div>';
  }
  ?>
</main>


  </div>
</div>

<!-- Modal personalizado Tailwind -->
<div id="modal-container"></div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('hidden')) {
      sidebar.classList.remove('hidden');
    } else {
      sidebar.classList.add('hidden');
    }
  }

  <?php if (str_contains($_SERVER['REQUEST_URI'], '/servicios/')): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(e) {
      const btn = e.target.closest('.ver-detalle');
      if (!btn) return;

      const ticket = btn.dataset.ticket;

      fetch('/admin/servicios/detalle_servicio.php?ticket=' + encodeURIComponent(ticket)) // ✅ RUTA ABSOLUTA
        .then(res => res.text())
        .then(html => {
          const anterior = document.getElementById('modalDetalleServicio');
          if (anterior) anterior.remove();

          document.getElementById('modal-container').innerHTML = html;
        })
        .catch(err => {
          alert('Error al cargar el detalle del servicio.');
          console.error(err);
        });
    });
  });
</script>
<?php endif; ?>

</script>

<?php if (file_exists(__DIR__ . '/includes/foot.php')) require_once __DIR__ . '/includes/foot.php'; ?>
<?php ob_end_flush(); ?>
</body>
</html>
