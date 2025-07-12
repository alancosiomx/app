require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/service_functions.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

$tabs = [
    'por_asignar' => 'Por Asignar',
    'en_ruta'     => 'En Ruta',
    'concluido'   => 'Concluido',
    'citas'       => 'Citas',
];

$tab = $_GET['tab'] ?? 'por_asignar';

// Mostrar el panel de alertas en todo el módulo de servicios
ob_start(); // Capturamos la salida HTML
mostrar_panel_alertas($pdo);
$panel_alertas_html = ob_get_clean();

$contenido_tab = match($tab) {
    'en_ruta'   => __DIR__ . '/contenido_en_ruta.php',
    'concluido' => __DIR__ . '/contenido_concluido.php',
    'citas'     => __DIR__ . '/contenido_citas.php',
    default     => __DIR__ . '/contenido_por_asignar.php',
};

$contenido = $contenido_tab;
require_once __DIR__ . '/../layout.php';
