<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$nombre = $_SESSION['usuario_nombre'] ?? 'Técnico';
$contenido = $contenido ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Técnico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9f9fb] min-h-screen text-gray-800">

  <!-- Header fijo -->
  <header class="bg-white p-4 shadow sticky top-0 z-50 flex justify-between items-center max-w-md mx-auto">
    <div>
      <p class="text-sm text-gray-500">Bienvenido</p>
      <h1 class="text-xl font-bold"><?= htmlspecialchars($nombre) ?> 👋</h1>
    </div>
    <a href="/logout.php" class="text-red-500 text-sm">Salir</a>
  </header>

  <!-- Contenido dinámico -->
  <main class="max-w-md mx-auto p-4 pb-24">
    <?php
      if ($contenido && file_exists($contenido)) {
        include $contenido;
      } else {
        echo "<p class='text-gray-500'>Contenido no disponible.</p>";
      }
    ?>
  </main>


  <!-- Footer / Menú inferior tipo app -->
  <<?php include __DIR__ . '/includes/footer.php'; ?>


</body>
</html>
