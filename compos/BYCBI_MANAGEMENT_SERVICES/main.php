<?php
/*
Plugin Name: CBI Servicios Panel
Description: Plugin para gestionar servicios con tabs.
Version: 1.0
Author: Tu Nombre
*/

// Registrar el shortcode para cargar el panel
add_shortcode('cbi_services_panel', 'cbi_services_panel');

// Cargar estilos y scripts del plugin
function cbi_enqueue_assets() {
    // Estilos de DataTables
    wp_enqueue_style('cbi-datatables-css', 'https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css');
    // Estilos personalizados del plugin
    wp_enqueue_style('cbi-plugin-css', plugin_dir_url(__FILE__) . 'css/styles.css');

    // Scripts de jQuery y DataTables
    wp_enqueue_script('cbi-jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), null, true);
    wp_enqueue_script('cbi-datatables-js', 'https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', array('cbi-jquery'), null, true);

    // Scripts personalizados del plugin
    wp_enqueue_script('cbi-plugin-js', plugin_dir_url(__FILE__) . 'js/scripts.js', array('cbi-datatables-js'), null, true);

    // Localizar ajaxurl si es necesario
    wp_localize_script('cbi-plugin-js', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'cbi_enqueue_assets');


// Función para mostrar el panel de servicios con tabs
function cbi_services_panel() {
    ob_start(); // Iniciar el buffer de salida
    ?>
    <div id="cbi-tabs">
    <ul class="tab-buttons">
        <li><a href="#" class="tab-link active" data-tab="por_asignar">Por Asignar</a></li>
        <li><a href="#" class="tab-link" data-tab="en_ruta">En Ruta</a></li>
        <li><a href="#" class="tab-link" data-tab="concluido">Concluido</a></li>
        <li><a href="#" class="tab-link" data-tab="programar_cita">Programar Cita</a></li>
        <li><a href="#" class="tab-link" data-tab="test">Test</a></li>
    </ul>

    <div id="por_asignar" class="tab-content active">
        <?php cbi_tab_por_asignar(); ?>
    </div>
    <div id="en_ruta" class="tab-content">
        <?php cbi_tab_en_ruta(); ?>
    </div>
    <div id="concluido" class="tab-content">
        <?php cbi_tab_concluido(); ?>
    </div>
    <div id="programar_cita" class="tab-content">
        <?php cbi_tab_programar_cita(); ?>
    </div>
    <div id="test" class="tab-content">
        <?php cbi_tab_test(); ?>
    </div>
</div>


    <style>
        /* Estilos básicos para los tabs */
        .tab-buttons {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        .tab-buttons li {
            margin-right: 10px;
        }
        .tab-buttons a {
            padding: 10px 20px;
            background-color: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .tab-buttons a.active {
            background-color: #005885;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
            margin-top: 20px;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.tab-link');
        const contents = document.querySelectorAll('.tab-content');

        // Leer la URL actual para activar la pestaña correcta al cargar la página
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'por_asignar'; // Cambiar valor predeterminado a la pestaña por defecto
        const activeTabElement = document.querySelector(`[data-tab="${activeTab}"]`);

        if (activeTabElement) {
            // Activar pestaña desde la URL
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            activeTabElement.classList.add('active');
            document.getElementById(activeTab).classList.add('active');
        }

        // Cambiar pestaña y actualizar la URL al hacer clic
        tabs.forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // Cambiar pestañas activas
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.getAttribute('data-tab');
                document.getElementById(target).classList.add('active');

                // Actualizar la URL sin recargar la página
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', target);
                window.history.pushState({}, '', newUrl);
            });
        });
    });
</script>


    <?php
    return ob_get_clean(); // Devolver el contenido del buffer de salida
}

// Incluir los archivos de tabs y constantes
require_once(plugin_dir_path(__FILE__) . 'tabs/por_asignar.php');
require_once(plugin_dir_path(__FILE__) . 'tabs/en_ruta.php');
require_once(plugin_dir_path(__FILE__) . 'tabs/concluido.php');
require_once(plugin_dir_path(__FILE__) . 'tabs/programar_cita.php');
require_once(plugin_dir_path(__FILE__) . 'constants/table.php');
require_once(plugin_dir_path(__FILE__) . 'tabs/test.php');

