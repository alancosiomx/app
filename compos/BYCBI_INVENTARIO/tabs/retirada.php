<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Declarar $wpdb globalmente para evitar errores de variable no definida
global $wpdb;

// Establecer la zona horaria de Ciudad de México
date_default_timezone_set('America/Mexico_City');

// Función para obtener la lista de técnicos desde la tabla wp_bm_user_index
function obtenerTecnicos() {
    global $wpdb;
    // Obtener la lista de técnicos
    $tecnicos = $wpdb->get_results("SELECT ID, display_name FROM wp_bm_user_index", ARRAY_A);
    return $tecnicos;
}

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

// Función para actualizar la terminal como retirada
function actualizarTerminalRetirada($terminal_id, $ticket_retirada, $tecnico_id) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Obtener el nombre del técnico seleccionado
    $tecnico_nombre = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM wp_bm_user_index WHERE ID = %d", $tecnico_id));

    // Actualizar la terminal: cambiar status a DAÑADA, custodia a TECNICO y guardar el técnico y el ticket de retirada
    $result = $wpdb->update(
        $table_name,
        [
            'status' => 'DAÑADA',
            'custodia' => 'TECNICO',
            'technician' => $tecnico_nombre,
            'ticket_retired' => $ticket_retirada
        ],
        ['id' => $terminal_id]
    );

    if ($result === false) {
        echo '<p>Error al actualizar la terminal: ' . esc_html($wpdb->last_error) . '</p>';
    } else {
        echo '<p>Terminal marcada como DAÑADA con custodia TECNICO y asignada al técnico: ' . esc_html($tecnico_nombre) . '.</p>';
    }
}

// Obtener la lista de técnicos para el select
$tecnicos = obtenerTecnicos();

// Manejar el envío del formulario de retirada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['serie_busqueda']) && !empty($_POST['tecnico_id'])) {
    // Buscar la terminal por serie completa o corta
    $terminal_encontrada = buscarTerminalPorSerie($_POST['serie_busqueda']);

    if ($terminal_encontrada) {
        // Actualizar la terminal como retirada con el ticket y el técnico asignado
        actualizarTerminalRetirada($terminal_encontrada['id'], $_POST['ticket_retirada'] ?? null, $_POST['tecnico_id']);
    } else {
        echo '<p>No se encontró ninguna terminal con la serie ingresada.</p>';
    }
}
?>

<h2>Registrar Retirada de Terminal</h2>

<!-- Formulario de Retirada -->
<form method="POST">
    <label for="serie_busqueda">Serie Completa o Serie Corta:</label>
    <input type="text" name="serie_busqueda" id="serie_busqueda" placeholder="Ingrese la serie" required />

    <label for="ticket_retirada">Ticket de Retirada (Opcional):</label>
    <input type="text" name="ticket_retirada" id="ticket_retirada" placeholder="Ingrese el ticket de retirada" />

    <label for="tecnico_id">Asignar a Técnico:</label>
    <select name="tecnico_id" id="tecnico_id" required>
        <option value="">Seleccione un Técnico</option>
        <?php foreach ($tecnicos as $tecnico): ?>
            <option value="<?= esc_attr($tecnico['ID']) ?>">
                <?= esc_html($tecnico['display_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Registrar Retirada</button>
</form>
