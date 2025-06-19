<?php
// Tab: Por Asignar
function cbi_tab_por_asignar() {
    global $wpdb;

    // Obtener la fecha actual y la de mañana en CDMX
    $timezone = new DateTimeZone('America/Mexico_City');
    $current_date = new DateTime('now', $timezone);
    $current_date_str = $current_date->format('Y-m-d');
    $tomorrow_date = $current_date->modify('+1 day')->format('Y-m-d');

    // Consultar citas para hoy y mañana
    $citas_hoy = $wpdb->get_results($wpdb->prepare(
        "SELECT ticket, comercio FROM servicios_omnipos WHERE fecha_cita = %s",
        $current_date_str
    ));

    $citas_manana = $wpdb->get_results($wpdb->prepare(
        "SELECT ticket, comercio FROM servicios_omnipos WHERE fecha_cita = %s",
        $tomorrow_date
    ));

    // Mostrar alertas si hay citas para hoy o mañana
    if (!empty($citas_hoy)) {
        echo '<p style="color: red; font-weight: bold;">Alerta: Hay ' . count($citas_hoy) . ' citas programadas para hoy (' . $current_date_str . '):</p>';
        echo '<ul style="color: red;">';
        foreach ($citas_hoy as $cita) {
            echo '<li>Ticket: ' . esc_html($cita->ticket) . ', Comercio: ' . esc_html($cita->comercio) . '</li>';
        }
        echo '</ul>';
    }

    if (!empty($citas_manana)) {
        echo '<p style="color: orange; font-weight: bold;">Aviso: Hay ' . count($citas_manana) . ' citas programadas para mañana (' . $tomorrow_date . '):</p>';
        echo '<ul style="color: orange;">';
        foreach ($citas_manana as $cita) {
            echo '<li>Ticket: ' . esc_html($cita->ticket) . ', Comercio: ' . esc_html($cita->comercio) . '</li>';
        }
        echo '</ul>';
    }

    // Consulta para obtener los servicios con estatus "Por Asignar"
    $servicios = $wpdb->get_results(
        "SELECT id, banco, fecha_inicio, fecha_limite, ticket, afiliacion, comercio, domicilio, colonia, ciudad, cp, servicio, cantidad_insumos, telefono_contacto_1, referencia, horario, fecha_cita 
         FROM servicios_omnipos 
         WHERE estatus = 'Por Asignar'", 
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
        'fecha_cita' => 'Fecha Cita'
    ];

    echo '<h3>Servicios Por Asignar</h3>';

    // Callback para la columna de acción
    $accion_callback = function($servicio) use ($wpdb) {
        ob_start(); ?>
        <form method="post">
            <select name="tecnico" required>
                <option value="">Selecciona un técnico</option>
                <?php
                $tecnicos = $wpdb->get_results("SELECT ID, display_name FROM wp_users", ARRAY_A);
                foreach ($tecnicos as $tecnico) {
                    echo "<option value='{$tecnico['ID']}'>{$tecnico['display_name']}</option>";
                }
                ?>
            </select>
            <input type="hidden" name="servicio_id" value="<?php echo esc_attr($servicio['id']); ?>">
            <input type="submit" name="asignar_tecnico" value="Asignar">
        </form>
        <?php
        return ob_get_clean();
    };

    // Generar tabla usando la constante con callback de acción
    cbi_generar_tabla_servicios($servicios, $columns, $accion_callback);

    // Procesar la asignación individual
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_tecnico'])) {
        $tecnico_id = intval($_POST['tecnico']);
        $servicio_id = intval($_POST['servicio_id']);

        // Validar técnico y servicio
        if ($tecnico_id > 0 && $servicio_id > 0) {
            // Obtener el display_name asociado al técnico
            $display_name = $wpdb->get_var(
                $wpdb->prepare("SELECT display_name FROM wp_users WHERE ID = %d", $tecnico_id)
            );

            // Verificar que el display_name no sea vacío
            if (!empty($display_name)) {
                // Actualizar servicio
                $resultado = $wpdb->update(
                    'servicios_omnipos',
                    array(
                        'tecnico_id' => $tecnico_id, 
                        'estatus' => 'En Ruta',
                        'idc' => $display_name // Asignar el display_name como IDC
                    ),
                    array('id' => $servicio_id)
                );

                // Verificar el resultado de la actualización
                if ($resultado === false) {
                    echo "<p>Error al actualizar el servicio: " . $wpdb->last_error . "</p>";
                } elseif ($resultado === 0) {
                    echo "<p>La consulta no afectó filas. Verifica los valores proporcionados.</p>";
                } else {
                    // Redirigir para evitar el reenvío del formulario
                    wp_redirect(add_query_arg('asignado', 'true', $_SERVER['REQUEST_URI']));
                    exit;
                }
            } else {
                echo "<p>No se encontró un display_name asociado al técnico seleccionado. Por favor, inténtalo de nuevo.</p>";
            }
        } else {
            echo "<p>Datos inválidos proporcionados. Por favor, inténtalo de nuevo.</p>";
        }
    }

    // Mostrar mensaje si se asignó correctamente
    if (isset($_GET['asignado']) && $_GET['asignado'] === 'true') {
        echo "<p>Servicio asignado correctamente.</p>";
    }
}
?>
