<?php
session_start();
require_once '../config.php'; // Incluye tu conexión PDO

$idc = $_SESSION['usuario'] ?? null; // ID técnico logueado

if (!$idc) {
    header('Location: /login.php');
    exit;
}

// --- Procesar formulario ---
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = trim($_POST['ticket']);
    $resultado = trim($_POST['resultado']);
    $comentarios = trim($_POST['comentarios'] ?? '');

    if ($ticket && $resultado) {
        if (in_array($resultado, ['Éxito', 'Rechazo', 'Reasignar'])) {
            if (($resultado == 'Rechazo' || $resultado == 'Reasignar') && empty($comentarios)) {
                $mensaje = "⚠️ El comentario es obligatorio en caso de Rechazo o Reasignar.";
            } else {
                $nuevo_estatus = ($resultado == 'Reasignar') ? 'En Ruta' : 'Histórico';

                $stmt = $pdo->prepare("
                    UPDATE servicios_omnipos 
                    SET resultado = :resultado, 
                        fecha_atencion = NOW(), 
                        estatus = :estatus, 
                        observaciones = :comentarios 
                    WHERE ticket = :ticket AND idc = :idc
                ");

                $stmt->execute([
                    'resultado' => $resultado,
                    'estatus' => $nuevo_estatus,
                    'comentarios' => $comentarios,
                    'ticket' => $ticket,
                    'idc' => $idc
                ]);

                $mensaje = "✅ Servicio cerrado correctamente.";
            }
        } else {
            $mensaje = "⚠️ Resultado no válido.";
        }
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
}

// --- Cargar tickets disponibles ---
$stmt = $pdo->prepare("
    SELECT ticket 
    FROM servicios_omnipos 
    WHERE idc = :idc AND estatus = 'En Ruta'
");
$stmt->execute(['idc' => $idc]);
$tickets = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cerrar Servicio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function toggleComentarios() {
      const resultado = document.getElementById('resultado').value;
      const comentariosBox = document.getElementById('comentarios-box');
      if (resultado === 'Rechazo' || resultado === 'Reasignar') {
        comentariosBox.style.display = 'block';
      } else {
        comentariosBox.style.display = 'none';
      }
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="bg-white shadow p-4 text-center text-xl font-semibold">
  ✅ Cerrar Servicio
</header>

<main class="p-4">
  <?php if (!empty($mensaje)): ?>
    <div class="mb-4 p-3 rounded-xl shadow 
                <?= strpos($mensaje, '✅') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
      <?= htmlspecialchars($mensaje) ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4 bg-white p-4 rounded-2xl shadow">
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Ticket:</label>
      <select name="ticket" class="w-full border rounded-lg p-2" required>
        <option value="">Selecciona un Ticket</option>
        <?php foreach ($tickets as $ticket): ?>
          <option value="<?= htmlspecialchars($ticket) ?>"><?= htmlspecialchars($ticket) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold mb-1">Resultado:</label>
      <select name="resultado" id="resultado" onchange="toggleComentarios()" class="w-full border rounded-lg p-2" required>
        <option value="">Selecciona un Resultado</option>
        <option value="Éxito">✅ Éxito</option>
        <option value="Rechazo">❌ Rechazo</option>
        <option value="Reasignar">🔁 Reasignar</option>
      </select>
    </div>

    <div id="comentarios-box" style="display: none;">
      <label class="block text-gray-700 font-semibold mb-1">Comentarios:</label>
      <textarea name="comentarios" rows="3" class="w-full border rounded-lg p-2"></textarea>
    </div>

    <div class="text-center">
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl shadow hover:bg-green-700">
        Cerrar Servicio
      </button>
    </div>
  </form>
</main>

</body>
</html>
