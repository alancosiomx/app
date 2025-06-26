<?php
// ==============================================
// INIT.PHP - Carga centralizada para el admin
// ==============================================

// Seguridad: impedir acceso directo fuera del admin
if (!defined('IN_OMNIPOS_ADMIN')) {
    define('IN_OMNIPOS_ADMIN', true);
}

// Configuración de seguridad para sesiones (ANTES de session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'gc_maxlifetime'  => 1800,
        'read_and_close'  => false
    ]);
}

// Rutas absolutas
define('BASE_PATH', dirname(__DIR__));
define('ADMIN_PATH', __DIR__);

// Archivos centrales del sistema
require_once BASE_PATH . '/config.php';       // Configuración global y conexión a BD
require_once BASE_PATH . '/auth.php';         // Verificación de sesión y seguridad
require_once BASE_PATH . '/permisos.php';     // Permisos por rol

// Archivos adicionales si los necesitas
// require_once BASE_PATH . '/helpers.php';
// require_once BASE_PATH . '/constantes.php';
// require_once BASE_PATH . '/logger.php';

// Constantes del módulo de Finanzas (si existen)
$finanzas_constants = __DIR__ . '/finanzas/constants.php';
if (file_exists($finanzas_constants)) {
    require_once $finanzas_constants;
}
