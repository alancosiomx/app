<?php
// Este archivo se asume que ya está dentro de admin/
require_once __DIR__ . '/includes/head.php'; // Carga <link> y configuraciones
require_once __DIR__ . '/includes/menu.php'; // Menú lateral
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel OMNIPOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <?php // Todo lo que esté en head.php debe ir aquí adentro ?>
</head>

<body>

  <!-- Barra superior -->
  <div class="top-bar">
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
    <span>
      👋 Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></strong>. Este es tu panel de administración.
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

  <!-- Scripts (puedes moverlos a foot.php si quieres) -->
  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('collapsed');
      document.querySelector('.main-content').classList.toggle('collapsed');
    }
  </script>

<?php require_once __DIR__ . '/includes/foot.php'; ?>
</body>
</html>
