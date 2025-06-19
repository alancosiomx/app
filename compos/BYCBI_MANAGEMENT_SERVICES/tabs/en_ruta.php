<?php
// Tab: En Ruta
function cbi_tab_en_ruta() {
    global $wpdb;

    // Consulta para obtener los servicios con estatus "En Ruta"
    $servicios = $wpdb->get_results(
        "SELECT id, banco, fecha_inicio, fecha_limite, ticket, afiliacion, comercio, domicilio, colonia, ciudad, cp, servicio, cantidad_insumos, telefono_contacto_1, referencia, horario, idc 
         FROM servicios_omnipos 
         WHERE estatus = 'En Ruta'",
        ARRAY_A
    );

    // Definir columnas de la tabla
    $columns = [
        'banco' => 'Banco',
        'fecha_inicio' => 'Fecha de Inicio',
        'fecha_limite' => 'Fecha Límite',
        'ticket' => 'Ticket',
        'afiliacion' => 'Afiliación',
        'comercio' => 'Comercio',
        'domicilio' => 'Domicilio',
        'colonia' => 'Colonia',
        'ciudad' => 'Ciudad',
        'cp' => 'CP',
        'servicio' => 'Servicio',
        'cantidad_insumos' => 'Cantidad Insumos',
        'telefono_contacto_1' => 'Teléfono Contacto 1',
        'referencia' => 'Referencia',
        'horario' => 'Horario',
        'idc' => 'IDC'
    ];

    echo '<h3>Servicios En Ruta</h3>';

    // Callback para la columna de acción
    $accion_callback = function($servicio) use ($wpdb) {
        ob_start(); ?>
        <form method="post">
            <select name="conclusion" required>
                <option value="">Selecciona</option>
                <option value="Exito">Éxito</option>
                <option value="Rechazo">Rechazo</option>
                <option value="Cancelado">Cancelado</option>
            </select>
            <br>
            <label>
                <input type="radio" name="atendido" value="Hoy" required> Hoy
            </label>
            <label>
                <input type="radio" name="atendido" value="Ayer" required> Ayer
            </label>
            <br>
            <button type="submit" name="concluir_servicio" value="<?php echo esc_attr($servicio['id']); ?>">
                Concluir
            </button>
        </form>
        <form method="post" style="display:inline-block;">
            <button type="submit" name="reasignar_servicio" value="<?php echo esc_attr($servicio['id']); ?>">Reasignar</button>
        </form>
        <?php
        return ob_get_clean();
    };

    // Generar tabla usando la constante con callback de acción
    cbi_generar_tabla_servicios($servicios, $columns, $accion_callback);

    // Procesar la conclusión o reasignación del servicio
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['concluir_servicio'])) {
            // Procesar conclusión
            $servicio_id = intval($_POST['concluir_servicio']);
            $conclusion = sanitize_text_field($_POST['conclusion']);
            $atendido = sanitize_text_field($_POST['atendido']);
            $fecha_hora = ($atendido === 'Hoy') ? get_cdmx_time() : date('Y-m-d H:i:s', strtotime('-1 day', strtotime(get_cdmx_time())));

            $estatus = ($conclusion === 'Exito') ? 'Concluido' : ($conclusion === 'Rechazo' ? 'Rechazado' : 'Cancelado');

            $resultado = $wpdb->update(
                'servicios_omnipos',
                array(
                    'estatus' => $estatus,
                    'resultado' => $conclusion,
                    'fecha_atencion' => $fecha_hora
                ),
                array('id' => $servicio_id)
            );

            if ($resultado !== false) {
                wp_redirect(add_query_arg('actualizado', 'true', $_SERVER['REQUEST_URI']));
                exit;
            } else {
                echo "<p>Error al procesar el servicio con ID {$servicio_id}. Error: " . $wpdb->last_error . "</p>";
            }
        } elseif (isset($_POST['reasignar_servicio'])) {
            // Procesar reasignación
            $servicio_id = intval($_POST['reasignar_servicio']);

            $resultado = $wpdb->update(
                'servicios_omnipos',
                array(
                    'estatus' => 'Por Asignar',
                    'tecnico_id' => null,
                    'resultado' => null,
                    'fecha_atencion' => null
                ),
                array('id' => $servicio_id)
            );

            if ($resultado !== false) {
                wp_redirect(add_query_arg('actualizado', 'true', $_SERVER['REQUEST_URI']));
                exit;
            } else {
                echo "<p>Error al reasignar el servicio con ID {$servicio_id}. Error: " . $wpdb->last_error . "</p>";
            }
        }
    }

    // Mostrar mensaje si se actualizó correctamente
    if (isset($_GET['actualizado']) && $_GET['actualizado'] === 'true') {
        echo "<p>Tabla actualizada correctamente.</p>";
    }
}

// Función para obtener hora en CDMX
function get_cdmx_time() {
    $timezone = new DateTimeZone('America/Mexico_City');
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y-m-d H:i:s');
}
?>
