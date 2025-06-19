<?php
require_once __DIR__ . '/../../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = $_POST['ticket'] ?? '';
    $idc = $_SESSION['usuario_nombre'] ?? 'Desconocido';
    $resultado = $_POST['resultado'] ?? '';
    $solucion = $_POST['solucion'] ?? '';
    $solucion_especifica = $_POST['solucion_especifica'] ?? null;
    $serie_instalada = $_POST['serie_instalada'] ?? null;
    $serie_retiro = $_POST['serie_retiro'] ?? null;
    $recibio_cliente = $_POST['recibio_cliente'] ?? '';
    $comentarios = $_POST['comentarios'] ?? '';
    $fecha_cierre = date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        // Obtener fecha límite del servicio
        $stmt = $pdo->prepare("SELECT fecha_limite FROM servicios_omnipos WHERE ticket = ?");
        $stmt->execute([$ticket]);
        $fecha_limite = $stmt->fetchColumn();

        $sla = (strtotime($fecha_cierre) > strtotime($fecha_limite)) ? 'FT' : 'DT';

        // 1. Insertar en cierres_servicios
        $stmt = $pdo->prepare("INSERT INTO cierres_servicios 
        (ticket, idc, resultado, solucion, solucion_especifica, serie_instalada, serie_retiro, recibio_cliente, comentarios, fecha_cierre)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $ticket, $idc, $resultado, $solucion, $solucion_especifica,
            $serie_instalada, $serie_retiro, $recibio_cliente, $comentarios, $fecha_cierre
        ]);

        // 2. Registrar visita
        $stmt = $pdo->prepare("INSERT INTO visitas_servicios 
        (ticket, fecha_visita, idc, resultado, tipo_visita, comentarios, creado_por)
        VALUES (?, ?, ?, ?, 'Normal', ?, ?)");
        $stmt->execute([
            $ticket, $fecha_cierre, $idc, $resultado, $comentarios, $idc
        ]);

        // 3. Actualizar servicios_omnipos
        $stmt = $pdo->prepare("UPDATE servicios_omnipos 
            SET conclusion = ?, resultado = ?, estatus = 'Histórico', fecha_cierre = ?, sla = ?, 
                serie_instalada = ?, serie_retiro = ?
            WHERE ticket = ?");
        $stmt->execute([
            $resultado, $solucion, $fecha_cierre, $sla, $serie_instalada, $serie_retiro, $ticket
        ]);

        // 4. Log en log_servicios
        $stmt = $pdo->prepare("INSERT INTO log_servicios (ticket, accion, usuario, detalles) 
        VALUES (?, 'Cierre de servicio', ?, ?)");
        $stmt->execute([$ticket, $idc, "Resultado: $resultado, Solución: $solucion"]);

        // 5. Inventario - Serie instalada
        if (!empty($serie_instalada)) {
            $stmt = $pdo->prepare("UPDATE inventario_tpv SET estado = 'Instalado', tecnico_actual = ? WHERE serie = ?");
            $stmt->execute([$idc, $serie_instalada]);

            $stmt = $pdo->prepare("INSERT INTO log_inventario (serie, tipo_movimiento, usuario, observaciones) 
            VALUES (?, 'Instalación', ?, ?)");
            $stmt->execute([$serie_instalada, $idc, "Instalada en ticket $ticket"]);
        }

        // 6. Inventario - Serie retirada
        if (!empty($serie_retiro)) {
            $stmt = $pdo->prepare("UPDATE inventario_tpv SET estado = 'Retirado' WHERE serie = ?");
            $stmt->execute([$serie_retiro]);

            $stmt = $pdo->prepare("INSERT INTO log_inventario (serie, tipo_movimiento, usuario, observaciones) 
            VALUES (?, 'Retiro', ?, ?)");
            $stmt->execute([$serie_retiro, $idc, "Retirada en ticket $ticket"]);
        }

        $pdo->commit();
        echo "✅ Servicio cerrado correctamente.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . $e->getMessage();
    }
} else {
    echo "Acceso no permitido.";
}
