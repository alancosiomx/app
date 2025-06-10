<?php
session_start();
require_once '../config.php'; // Aquí ya tienes $pdo

$idc = $_SESSION['usuario'] ?? null; // Técnico logueado

if (!$idc) {
    header('Location: /login.php');
    exit;
}

// Obtener servicios asignados con PDO
$stmt = $pdo->prepare("
    SELECT id, ticket, afiliacion, comercio, servicio, horario, resultado 
    FROM servicios_omnipos 
    WHERE idc = :idc AND estatus = 'En Ruta'
    ORDER BY fecha_inicio DESC
");

$stmt->execute(['idc' => $idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Servicios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="bg-white shadow p-4 text-center text-xl font-semibold">
  📋 Mis Servicios
</header>

<main class="p-4 space-y-4">
  <?php if (empty($servicios)): ?>
    <div class="text-center text-gray-500">
      No tienes servicios asignados por ahora.
    </div>
  <?php else: ?>
    <?php foreach ($servicios as $servicio): ?>
      <a href="detalle_servicio.php?id=<?= $servicio['id'] ?>" 
         class="block p-4 rounded-2xl shadow transition
         <?= empty($servicio['resultado']) ? 'bg-gray-100 hover:bg-gray-200' : 'bg-white hover:bg-gray-50' ?>">
        <div class="space-y-1">
          <div><span class="font-semibold">🎫 Ticket:</span> <?= htmlspecialchars($servicio['ticket']) ?></div>
          <div><span class="font-semibold">🆔 Afiliación:</span> <?= htmlspecialchars($servicio['afiliacion']) ?></div>
          <div><span class="font-semibold">🏬 Comercio:</span> <?= htmlspecialchars($servicio['comercio']) ?></div>
          <div><span class="font-semibold">🛠️ Servicio:</span> <?= htmlspecialchars($servicio['servicio']) ?></div>
          <div><span class="font-semibold">🕑 Horario:</span> <?= htmlspecialchars($servicio['horario']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

</body>
</html>
