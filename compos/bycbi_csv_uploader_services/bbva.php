<?php

// Función para procesar los datos del archivo Excel de BBVA
function procesar_datos_bbva($data) {
    global $wpdb;

    $total_registros = 0;
    $registros_exitosos = 0;
    $registros_fallidos = 0;
    $errores = [];

    // Nombre de la tabla corregido a 'servicios_bbva' sin el prefijo wp_
    $tabla_bbva = 'servicios_bbva';

    // Obtener la fecha de carga (fecha y hora actuales)
    $fecha_carga = current_time('mysql'); // Esto devuelve la fecha y hora actual en formato MySQL
  

    // Iterar sobre los datos del archivo Excel, comenzando desde la fila 1 (la fila 0 son los títulos)
    foreach ($data as $index => $row) {
        if ($index == 0) continue; // Saltar la fila de títulos

        $total_registros++;

        // Mapeo de columnas del Excel a la base de datos
        $ticket = trim($row[0]);  // Columna del ticket
        $fecha_inicio = convertir_fecha($row[1]);  // Convierte la fecha a formato MySQL

        // Si la fecha de inicio está vacía, usa la fecha de carga
        if (empty($fecha_inicio)) {
            $fecha_inicio = $fecha_carga;  // Asignar la fecha de carga si está vacía
        }

        // Verificar si el ticket está vacío
        if (empty($ticket)) {
            $errores[] = "Error en fila " . ($index + 1) . ": Ticket vacío.";
            $registros_fallidos++;
            continue;
        }

        // Otras columnas
        $vim = $row[2];
        $afiliacion = $row[3];
        $servicio = $row[4];
        $proveedor = $row[5];
        $temporal = $row[6];
        $comercio = $row[7];
        $domicilio = $row[8];
        $colonia = $row[9];
        $ciudad = $row[10];
        $cp = $row[11];
        $dar = $row[12];
        $plaza = $row[13];
        $idc = $row[14];
        $grupo = $row[15];
        $cadena = $row[16];
        $solicito = $row[17];
        $telefono_contacto_1 = $row[18];
        $tipo_tpv = $row[19];
        $gestor = $row[20];
        $email = $row[21];
        $lada_tel = $row[22];
        $referencia = $row[23];
        $horario = $row[24];
        $representante_domicilio_temporal = $row[25];
        $telefono_temporal_1 = $row[26];
        $comentarios = $row[27];
        $fecha_limite = convertir_fecha($row[28]);
        $fecha_cierre = convertir_fecha($row[29]);
        $fecha_atencion = convertir_fecha($row[30]);
        $hora_atencion = $row[31];
        $fecha_cierre_telecarga = convertir_fecha($row[32]);
        $solucion = $row[33];
        $conclusion = $row[34];
        $nivel_servicio = $row[35];
        $tecnico = $row[36];
        $recibio = $row[37];
        $causa_fuera_tiempo = $row[38];
        $sucursal = $row[39];
        $division = $row[40];
        $banca = $row[41];
        $origen = $row[42];
        $sistema = $row[43];
        $ultima_modificacion = convertir_fecha($row[44]);
        $tipo_ticket = $row[45];
        $ultimo_usuario = $row[46];
        $dar_2 = $row[47];
        $giro = $row[48];
        $estado = $row[49];
        $tipo_servicio = $row[50];
        $cantidad_insumos = $row[51];
        $folio_telecarga = $row[52];
        $almacen = $row[53];
        $serie_asignada = $row[54];
        $serie_instalada = $row[55];
        $serie_retiro = $row[56];
        $check_cierre_telecarga = $row[57];
        $celular_contacto = $row[58];
        $telefono_contacto_2 = $row[59];
        $multimerchant = $row[60];
        $vim_2 = $row[61];
        $tipo_domicilio = $row[62];

        // Insertar los datos en la tabla de la base de datos
        $resultado = $wpdb->insert(
            $tabla_bbva,
            array(
                'ticket' => $ticket,
                'fecha_inicio' => $fecha_inicio,
                'vim' => $vim,
                'afiliacion' => $afiliacion,
                'servicio' => $servicio,
                'proveedor' => $proveedor,
                'temporal' => $temporal,
                'comercio' => $comercio,
                'domicilio' => $domicilio,
                'colonia' => $colonia,
                'ciudad' => $ciudad,
                'cp' => $cp,
                'dar' => $dar,
                'plaza' => $plaza,
                'idc' => $idc,
                'grupo' => $grupo,
                'cadena' => $cadena,
                'solicito' => $solicito,
                'telefono_contacto_1' => $telefono_contacto_1,
                'tipo_tpv' => $tipo_tpv,
                'gestor' => $gestor,
                'email' => $email,
                'lada_tel' => $lada_tel,
                'referencia' => $referencia,
                'horario' => $horario,
                'representante_domicilio_temporal' => $representante_domicilio_temporal,
                'telefono_temporal_1' => $telefono_temporal_1,
                'comentarios' => $comentarios,
                'fecha_limite' => $fecha_limite,
                'fecha_cierre' => $fecha_cierre,
                'fecha_atencion' => $fecha_atencion,
                'hora_atencion' => $hora_atencion,
                'fecha_cierre_telecarga' => $fecha_cierre_telecarga,
                'solucion' => $solucion,
                'conclusion' => $conclusion,
                'nivel_servicio' => $nivel_servicio,
                'tecnico' => $tecnico,
                'recibio' => $recibio,
                'causa_fuera_tiempo' => $causa_fuera_tiempo,
                'sucursal' => $sucursal,
                'division' => $division,
                'banca' => $banca,
                'origen' => $origen,
                'sistema' => $sistema,
                'ultima_modificacion' => $ultima_modificacion,
                'tipo_ticket' => $tipo_ticket,
                'ultimo_usuario' => $ultimo_usuario,
                'dar_2' => $dar_2,
                'giro' => $giro,
                'estado' => $estado,
                'tipo_servicio' => $tipo_servicio,
                'cantidad_insumos' => $cantidad_insumos,
                'folio_telecarga' => $folio_telecarga,
                'almacen' => $almacen,
                'serie_asignada' => $serie_asignada,
                'serie_instalada' => $serie_instalada,
                'serie_retiro' => $serie_retiro,
                'check_cierre_telecarga' => $check_cierre_telecarga,
                'celular_contacto' => $celular_contacto,
                'telefono_contacto_2' => $telefono_contacto_2,
                'multimerchant' => $multimerchant,
                'vim_2' => $vim_2,
                'tipo_domicilio' => $tipo_domicilio,
            )
        );

        if ($resultado === false) {
            $errores[] = "Error en fila " . ($index + 1) . ": " . $wpdb->last_error;
            $registros_fallidos++;
        } else {
            $registros_exitosos++;
        }
    }

    // Mostrar resultados del proceso
    echo '<div class="notice notice-success">';
    echo "<p>Proceso completado para BBVA. Registros cargados correctamente: $registros_exitosos de $total_registros.</p>";
    if ($registros_fallidos > 0) {
        echo "<p>Registros fallidos: $registros_fallidos de $total_registros.</p>";
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
    }
    echo '</div>';
}

// Función para convertir fechas a formato MySQL
function convertir_fecha($fecha) {
    if (empty($fecha)) {
        return null; // Si la fecha está vacía, retornar null
    }

    // Intentar convertir la fecha desde formato DD/MM/YYYY a YYYY-MM-DD
    $date = DateTime::createFromFormat('d/m/Y', $fecha);
    if ($date) {
        return $date->format('Y-m-d'); // Retornar formato compatible con MySQL
    }

    // Si no es DD/MM/YYYY, intentar como YYYY-MM-DD
    $date = DateTime::createFromFormat('Y-m-d', $fecha);
    if ($date) {
        return $date->format('Y-m-d');
    }

    // Si la conversión falla, retornar null
    return null;
}
