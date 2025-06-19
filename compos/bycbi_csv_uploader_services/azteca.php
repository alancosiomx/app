function procesar_carga_servicios() {
    global $wpdb;

    if (!isset($_FILES['archivo']) || empty($_FILES['archivo']['tmp_name'])) {
        echo '<div class="notice notice-error">Error: No se ha subido ningún archivo. Por favor selecciona un archivo Excel.</div>';
        return;
    }

    $banco_seleccionado = $_POST['banco'];
    $archivo = $_FILES['archivo']['tmp_name'];

    error_log("Banco seleccionado: " . $banco_seleccionado); // 👀 Registro de depuración

    require_once(__DIR__ . '/vendor/autoload.php');

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $data = $hoja->toArray();

        if ($banco_seleccionado === 'bbva') {
            error_log("Procesando BBVA");
            require_once('bbva.php');
            procesar_datos_bbva($data);
        } elseif ($banco_seleccionado === 'banregio') {
            error_log("Procesando Banregio");
            require_once('banregio.php');
            procesar_datos_banregio($data);
        } elseif ($banco_seleccionado === 'azteca') {
            error_log("Procesando Banco Azteca"); // 👀 Verificar si se ejecuta
            require_once('azteca.php');
            procesar_datos_azteca($data);
        } else {
            echo '<div class="notice notice-error">Error: Banco no válido seleccionado.</div>';
        }

    } catch (Exception $e) {
        echo '<div class="notice notice-error">Error al procesar el archivo: ' . $e->getMessage() . '</div>';
    }
}
