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
        $ticket = trim($fila['ticket'] ?? '');
        if (!$ticket) continue;

        // Validar duplicado en OMNIPOS
        $check = $pdo->prepare("SELECT COUNT(*) FROM $tabla_destino WHERE ticket = ?");
        $check->execute([$ticket]);
        if ($check->fetchColumn() > 0) {
            $duplicados++;
            continue;
        }

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
                $valor = trim($fila[$origen] ?? '');

                // Normalizar fechas
                if (in_array($destino, ['fecha_inicio', 'fecha_limite', 'fecha_atencion'])) {
                    $valor = normalizar_fecha($valor);
                }

                $datos[$destino] = $valor !== '' ? $valor : null;
            }
        }

        $datos['banco'] = 'BBVA';
        $datos['estatus'] = 'Por Asignar';
        $datos['fecha_carga'] = date('Y-m-d H:i:s');

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

        // Mapeo específico Banregio → Omnipos
        $mapeo = [
            'folio_cliente' => 'ticket',
            'afiliacion' => 'afiliacion',
            'grupo' => 'servicio',
            'comercio' => 'comercio',
            'direccion' => 'domicilio',
            'colonia' => 'colonia',
            'poblacion' => 'ciudad',
            'cp' => 'cp',
            'plaza' => 'plaza',
            'estado' => 'estado',
            'modelo' => 'modelo',
            'tecnologia' => 'tipo_tpv',
            'telefono' => 'telefono_contacto_1',
            'horario_comercio' => 'horario',
            'rollos' => 'cantidad_insumos',
            'fecha_limite' => 'fecha_limite',
            'folio_telecarga_serie' => 'folio_telecarga',
            'caja' => 'referencia',
            'tecnico' => 'idc'
        ];

        foreach ($mapeo as $origen => $destino) {
            if (in_array($destino, $columnas_destino)) {
                $datos[$destino] = $fila[$origen] ?? null;
            }
        }

        // Agregados fijos
        $datos['banco'] = 'BANREGIO';
        $datos['estatus'] = 'Por Asignar';
        $datos['fecha_carga'] = date('Y-m-d H:i:s');

        // Insertar
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
