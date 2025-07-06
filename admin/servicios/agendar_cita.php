<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/service_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tickets_raw = $_POST['tickets'] ?? '';
    $fecha_cita = $_POST['fecha_cita'] ?? null;
    $tickets = array_filter(array_map('trim', explode(',', $tickets_raw)));

    $agendadas = 0;

    foreach ($tickets as $ticket) {
        // Revisión del estado actual
        $stmt = $pdo->prepare("SELECT estatus, resultado FROM servicios_omnipos WHERE ticket = ?");
        $stmt->execute([$ticket]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) continue; // Ticket inválido

        $estatus = $servicio['estatus'];
        $resultado = $servicio['resultado'];

        if ($estatus === 'Por Asignar' || $estatus === 'En Ruta') {
            // Solo actualizar fecha_cita y pasar a En Ruta si no lo está
            $pdo->prepare("UPDATE servicios_omnipos 
                           SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nCita programada para $fecha_cita.') 
                           WHERE ticket = ?")
                ->execute([$fecha_cita, $ticket]);

        } elseif ($estatus === 'Histórico' && strtolower($resultado) === 'rechazo') {
            // Reabrir el servicio con una nueva cita
            $pdo->prepare("UPDATE servicios_omnipos 
                           SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nReagenda por rechazo - cita para $fecha_cita.') 
                           WHERE ticket = ?")
                ->execute([$fecha_cita, $ticket]);
        } else {
            continue; // No se agenda si ya está concluido como éxito o cancelado
        }

        logServicio($pdo, $ticket, 'Cita Programada', $_SESSION['usuario_nombre'], "Fecha programada: $fecha_cita");
        $agendadas++;
    }

    $_SESSION['mensaje'] = "✅ Se agendaron $agendadas citas correctamente.";
}

header("Location: index.php?tab=citas");
exit;
