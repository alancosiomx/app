<?php
session_start();
require_once '../config.php'; // Incluye la conexión $pdo

$id = intval($_GET['id'] ?? 0); // ID de servicio

if (!$id) {
    echo "Servicio no encontrado.";
    exit;
}

// Obtener todos los datos del servicio
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "Servicio no encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle Servicio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="bg-white shadow p-4 text-center text-xl font-semibold">
  📄 Detalle del Servicio
</header>

<main class="p-4 space-y-4">
  <?php foreach ($servicio as $campo => $valor): ?>
    <div class="bg-white p-3 rounded-xl shadow">
      <div class="text-sm text-gray-500 uppercase"><?= htmlspecialchars(str_replace('_', ' ', $campo)) ?></div>
      <div class="text-lg font-semibold break-words"><?= htmlspecialchars($valor) ?></div>
    </div>
  <?php endforeach; ?>

  <div class="mt-6 text-center">
    <a href="mis_servicios.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-xl shadow hover:bg-blue-700">
      ⬅️ Regresar
    </a>
  </div>
</main>

</body>
</html>
