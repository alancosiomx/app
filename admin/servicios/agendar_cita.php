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
        $stmt = $pdo->prepare("SELECT estatus, resultado, idc FROM servicios_omnipos WHERE ticket = ?");
        $stmt->execute([$ticket]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) continue; // Ticket inválido

        $estatus = $servicio['estatus'];
        $resultado = $servicio['resultado'];
        $idc = $servicio['idc'];

        if ($estatus === 'Por Asignar' || $estatus === 'En Ruta') {
            $pdo->prepare("UPDATE servicios_omnipos 
                           SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nCita programada para $fecha_cita.') 
                           WHERE ticket = ?")
                ->execute([$fecha_cita, $ticket]);

        } elseif ($estatus === 'Histórico' && strtolower($resultado) === 'rechazo') {
            $pdo->prepare("UPDATE servicios_omnipos 
                           SET estatus = 'En Ruta', fecha_cita = ?, observaciones = CONCAT(IFNULL(observaciones, ''), '\\nReagenda por rechazo - cita para $fecha_cita.') 
                           WHERE ticket = ?")
                ->execute([$fecha_cita, $ticket]);
        } else {
            continue; // No se agenda si ya está concluido como éxito o cancelado
        }

        // ✅ REGISTRAR VISITA EN TABLA visitas_servicios
        $pdo->prepare("INSERT INTO visitas_servicios 
                        (ticket, fecha_visita, idc, resultado, tipo_visita, comentarios, creado_por)
                       VALUES (?, ?, ?, 'Observación', 'Cita', 'Cita programada desde módulo', ?)")
            ->execute([$ticket, $fecha_cita, $idc, $_SESSION['usuario_nombre']]);

        logServicio($pdo, $ticket, 'Cita Programada', $_SESSION['usuario_nombre'], "Fecha programada: $fecha_cita");
        $agendadas++;
    }

    $_SESSION['mensaje'] = "✅ Se agendaron $agendadas citas correctamente.";
}

header("Location: index.php?tab=citas");
exit;
