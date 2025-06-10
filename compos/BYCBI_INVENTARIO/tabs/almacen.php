<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Declarar $wpdb globalmente para evitar errores de variable no definida
global $wpdb;

// Establecer la zona horaria de Ciudad de México
date_default_timezone_set('America/Mexico_City');

// Función para obtener todas las terminales en custodia de "ALMACEN"
function obtenerTerminalesEnAlmacen($filtros = []) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Nombre de la tabla de terminales

    // Construir la consulta básica para obtener terminales con custodia ALMACEN
    $query = "SELECT * FROM $table_name WHERE custodia = 'ALMACEN'";

    // Filtros dinámicos
    if (!empty($filtros['busqueda'])) {
        $query .= $wpdb->prepare(" AND (serial_number LIKE %s OR short_serial LIKE %s)", '%' . $filtros['busqueda'] . '%', '%' . $filtros['busqueda'] . '%');
    }

    if (!empty($filtros['banco'])) {
        $query .= $wpdb->prepare(" AND bank = %s", $filtros['banco']);
    }

    if (!empty($filtros['modelo'])) {
        $query .= $wpdb->prepare(" AND model = %s", $filtros['modelo']);
    }

    if (!empty($filtros['estatus'])) {
        $query .= $wpdb->prepare(" AND status = %s", $filtros['estatus']);
    }

    if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
        $query .= $wpdb->prepare(" AND entry_date BETWEEN %s AND %s", $filtros['fecha_inicio'], $filtros['fecha_fin']);
    }

    // Ejecutar la consulta y obtener los resultados
    $resultados = $wpdb->get_results($query, ARRAY_A);

    // Verificar si hubo un error en la consulta
    if ($wpdb->last_error) {
        echo '<p>Error al obtener los terminales: ' . esc_html($wpdb->last_error) . '</p>';
        return [];
    }

    return $resultados;
}

// Manejar el envío del formulario de búsqueda y filtros
$filtros = [
    'busqueda' => sanitize_text_field($_POST['serie_busqueda'] ?? ''),
    'banco' => sanitize_text_field($_POST['banco'] ?? ''),
    'modelo' => sanitize_text_field($_POST['modelo'] ?? ''),
    'estatus' => sanitize_text_field($_POST['estatus'] ?? ''),
    'fecha_inicio' => sanitize_text_field($_POST['fecha_inicio'] ?? ''),
    'fecha_fin' => sanitize_text_field($_POST['fecha_fin'] ?? '')
];

// Obtener la lista de terminales en custodia de "ALMACEN"
$terminales = obtenerTerminalesEnAlmacen($filtros);

// Obtener opciones únicas de bancos, modelos y estatus para los filtros
$bancos = $wpdb->get_col("SELECT DISTINCT bank FROM pos_terminals WHERE custodia = 'ALMACEN'");
$modelos = $wpdb->get_col("SELECT DISTINCT model FROM pos_terminals WHERE custodia = 'ALMACEN'");
$estatuses = ['DISPONIBLE', 'DAÑADA', 'INSTALADA', 'DEVUELTA']; // Valores de estatus posibles

// Títulos para la tabla de visualización
$titulos = [
    'Banco' => 'bank',
    'Marca' => 'brand',
    'Modelo' => 'model',
    'Serie Completa' => 'serial_number',
    'Serie Corta' => 'short_serial',
    'Fecha de Ingreso' => 'entry_date',
    'Estatus' => 'status',
    'Custodia' => 'custodia',
    'Ticket Retirada' => 'ticket_retired',
    'Ticket Instalación' => 'ticket_installed',
    'Asignación' => 'assignment',
    'Técnico' => 'technician',
    'Fecha de Asignación' => 'assignment_date',
    'Fecha de Devolución' => 'return_date'
];
?>

<h2>Inventario de Terminales en Custodia de ALMACEN</h2>

<!-- Formulario de Filtros -->
<form method="POST">
    <label for="serie_busqueda">Buscar por Serie Completa o Serie Corta:</label>
    <input type="text" name="serie_busqueda" id="serie_busqueda" placeholder="Ingrese la serie" value="<?= esc_attr($filtros['busqueda']) ?>" />

    <label for="banco">Filtrar por Banco:</label>
    <select name="banco" id="banco">
        <option value="">Todos</option>
        <?php foreach ($bancos as $banco): ?>
            <option value="<?= esc_attr($banco) ?>" <?= selected($filtros['banco'], $banco) ?>><?= esc_html($banco) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="modelo">Filtrar por Modelo:</label>
    <select name="modelo" id="modelo">
        <option value="">Todos</option>
        <?php foreach ($modelos as $modelo): ?>
            <option value="<?= esc_attr($modelo) ?>" <?= selected($filtros['modelo'], $modelo) ?>><?= esc_html($modelo) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="estatus">Filtrar por Estatus:</label>
    <select name="estatus" id="estatus">
        <option value="">Todos</option>
        <?php foreach ($estatuses as $estatus): ?>
            <option value="<?= esc_attr($estatus) ?>" <?= selected($filtros['estatus'], $estatus) ?>><?= esc_html($estatus) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="fecha_inicio">Fecha de Ingreso Desde:</label>
    <input type="date" name="fecha_inicio" value="<?= esc_attr($filtros['fecha_inicio']) ?>" />

    <label for="fecha_fin">Fecha de Ingreso Hasta:</label>
    <input type="date" name="fecha_fin" value="<?= esc_attr($filtros['fecha_fin']) ?>" />

    <button type="submit">Filtrar</button>
</form>

<!-- Mostrar resultados de la búsqueda -->
<?php if (!empty($terminales)): ?>
    <h3>Total de terminales encontradas: <?= count($terminales) ?></h3>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <?php foreach ($titulos as $titulo => $campo): ?>
                    <th style="text-align: left; padding: 8px; background-color: #f2f2f2;"><?= $titulo ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($terminales as $terminal): ?>
                <tr>
                    <?php foreach ($titulos as $campo): ?>
                        <td style="padding: 8px;"><?= !empty($terminal[$campo]) ? esc_html($terminal[$campo]) : 'N/A' ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No se encontraron terminales en custodia de ALMACEN.</p>
<?php endif; ?>
