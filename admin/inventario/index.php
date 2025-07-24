require_once __DIR__ . '/../init.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

$vista = $_GET['vista'] ?? 'index'; // valores: index, asignar, editar, etc.
$contenido = __DIR__ . "/contenido_{$vista}.php";

require_once __DIR__ . '/../layout.php';
