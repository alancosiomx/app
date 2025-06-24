<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/service_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_nombre'])) {
    header("Location: /login.php");
    exit();
}


$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tickets_raw = $_POST['tickets'] ?? '';
    $fecha_cita = $_POST['fecha_cita'] ?? null;

    $tickets = array_filter(array_map('trim', explode(',', $tickets_raw)));
    $agendadas = 0;

    foreach ($tickets as $ticket) {
        // Agendar cita
        $stmt = $pdo->prepare("UPDATE servicios_omnipos SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\nCita programada para $fecha_cita.') WHERE ticket = ?");
        $stmt->execute([$fecha_cita, $ticket]);

        // Registrar visita
        $pdo->prepare("INSERT INTO visitas_servicios (ticket, fecha_visita, tipo_visita, resultado, creado_por) VALUES (?, ?, 'Cita', 'Pendiente', ?)")
            ->execute([$ticket, $fecha_cita, $_SESSION['usuario_nombre']]);

        // Log
        logServicio($pdo, $ticket, 'Cita Programada', $_SESSION['usuario_nombre'], "Fecha programada: $fecha_cita");

        $agendadas++;
    }

    $mensaje = "✅ Se agendaron $agendadas citas correctamente.";
}
?>

<h2 class="text-xl font-bold mb-4">📅 Programar Cita</h2>

<?php if ($mensaje): ?>
  <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= $mensaje ?></div>
<?php endif; ?>

<form method="post" class="bg-white shadow rounded-xl p-6 space-y-4 max-w-xl">
  <div>
    <label for="tickets" class="block text-sm font-medium text-gray-700">Tickets (separados por coma):</label>
    <input type="text" name="tickets" id="tickets" required placeholder="Ej: 12345678, 87654321"
           class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label for="fecha_cita" class="block text-sm font-medium text-gray-700">Fecha de la cita:</label>
    <input type="date" name="fecha_cita" id="fecha_cita" required
           class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div class="text-right">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
      Agendar Cita
    </button>
  </div>
</form>
