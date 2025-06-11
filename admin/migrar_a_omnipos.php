<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';

$insertados = 0;
$errores = 0;

function convertirFecha($fecha) {
    if (empty($fecha)) {
        return null;
    }
    $timestamp = strtotime($fecha);
    return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
}

try {
    $query = $pdo->query("SELECT * FROM servicios_bbva");
    $registros = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registros as $row) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM servicios_omnipos WHERE ticket = ?");
        $check->execute([$row['ticket']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $pdo->prepare("
                INSERT INTO servicios_omnipos (
                    ticket, banco, fecha_inicio, vim, afiliacion, servicio, comercio, domicilio, colonia, ciudad, cp,
                    idc, solicito, telefono_contacto_1, tipo_tpv, referencia, horario, comentarios, fecha_limite, cantidad_insumos,
                    estatus, fecha_atencion, fecha_cita, resultado, id_usuario, observaciones, tecnico_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $row['ticket'], 'BBVA', convertirFecha($row['fecha_inicio']), $row['vim'], $row['afiliacion'],
                $row['servicio'], $row['comercio'], $row['domicilio'], $row['colonia'], $row['ciudad'], $row['cp'],
                $row['idc'], $row['solicito'], $row['telefono_contacto_1'], $row['tipo_tpv'], $row['referencia'],
                $row['horario'], $row['comentarios'], convertirFecha($row['fecha_limite']), $row['cantidad_insumos'],
                'Por Asignar', null, null, null, null, null, null
            ]);
            $insertados++;
        }
    }
} catch (Exception $e) {
    $errores++;
    die("❌ Error al migrar: " . $e->getMessage());
}

// Llamamos a la vista
$contenido = __DIR__ . '/contenido_migracion.php';
require_once __DIR__ . '/layout.php';
