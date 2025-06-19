<?php
// Función para generar una tabla dinámica para los servicios
function cbi_generar_tabla_servicios($servicios, $columns, $accion_callback = null, $column_widths = []) {
    ?>
    <div class="cbi-table-container">
        <!-- Input para búsqueda en tiempo real -->
        <div class="table-search">
            <label for="cbi-search-input">Buscar:</label>
            <input type="text" id="cbi-search-input" class="cbi-search-input" placeholder="Escribe para buscar...">
        </div>

        <!-- Tabla dinámica con DataTables -->
        <div class="table-wrapper">
            <table id="cbi-services-table" class="display cbi-services-table">
                <thead>
                    <tr>
                        <?php if ($accion_callback): ?>
                            <th>Acción</th>
                        <?php endif; ?>
                        <?php foreach ($columns as $column) : ?>
                            <th><?php echo esc_html($column); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicios as $servicio) : ?>
                        <tr>
                            <?php if ($accion_callback): ?>
                                <td><?php echo call_user_func($accion_callback, $servicio); ?></td>
                            <?php endif; ?>
                            <?php foreach ($columns as $key => $column) : ?>
                                <td 
                                    style="
                                        <?php 
                                            // Condición para resaltar si 'fecha_cita' tiene valor
                                            if (!empty($servicio['fecha_cita'])) { 
                                                echo 'color: #00B050;'; 
                                            } 
                                        ?>
                                    ">
                                    <?php echo esc_html($servicio[$key]); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estilos -->
    <style>
        .cbi-table-container {
            margin: 20px 0;
        }

        .table-search {
            margin-bottom: 15px;
        }

        .cbi-search-input {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .table-wrapper {
            overflow-x: auto; /* Habilitar desplazamiento horizontal */
        }

        .cbi-services-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cbi-services-table th,
        .cbi-services-table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .cbi-services-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .cbi-services-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .cbi-services-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <?php
}
?>
