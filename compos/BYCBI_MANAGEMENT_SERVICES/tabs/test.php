<?php
// Función para mostrar el tab "Test"
function cbi_tab_test() {
    global $wpdb;

    // Incluir la constante de tabla
    require_once plugin_dir_path(__FILE__) . '../constants/table.php';

    // Consulta para obtener datos de ejemplo
    $servicios = $wpdb->get_results(
        "SELECT ticket, comercio FROM servicios_omnipos LIMIT 10", 
        ARRAY_A
    );

    // Definir las columnas de la tabla
    $columnas = [
        'ticket' => 'Ticket',
        'comercio' => 'Comercio',
    ];

    // Mostrar encabezado
    echo '<h3>Test de Tabla con Constante</h3>';

    // Usar la constante para generar la tabla
    cbi_generar_tabla_servicios($servicios, $columnas);
}
?>
