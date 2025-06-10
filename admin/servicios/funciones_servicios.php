<?php
// funciones_servicios.php

function getServiciosPorEstado(PDO $pdo, string $estado): array {
    $stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE actual_status = :estado ORDER BY fecha_limite ASC");
    $stmt->execute(['estado' => $estado]);
    return $stmt->fetchAll();
}

function asignarTecnico(PDO $pdo, string $ticket, string $idc): void {
    $stmt = $pdo->prepare("UPDATE servicios_omnipos SET idc = :idc, actual_status = 'En Ruta' WHERE ticket = :ticket");
    $stmt->execute(['idc' => $idc, 'ticket' => $ticket]);
}

function marcarResultado(PDO $pdo, string $ticket, string $resultado): void {
    $status = ($resultado === 'Exito' || $resultado === 'Rechazo') ? 'Histórico' : 'En Ruta';
    $stmt = $pdo->prepare("UPDATE servicios_omnipos SET conclusion = :resultado, actual_status = :status WHERE ticket = :ticket");
    $stmt->execute(['resultado' => $resultado, 'status' => $status, 'ticket' => $ticket]);
}
