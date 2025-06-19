<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Función para buscar una serie en la base de datos
function buscarSerie($serie) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Asegúrate de que esta tabla existe en la base de datos

    // Buscar la serie completa o la serie corta en la base de datos
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE serial_number = %s OR short_serial = %s",
            $serie, $serie
        ),
        ARRAY_A
    );

    return $result;
}

// Manejar el envío del formulario de búsqueda
$busqueda_resultado = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['serie_busqueda'])) {
    $busqueda_resultado = buscarSerie($_POST['serie_busqueda']);
}

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

// Función para determinar el último movimiento y su fecha
function obtenerUltimoMovimiento($resultado) {
    $fechas = [
        'Ingreso' => $resultado['entry_date'] ?? null,
        'Asignación' => $resultado['assignment_date'] ?? null,
        'Devolución' => $resultado['return_date'] ?? null
    ];

    // Filtrar fechas no nulas y ordenarlas
    $fechas_validas = array_filter($fechas);
    arsort($fechas_validas); // Ordenar fechas de más reciente a más antigua

    // Obtener el último movimiento
    $ultimo_movimiento = key($fechas_validas);
    $fecha_ultimo_movimiento = current($fechas_validas);

    return ['movimiento' => $ultimo_movimiento, 'fecha' => $fecha_ultimo_movimiento];
}
?>

<h2>Inventario de Terminales</h2>

<!-- Formulario de Búsqueda -->
<form method="POST">
    <label for="serie_busqueda">Buscar por Serie Completa o Serie Corta:</label>
    <input type="text" name="serie_busqueda" id="serie_busqueda" placeholder="Ingrese la serie" required />
    <button type="submit">Buscar</button>
</form>

<!-- Mostrar resultados de la búsqueda -->
<?php if ($busqueda_resultado): ?>
    <h3>Resultado de la Búsqueda</h3>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 8px; background-color: #f2f2f2;">Campo</th>
                <th style="text-align: left; padding: 8px; background-color: #f2f2f2;">Información</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($titulos as $titulo => $campo): ?>
                <tr>
                    <td style="padding: 8px; font-weight: bold;"><?= $titulo ?></td>
                    <td style="padding: 8px;"><?= !empty($busqueda_resultado[$campo]) ? esc_html($busqueda_resultado[$campo]) : 'N/A' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Mini tabla para mostrar el último movimiento y su fecha -->
    <?php
    $ultimo = obtenerUltimoMovimiento($busqueda_resultado);
    ?>
    <h4>Último Movimiento</h4>
    <table border="1" style="width: 50%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 8px; background-color: #f2f2f2;">Movimiento</th>
                <th style="text-align: left; padding: 8px; background-color: #f2f2f2;">Fecha</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px;"><?= esc_html($ultimo['movimiento']) ?></td>
                <td style="padding: 8px;"><?= esc_html($ultimo['fecha']) ?></td>
            </tr>
        </tbody>
    </table>

<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p>No se encontró ninguna terminal con la serie ingresada.</p>
<?php endif; ?>
