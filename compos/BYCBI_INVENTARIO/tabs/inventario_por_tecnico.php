<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Declarar $wpdb globalmente para evitar errores de variable no definida
global $wpdb;

// Función para obtener la lista de técnicos desde la tabla wp_bm_user_index
function obtenerTecnicos() {
    global $wpdb;
    // Obtener la lista de técnicos
    $tecnicos = $wpdb->get_results("SELECT ID, display_name FROM wp_bm_user_index", ARRAY_A);
    return $tecnicos;
}

// Función para obtener el inventario asignado a un técnico por nombre, excluyendo terminales con custodia COMERCIO o status INSTALADO
function obtenerInventarioPorTecnico($tecnico_nombre) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Asegúrate de que esta tabla existe en la base de datos

    // Ajustar la consulta para excluir terminales con custodia COMERCIO o status INSTALADO
    $inventario = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE technician = %s AND custodia != 'COMERCIO' AND status != 'INSTALADO'", 
            $tecnico_nombre
        ), 
        ARRAY_A
    );

    return $inventario;
}

// Obtener la lista de técnicos para el select
$tecnicos = obtenerTecnicos();

// Manejar el envío del formulario de selección de técnico
$inventario_resultado = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tecnico_id'])) {
    $tecnico_id = $_POST['tecnico_id'];
    $tecnico_nombre = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM wp_bm_user_index WHERE ID = %d", $tecnico_id));
    $inventario_resultado = obtenerInventarioPorTecnico($tecnico_nombre);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario por Técnico</title>
    <!-- Incluir CSS y JS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>

<h2>Inventario por Técnico</h2>

<!-- Formulario de Selección de Técnico -->
<form method="POST">
    <label for="tecnico_id">Seleccione un Técnico:</label>
    <select name="tecnico_id" id="tecnico_id" required>
        <option value="">Seleccione un Técnico</option>
        <?php foreach ($tecnicos as $tecnico): ?>
            <option value="<?= esc_attr($tecnico['ID']) ?>" <?= isset($tecnico_id) && $tecnico_id == $tecnico['ID'] ? 'selected' : '' ?>>
                <?= esc_html($tecnico['display_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Ver Inventario</button>
</form>

<!-- Mostrar el inventario en una tabla si se seleccionó un técnico -->
<?php if (!empty($inventario_resultado)): ?>
    <h3>Inventario de <?= esc_html($tecnico_nombre) ?></h3>
    <table id="inventarioTabla" class="display" style="width: 100%;">
        <thead>
            <tr>
                <th>Banco</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie Completa</th>
                <th>Serie Corta</th>
                <th>Fecha de Ingreso</th>
                <th>Estatus</th>
                <th>Custodia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventario_resultado as $item): ?>
                <tr>
                    <td><?= esc_html($item['bank']) ?></td>
                    <td><?= esc_html($item['brand']) ?></td>
                    <td><?= esc_html($item['model']) ?></td>
                    <td><?= esc_html($item['serial_number']) ?></td>
                    <td><?= esc_html($item['short_serial']) ?></td>
                    <td><?= esc_html($item['entry_date']) ?></td>
                    <td><?= esc_html($item['status']) ?></td>
                    <td><?= esc_html($item['custodia']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Banco</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie Completa</th>
                <th>Serie Corta</th>
                <th>Fecha de Ingreso</th>
                <th>Estatus</th>
                <th>Custodia</th>
            </tr>
        </tfoot>
    </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p>No se encontraron terminales asignadas al técnico seleccionado.</p>
<?php endif; ?>

<!-- Script para inicializar DataTables -->
<script>
    $(document).ready(function() {
        $('#inventarioTabla').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json' // Para traducir DataTables a español
            }
        });
    });
</script>

</body>
</html>