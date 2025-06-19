<?php
/*
Plugin Name: CBI Inventario POS
Description: Plugin para controlar terminales punto de venta.
Version: 1.0
Author: Tu Nombre
*/

// Verificar si el archivo db.php existe y puede incluirse correctamente
if (file_exists(__DIR__ . '/db.php')) {
    include_once __DIR__ . '/db.php';
} else {
    // Mostrar mensaje de error en el panel de administración si no se encuentra db.php
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>Error: El archivo db.php no se encontró. Asegúrate de que db.php esté en la raíz del plugin.</p></div>';
    });
    // Detener la ejecución del plugin si falta el archivo db.php
    return;
}

// Función para cargar la pestaña solicitada
function cargarTab($tab) {
    // Validar la pestaña solicitada para evitar inclusiones no deseadas
    $tab = preg_replace('/[^a-z_]/', '', $tab); // Sanitizar el nombre de la pestaña
    $file = __DIR__ . "/tabs/$tab.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<p>Tab no encontrada.</p>";
    }
}

// Función para renderizar el panel completo del plugin
function renderizar_inventario_panel() {
    // Detectar la pestaña activa desde el parámetro GET, con un valor predeterminado de 'ingreso'
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'ingreso';

    // Construcción de la interfaz del panel
    ob_start(); // Iniciar captura de salida para devolver el contenido del panel
    ?>
    <div class="cbi-inventario-panel">
        <h1>Control de Terminales POS</h1>
        <nav>
            <ul class="nav-buttons">
                <li><a href="?tab=ingreso" class="btn <?php echo $tab === 'ingreso' ? 'active' : ''; ?>">Ingreso</a></li>
                <li><a href="?tab=asignar" class="btn <?php echo $tab === 'asignar' ? 'active' : ''; ?>">Asignar</a></li>
                <li><a href="?tab=instalado" class="btn <?php echo $tab === 'instalado' ? 'active' : ''; ?>">Instalado</a></li>
                <li><a href="?tab=retirada" class="btn <?php echo $tab === 'retirada' ? 'active' : ''; ?>">Retirada</a></li>
                <li><a href="?tab=devuelta" class="btn <?php echo $tab === 'devuelta' ? 'active' : ''; ?>">Devuelta</a></li>
                <li><a href="?tab=inventario" class="btn <?php echo $tab === 'inventario' ? 'active' : ''; ?>">Inventario</a></li>
                <li><a href="?tab=almacen" class="btn <?php echo $tab === 'almacen' ? 'active' : ''; ?>">Almacén</a></li>
                <li><a href="?tab=inventario_por_tecnico" class="btn <?php echo $tab === 'inventario_por_tecnico' ? 'active' : ''; ?>">Inventario Por Técnico</a></li>
            </ul>
        </nav>
        
        <section>
            <?php cargarTab($tab); ?>
        </section>
    </div>

    <style>
        .nav-buttons {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 10px; /* Espacio entre botones */
            flex-wrap: wrap; /* Para que los botones se ajusten si hay muchos */
        }
        .nav-buttons .btn {
            padding: 10px 15px;
            text-decoration: none;
            background-color: #0073aa;
            color: #ffffff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .nav-buttons .btn:hover {
            background-color: #005885;
        }
        .nav-buttons .btn.active {
            background-color: #00a0d2;
            color: #ffffff;
        }
    </style>
    <?php
    return ob_get_clean(); // Devolver el contenido capturado
}

// Registrar el shortcode [cbi_inventario_panel] que renderiza el panel
function registrar_shortcodes() {
    add_shortcode('cbi_inventario_panel', 'renderizar_inventario_panel');
}
add_action('init', 'registrar_shortcodes');

?>
