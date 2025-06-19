<?php
/*
Plugin Name: Carga de Servicios Bancarios
Description: Plugin para cargar archivos Excel de BBVA, Banregio y Banco Azteca y procesar sus servicios.
Version: 1.1
Author: Tu Nombre
*/

// Registrar el shortcode para mostrar el formulario de carga
add_shortcode('cargar_servicios', 'mostrar_formulario_carga');

function mostrar_formulario_carga() {
    ob_start();
    ?>

    <h2>Carga de Servicios Bancarios</h2>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('cargar_servicio_nonce', 'cargar_servicio_nonce'); ?>
        <label for="banco">Selecciona el banco:</label>
        <select name="banco" id="banco" required>
            <option value="bbva">BBVA</option>
            <option value="banregio">Banregio</option>
            <option value="azteca">Banco Azteca</option>
        </select>

        <label for="archivo">Selecciona el archivo Excel:</label>
        <input type="file" name="archivo" id="archivo" accept=".xlsx, .xls" required>

        <input type="submit" name="cargar" value="Cargar">
    </form>

    <?php
    // Procesar la carga del archivo si se ha enviado el formulario
    if (isset($_POST['cargar'])) {
        procesar_carga_servicios();
    }

    return ob_get_clean();
}

// Función para procesar la carga del archivo
function procesar_carga_servicios() {
    global $wpdb;

    // Validación del archivo subido
    if (!isset($_FILES['archivo']) || empty($_FILES['archivo']['tmp_name'])) {
        echo '<div class="notice notice-error">Error: No se ha subido ningún archivo. Por favor selecciona un archivo Excel.</div>';
        return;
    }

    // Validación de seguridad con nonce para evitar ataques CSRF
    if (!isset($_POST['cargar_servicio_nonce']) || !wp_verify_nonce($_POST['cargar_servicio_nonce'], 'cargar_servicio_nonce')) {
        echo '<div class="notice notice-error">Error: Acción no válida.</div>';
        return;
    }

    $banco_seleccionado = $_POST['banco'];
    $archivo = $_FILES['archivo']['tmp_name'];

    // Verificar que el archivo es un Excel
    $file_extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
    if (!in_array($file_extension, ['xlsx', 'xls'])) {
        echo '<div class="notice notice-error">Error: El archivo subido no es un Excel válido.</div>';
        return;
    }

    // Requiere PHPSpreadsheet (asegúrate de tenerlo instalado)
    require_once(__DIR__ . '/vendor/autoload.php');

    try {
        // Cargar el archivo Excel utilizando PHPSpreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $data = $hoja->toArray();

        // Procesar según el banco seleccionado
        if ($banco_seleccionado === 'bbva') {
            require_once('bbva.php');
            procesar_datos_bbva($data);  // Llama a la función específica para BBVA
        } elseif ($banco_seleccionado === 'banregio') {
            require_once('banregio.php');
            procesar_datos_banregio($data);  // Llama a la función específica para Banregio
        } elseif ($banco_seleccionado === 'azteca') {
            require_once('azteca.php');
            procesar_datos_azteca($data);  // Llama a la función específica para Banco Azteca
        } else {
            echo '<div class="notice notice-error">Error: Banco no válido seleccionado.</div>';
        }

        echo '<div class="notice notice-success">Archivo procesado correctamente.</div>';

    } catch (Exception $e) {
        // Manejo de errores en el procesamiento del archivo
        echo '<div class="notice notice-error">Error al procesar el archivo: ' . $e->getMessage() . '</div>';
    }
}