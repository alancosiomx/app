<?php
// service_functions.php
// Funciones reutilizables para el módulo de servicios OMNIPOS

/**
 * Registrar una acción en el log de servicios.
 *
 * @param PDO $pdo Instancia PDO activa
 * @param string $ticket Número de ticket afectado
 * @param string $accion Tipo de acción realizada (Asignación, Cierre, Cita, etc.)
 * @param string $usuario Nombre del usuario que hizo la acción
 * @param string $detalles Texto adicional con descripción o cambios
 */
function logServicio($pdo, $ticket, $accion, $usuario, $detalles = '') {
    $stmt = $pdo->prepare("INSERT INTO log_servicios (ticket, accion, usuario, detalles) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ticket, $accion, $usuario, $detalles]);
}
