<?php
require_once '../../config.php';
require_once __DIR__ . '/service_functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tickets = explode(',', $_POST['tickets'] ?? '');
    $fecha_cita = $_POST['fecha_cita'] ?? null;

    $tickets = array_map('trim', $tickets);
    $tickets = array_filter($tickets);

    $agendadas = 0;
    foreach ($tickets as $ticket) {
        $update = $pdo->prepare("UPDATE servicios_omnipos SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nCita programada para $fecha_cita.') WHERE ticket = ?");
        $update->execute([$fecha_cita, $ticket]);

        $pdo->prepare("INSERT INTO visitas_servicios (ticket, fecha_visita, tipo_visita, resultado, creado_por) VALUES (?, ?, 'Cita', 'Pendiente', ?)")
            ->execute([$ticket, $fecha_cita, $_SESSION['usuario_nombre']]);

        logServicio($pdo, $ticket, 'Cita Programada', $_SESSION['usuario_nombre'], "Fecha programada: $fecha_cita");

        $agendadas++;
    }

    echo "<div class='alert alert-success'>✅ Se agendaron $agendadas citas correctamente.</div>";
}
?>

<h3>Programar Cita</h3>

<form method="post">
  <div class="mb-3">
    <label for="tickets" class="form-label">Tickets (separados por coma):</label>
    <input type="text" name="tickets" id="tickets" class="form-control" placeholder="Ej: 12345678, 87654321" required>
  </div>

  <div class="mb-3">
    <label for="fecha_cita" class="form-label">Fecha de la cita:</label>
    <input type="date" name="fecha_cita" id="fecha_cita" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-primary">Agendar Cita</button>
</form>
