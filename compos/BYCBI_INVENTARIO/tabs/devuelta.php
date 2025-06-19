<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Declarar $wpdb globalmente para evitar errores de variable no definida
global $wpdb;

// Establecer la zona horaria de Ciudad de México
date_default_timezone_set('America/Mexico_City');

// Función para buscar una serie en la base de datos
function buscarTerminalPorSerie($serie) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Buscar la terminal por serie completa o serie corta
    $terminal = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE serial_number = %s OR short_serial = %s",
            $serie, $serie
        ),
        ARRAY_A
    );

    return $terminal;
}

// Función para actualizar la terminal como devuelta
function actualizarTerminalDevuelta($terminal_id) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Obtener la fecha y hora actual en la zona horaria de Ciudad de México
    $fecha_devuelta = date('Y-m-d H:i:s');

    // Actualizar la terminal: cambiar status a DEVUELTA, custodia a BANCO y registrar la fecha de devolución
    $result = $wpdb->query(
        $wpdb->prepare(
            "UPDATE $table_name SET status = %s, custodia = %s, return_date = %s WHERE id = %d",
            'DEVUELTA', 'BANCO', $fecha_devuelta, $terminal_id
        )
    );

    if ($result === false) {
        echo '<p>Error al actualizar la terminal: ' . esc_html($wpdb->last_error) . '</p>';
    } else {
        echo '<p>Terminal actualizada correctamente a DEVUELTA con custodia BANCO en la fecha ' . esc_html($fecha_devuelta) . '.</p>';
    }
}

// Manejar el envío del formulario de devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['serie_busqueda'])) {
    // Buscar la terminal por serie completa o corta
    $terminal_encontrada = buscarTerminalPorSerie($_POST['serie_busqueda']);

    if ($terminal_encontrada) {
        // Actualizar la terminal a devuelta
        actualizarTerminalDevuelta($terminal_encontrada['id']);
    } else {
        echo '<p>No se encontró ninguna terminal con la serie ingresada.</p>';
    }
}
?>

<h2>Registrar Devolución de Terminal</h2>

<!-- Formulario de Devolución -->
<form method="POST">
    <label for="serie_busqueda">Serie Completa o Serie Corta:</label>
    <input type="text" name="serie_busqueda" id="serie_busqueda" placeholder="Ingrese la serie" required />

    <button type="submit">Registrar Devolución</button>
</form>
