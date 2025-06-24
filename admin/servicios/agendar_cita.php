<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/service_functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tickets = explode(',', $_POST['tickets'] ?? '');
    $fecha_cita = $_POST['fecha_cita'] ?? null;

    $tickets = array_map('trim', $tickets);
    $tickets = array_filter($tickets);

    $agendadas = 0;
    foreach ($tickets as $ticket) {
        $pdo->prepare("UPDATE servicios_omnipos SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nCita programada para $fecha_cita.') WHERE ticket = ?")
            ->execute([$fecha_cita, $ticket]);

        $pdo->prepare("INSERT INTO visitas_servicios (ticket, fecha_visita, tipo_visita, resultado, creado_por) VALUES (?, ?, 'Cita', 'Pendiente', ?)")
            ->execute([$ticket, $fecha_cita, $_SESSION['usuario_nombre']]);

        logServicio($pdo, $ticket, 'Cita Programada', $_SESSION['usuario_nombre'], "Fecha programada: $fecha_cita");
        $agendadas++;
    }

    $_SESSION['mensaje'] = "✅ Se agendaron $agendadas citas correctamente.";
}

header("Location: index.php?tab=citas");
exit;
