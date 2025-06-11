<?php
function mapear_bbva($pdo)
{
    $insertados = 0;
    $duplicados = 0;

    $tabla_origen = 'servicios_bbva';
    $tabla_destino = 'servicios_omnipos';

    $columnas_destino = obtener_columnas($pdo, $tabla_destino);

    $stmt = $pdo->query("SELECT * FROM $tabla_origen");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registros as $fila) {
        $ticket = $fila['ticket'] ?? null;
        if (!$ticket) continue;

        // Validar duplicado en OMNIPOS
        $check = $pdo->prepare("SELECT COUNT(*) FROM $tabla_destino WHERE ticket = ?");
        $check->execute([$ticket]);
        if ($check->fetchColumn() > 0) {
            $duplicados++;
            continue;
        }

        // Preparar el array de inserción mapeando los campos manualmente
        $datos = [];

        $mapeo = [
            'ticket' => 'ticket',
            'fecha_inicio' => 'fecha_inicio',
            'vim' => 'vim',
            'afiliacion' => 'afiliacion',
            'servicio' => 'servicio',
            'comercio' => 'comercio',
            'domicilio' => 'domicilio',
            'colonia' => 'colonia',
            'ciudad' => 'ciudad',
            'cp' => 'cp',
            'idc' => 'idc',
            'solicito' => 'solicito',
            'telefono_contacto_1' => 'telefono_contacto_1',
            'tipo_tpv' => 'tipo_tpv',
            'referencia' => 'referencia',
            'horario' => 'horario',
            'comentarios' => 'comentarios',
            'fecha_limite' => 'fecha_limite',
            'cantidad_insumos' => 'cantidad_insumos',
            'fecha_atencion' => 'fecha_atencion'
        ];

        foreach ($mapeo as $origen => $destino) {
            if (in_array($destino, $columnas_destino)) {
                $datos[$destino] = $fila[$origen] ?? null;
            }
        }

        // Agregar campos adicionales
        $datos['banco'] = 'BBVA';
        $datos['estatus'] = 'Por Asignar';
        $datos['fecha_carga'] = date('Y-m-d H:i:s');

        // Insertar
        $campos = array_keys($datos);
        $sql = "INSERT INTO $tabla_destino (" . implode(',', $campos) . ") VALUES (:" . implode(', :', $campos) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);

        $insertados++;
    }

    return "✅ Servicios migrados: $insertados\n⚠️ Tickets duplicados: $duplicados";
}

function mapear_banregio($pdo)
{
    $insertados = 0;
    $duplicados = 0;

    $tabla_origen = 'servicios_banregio';
    $tabla_destino = 'servicios_omnipos';

    $columnas_destino = obtener_columnas($pdo, $tabla_destino);

    $stmt = $pdo->query("SELECT * FROM $tabla_origen");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registros as $fila) {
        $ticket = $fila['folio_cliente'] ?? null;
        if (!$ticket) continue;

        // Validar duplicado en OMNIPOS
        $check = $pdo->prepare("SELECT COUNT(*) FROM $tabla_destino WHERE ticket = ?");
        $check->execute([$ticket]);
        if ($check->fetchColumn() > 0) {
            $duplicados++;
            continue;
        }

        $datos = [];

        // Mapeo específico Banregio → OMNIPOS
        $mapeo = [
            'folio_cliente' => 'ticket',
            'afiliacion' => 'afiliacion',
            'grupo' => 'servicio',
            'comercio' => 'comercio',
            'direccion' => 'domicilio',
            'colonia' => 'colonia',
            'cp' => 'cp',
            'fecha_limite' => 'fecha_limite',
            'telefono' => 'telefono_contacto_1',
            'tecnico' => 'idc'
        ];

        foreach ($mapeo as $origen => $destino) {
            if (in_array($destino, $columnas_destino)) {
                $datos[$destino] = $fila[$origen] ?? null;
            }
        }

        // Campos adicionales
        $datos['banco'] = 'BANREGIO';
        $datos['estatus'] = 'Por Asignar';
        $datos['fecha_carga'] = date('Y-m-d H:i:s');

        // Insertar en servicios_omnipos
        $campos = array_keys($datos);
        $sql = "INSERT INTO $tabla_destino (" . implode(',', $campos) . ") VALUES (:" . implode(', :', $campos) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);

        $insertados++;
    }

    return "✅ Servicios BANREGIO migrados: $insertados\n⚠️ Tickets duplicados: $duplicados";
}


function mapear_azteca($pdo) {
    return "🛠️ Aún no implementado.";
}

function obtener_columnas($pdo, $tabla)
{
    $stmt = $pdo->query("DESCRIBE $tabla");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
