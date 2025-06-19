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

// Función para actualizar la terminal como instalada usando una consulta SQL directa
function actualizarTerminalInstalada($terminal_id, $ticket_instalacion) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // El valor correcto es 'INSTALADA'
    $status_valido = 'INSTALADA';
    $custodia_valida = 'COMERCIO';
    
    // Obtener la fecha y hora actual en la zona horaria de Ciudad de México
    $fecha_instalacion = date('Y-m-d H:i:s');

    // Crear consulta SQL para actualizar la terminal
    $sql = $wpdb->prepare(
        "UPDATE $table_name 
        SET status = %s, custodia = %s, ticket_installed = %s, installation_date = %s 
        WHERE id = %d",
        $status_valido, $custodia_valida, $ticket_instalacion, $fecha_instalacion, $terminal_id
    );

    // Ejecutar la consulta SQL
    $result = $wpdb->query($sql);

    if ($result === false) {
        echo '<p>Error al actualizar la terminal: ' . esc_html($wpdb->last_error) . '</p>';
    } else {
        if ($wpdb->rows_affected > 0) {
            echo '<p>Terminal actualizada correctamente a ' . esc_html($status_valido) . ' con custodia ' . esc_html($custodia_valida) . ' en la fecha ' . esc_html($fecha_instalacion) . '.</p>';
            
            // Mostrar los valores actualizados
            $terminal_actualizada = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $terminal_id),
                ARRAY_A
            );

            echo '<p>Verificación de cambios:</p>';
            echo '<pre>' . print_r($terminal_actualizada, true) . '</pre>';
        } else {
            echo '<p>No se actualizó ninguna fila. Verifique que el ID de la terminal sea correcto.</p>';
        }
    }
}

// Manejar el envío del formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['serie_busqueda']) && !empty($_POST['ticket_instalacion'])) {
    // Buscar la terminal por serie completa o corta
    $terminal_encontrada = buscarTerminalPorSerie($_POST['serie_busqueda']);

    if ($terminal_encontrada) {
        // Verificar que se encontró correctamente la terminal
        echo '<p>Terminal encontrada: ' . esc_html($terminal_encontrada['serial_number']) . ' (ID: ' . esc_html($terminal_encontrada['id']) . ')</p>';

        // Actualizar la terminal a instalada con el ticket proporcionado
        actualizarTerminalInstalada($terminal_encontrada['id'], $_POST['ticket_instalacion']);
    } else {
        echo '<p>No se encontró ninguna terminal con la serie ingresada.</p>';
    }
}
?>

<h2>Registrar Instalación de Terminal</h2>

<!-- Formulario de Instalación -->
<form method="POST">
    <label for="serie_busqueda">Serie Completa o Serie Corta:</label>
    <input type="text" name="serie_busqueda" id="serie_busqueda" placeholder="Ingrese la serie" required />

    <label for="ticket_instalacion">Ticket de Instalación:</label>
    <input type="text" name="ticket_instalacion" id="ticket_instalacion" placeholder="Ingrese el ticket de instalación" required />

    <button type="submit">Registrar Instalación</button>
</form>
