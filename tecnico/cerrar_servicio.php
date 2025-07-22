<?php
require_once __DIR__ . '/init.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ticket = $_GET['ticket'] ?? null;
$idc = $_SESSION['usuario_nombre'] ?? null;

// 🚫 Sin ticket
if (!$ticket) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ❌ Ticket no proporcionado. Agrega ?ticket=XXXXXXXX en la URL.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}

// 🚫 Sin técnico
if (!$idc) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ⚠️ No hay técnico en sesión. Por favor vuelve a iniciar sesión.
        <div class='mt-2'><a href='/login.php' class='text-blue-600 underline'>Ir al login</a></div>
    </div>";
    exit;
}

// ✅ Validar servicio asignado
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ? AND estatus = 'En Ruta' AND idc = ?");
$stmt->execute([$ticket, $idc]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "<div class='bg-yellow-100 text-yellow-800 p-4 rounded border border-yellow-300 font-semibold'>
        ❌ Este servicio no está asignado a ti o no está En Ruta.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}

// ❌ Ya fue cerrado antes
$checkCierre = $pdo->prepare("SELECT id FROM cierres_servicio WHERE ticket = ?");
$checkCierre->execute([$ticket]);
if ($checkCierre->fetch()) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ⚠️ Este servicio ya fue cerrado previamente.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar servicio <?= htmlspecialchars($ticket) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
  <div class="max-w-lg mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">✅ Cerrar servicio</h1>
    <p class="mb-4 text-sm text-gray-600">Ticket: <strong><?= htmlspecialchars($ticket) ?></strong></p>

    <form method="POST" action="procesar_cierre.php" class="space-y-4">
      <input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">

      <label class="block text-sm font-medium text-gray-700">
        Nombre de quien atiende (comercio):
        <input type="text" name="atiende" required autocomplete="off"
               class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. Luis Ramírez">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Resultado del servicio:
        <select name="resultado" required class="mt-1 w-full border rounded p-2 text-sm">
          <option value="">-- Seleccionar --</option>
          <option value="Éxito">✅ Éxito</option>
          <option value="Rechazo">❌ Rechazo</option>
        </select>
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Serie instalada:
        <input type="text" name="serie_instalada" autocomplete="off"
               class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 123456">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Serie retirada:
        <input type="text" name="serie_retirada" autocomplete="off"
               class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 654321">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Comentarios del técnico:
        <textarea name="observaciones" rows="3" class="mt-1 w-full border rounded p-2 text-sm"
                  placeholder="Notas adicionales..."></textarea>
      </label>

      <button type="submit"
              class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">
        Guardar y cerrar servicio
      </button>
    </form>
  </div>
</body>
</html>
