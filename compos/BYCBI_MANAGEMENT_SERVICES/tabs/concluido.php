<?php
// Función para mostrar el tab "Concluido"
function cbi_tab_concluido() {
    global $wpdb;

    // Consulta para obtener los servicios con estatus "Concluido", "Rechazado", o "Cancelado"
    $servicios = $wpdb->get_results("
        SELECT id, banco, fecha_inicio, fecha_limite, ticket, afiliacion, comercio, domicilio, colonia, ciudad, cp, servicio, cantidad_insumos, telefono_contacto_1, referencia, horario, estatus, resultado 
        FROM servicios_omnipos 
        WHERE estatus IN ('Concluido', 'Rechazado', 'Cancelado')
    ", ARRAY_A);

    // Define las columnas para la tabla
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
        'estatus' => 'Estatus',
        'resultado' => 'Resultado',
    ];

    // Callback de acción para generar acciones adicionales (puede estar vacío si no es necesario)
    $accion_callback = function($servicio) {
        // Ejemplo: Agregar un botón para descargar detalles
        return '<a href="#" class="btn-detalles">Detalles</a>';
    };

    ?>
    <h3>Servicios Concluidos</h3>
    <?php
    // Generar la tabla usando la constante y el callback
    cbi_generar_tabla_servicios($servicios, $columns, $accion_callback);
}
?>
