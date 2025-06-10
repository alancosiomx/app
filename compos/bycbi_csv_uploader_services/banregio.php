<?php

// Función para procesar los datos del archivo Excel de Banregio
function procesar_datos_banregio($data) {
    global $wpdb;

    $total_registros = 0;
    $registros_exitosos = 0;
    $registros_fallidos = 0;
    $errores = [];

    // Nombre de la tabla donde se van a insertar los registros
    $tabla_banregio = 'servicios_banregio'; // Uso directo del nombre de la tabla

    // Iterar sobre los datos del archivo Excel, comenzando desde la fila 1 (la fila 0 son los títulos)
    foreach ($data as $index => $row) {
        if ($index == 0) continue; // Saltar la fila de títulos

        $total_registros++;

        // Mapeo de columnas del Excel a la base de datos
        $banco = 'Banregio';  // Valor fijo 'Banregio'
        $folio_cliente = !empty($row[1]) ? trim($row[1]) : null;  // Verificar si hay un valor antes de aplicar trim
        $afiliacion = $row[2];
        $comercio = $row[3];
        $direccion = $row[4];
        $colonia = $row[5];
        $cp = !empty($row[6]) && strlen($row[6]) <= 10 ? $row[6] : null;  // Validar longitud del campo cp
        $poblacion = $row[7];
        $plaza = $row[8];
        $estado = $row[9];
        $modelo = $row[10];
        $tecnologia = $row[11];
        $telefono = $row[12];
        $horario_comercio = $row[13];
        $grupo = $row[14];
        $rollos = $row[15];
        $fecha_limite = convertir_fecha($row[16]);
        $folio_telecarga = $row[17];
        $caja = $row[18];
        $tecnico = $row[19];

        // Verificar si el folio_cliente está vacío
        if (empty($folio_cliente)) {
            $errores[] = "Error en fila " . ($index + 1) . ": Folio Cliente vacío.";
            $registros_fallidos++;
            continue;
        }

        // Insertar los datos en la tabla de la base de datos
        $resultado = $wpdb->insert(
            $tabla_banregio,
            array(
                'banco' => $banco,  // Agregar el valor 'Banregio' al campo banco
                'folio_cliente' => $folio_cliente,
                'afiliacion' => $afiliacion,
                'comercio' => $comercio,
                'direccion' => $direccion,
                'colonia' => $colonia,
                'cp' => $cp,
                'poblacion' => $poblacion,
                'plaza' => $plaza,
                'estado' => $estado,
                'modelo' => $modelo,
                'tecnologia' => $tecnologia,
                'telefono' => $telefono,
                'horario_comercio' => $horario_comercio,
                'grupo' => $grupo,
                'rollos' => $rollos,
                'fecha_limite' => $fecha_limite,
                'folio_telecarga' => $folio_telecarga,
                'caja' => $caja,
                'tecnico' => $tecnico
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
    echo "<p>Proceso completado para Banregio. Registros cargados correctamente: $registros_exitosos de $total_registros.</p>";
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
    } else {
        return null; // Si la conversión falla, retornar null
    }
}
