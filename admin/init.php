<?php
// ==============================================
// INIT.PHP - Carga centralizada para el admin
// ==============================================

// Seguridad: impedir acceso directo fuera del admin
if (!defined('IN_OMNIPOS_ADMIN')) {
    define('IN_OMNIPOS_ADMIN', true);
}

// Rutas absolutas
define('BASE_PATH', dirname(__DIR__));
define('ADMIN_PATH', __DIR__);

// Archivos centrales del sistema
require_once BASE_PATH . '/config.php';      // Configuración global y conexión a BD
require_once BASE_PATH . '/auth.php';        // Verificación de sesión y seguridad
require_once BASE_PATH . '/permisos.php';    // Permisos por rol

// (Opcional) Archivos extra
// require_once BASE_PATH . '/helpers.php';
// require_once BASE_PATH . '/constantes.php';
// require_once BASE_PATH . '/logger.php';
