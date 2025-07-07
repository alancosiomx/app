<?php
require_once '../layout.php';
require_once 'constants.php';

$tab = $_GET['tab'] ?? 'inventario';

switch ($tab) {
    case 'logs':
        include 'contenido_logs.php';
        break;
    case 'inventario':
    default:
        include 'contenido_inventario.php';
        break;
}
