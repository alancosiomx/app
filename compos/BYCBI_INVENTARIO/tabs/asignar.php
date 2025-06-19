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

    if ($wpdb->last_error) {
        echo '<p>Error al obtener los técnicos: ' . esc_html($wpdb->last_error) . '</p>';
    }

    return $tecnicos;
}

// Función para buscar una terminal por serie completa o serie corta
function buscarTerminalPorSerie($serie) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Buscar la terminal por serie completa o serie corta
    $terminal = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE serial_number = %s OR short_serial = %s", $serie, $serie),
        ARRAY_A
    );

    return $terminal;
}

// Función para asignar una terminal a un técnico, actualizando custodia a "TECNICO", pero sin modificar el estado
function asignarTerminal($terminal_id, $tecnico_nombre) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Obtener la fecha y hora actual en la zona horaria de Ciudad de México
    $fecha_asignacion = date('Y-m-d H:i:s'); // Formato MySQL para la fecha y hora actual

    // Obtener el estado actual de la terminal antes de la actualización
    $estado_actual = $wpdb->get_var(
        $wpdb->prepare("SELECT status FROM $table_name WHERE id = %d", $terminal_id)
    );

    // Mantener el estado existente y solo actualizar la custodia, técnico y fecha de asignación
    $update_query = $wpdb->prepare(
        "UPDATE $table_name SET technician = %s, assignment_date = %s, custodia = %s WHERE id = %d",
        $tecnico_nombre, $fecha_asignacion, 'TECNICO', $terminal_id
    );

    // Ejecutar la consulta manualmente
    $result = $wpdb->query($update_query);

    return $result;
}

// Manejar el envío del formulario de búsqueda y asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['series']) && !empty($_POST['tecnico_id'])) {
        global $wpdb;

        // Obtener las series, separadas por salto de línea
        $series = explode(PHP_EOL, trim($_POST['series']));
        $series = array_map('trim', $series); // Eliminar espacios en blanco adicionales

        // Obtener el técnico seleccionado
        $tecnico_id = $_POST['tecnico_id'];
        $tecnico_nombre = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM wp_bm_user_index WHERE ID = %d", $tecnico_id));

        if (!$tecnico_nombre) {
            echo '<p>Error: No se pudo obtener el nombre del técnico seleccionado.</p>';
        } else {
            // Procesar cada serie y asignar las terminales
            foreach ($series as $serie) {
                if (!empty($serie)) {
                    // Buscar la terminal por serie completa o serie corta
                    $terminal = buscarTerminalPorSerie($serie);

                    if ($terminal) {
                        // Asignar la terminal al técnico seleccionado sin cambiar el estado
                        $resultado_asignacion = asignarTerminal($terminal['id'], $tecnico_nombre);

                        if ($resultado_asignacion !== false && $wpdb->rows_affected > 0) {
                            echo '<p>Terminal ' . esc_html($serie) . ' asignada correctamente al técnico ' . esc_html($tecnico_nombre) . '.</p>';
                        } else {
                            echo '<p>Error al asignar la terminal ' . esc_html($serie) . '.</p>';
                        }
                    } else {
                        echo '<p>No se encontró ninguna terminal con la serie ' . esc_html($serie) . '.</p>';
                    }
                }
            }
        }
    } else {
        echo '<p>Error: Debes ingresar series de terminales y seleccionar un técnico.</p>';
    }
}

// Obtener la lista de técnicos disponibles
$tecnicos = obtenerTecnicos();
?>

<h2>Asignar Terminales a Técnico</h2>

<!-- Formulario de Búsqueda -->
<form method="POST">
    <label for="series">Ingrese las series (una por línea):</label>
    <textarea name="series" id="series" rows="6" placeholder="Ingrese una serie por línea" required></textarea>

    <!-- Selección de Técnico -->
    <label for="tecnico_id">Seleccione un Técnico:</label>
    <select name="tecnico_id" id="tecnico_id" required>
        <option value="">Seleccione un Técnico</option>
        <?php foreach ($tecnicos as $tecnico): ?>
            <option value="<?= esc_attr($tecnico['ID']) ?>">
                <?= esc_html($tecnico['display_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Asignar Terminales</button>
</form>
