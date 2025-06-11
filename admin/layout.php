<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // ← evita problemas con header()

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel OMNIPOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>

<!-- Barra superior -->
<div class="top-bar">
  <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
  <span>
    👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.
  </span>
</div>

<!-- Sidebar -->
<?php require_once __DIR__ . '/includes/menu.php'; ?>

<!-- Contenido principal -->
<div class="main-content">
  <?php
  if (isset($contenido) && file_exists($contenido)) {
      include $contenido;
  } else {
      echo '<div class="alert alert-danger">❌ Error: contenido no encontrado.</div>';
  }
  ?>
</div>

<script>
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('collapsed');
  }
</script>

<?php
require_once __DIR__ . '/includes/foot.php';
ob_end_flush(); // ← cierra buffer de salida
?>
</body>
</html>
