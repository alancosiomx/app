<?php
// =============================================
// CONFIGURACI0ˆ7N DE LA BASE DE DATOS
// =============================================

// Credenciales de la base de datos
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'enodhcdl_omndb');
define('DB_USER', 'enodhcdl_omn');
define('DB_PASS', 'Z00Q_8F^r0');
define('DB_CHARSET', 'utf8mb4');

// Template paths
define('HEAD', __DIR__ . '/includes/header.php');
define('MENU', __DIR__ . '/includes/menu.php');
define('FOOT', __DIR__ . '/includes/footer.php');

// =============================================
// CONEXI0ˆ7N SEGURA CON PDO
// =============================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_spanish_ci"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $pdo->exec("SET time_zone = '-06:00';");
    
} catch (PDOException $e) {

    error_log("Error de conexi¨®n DB [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage());
    die("<h2>Error en el sistema. Por favor intente m¨¢s tarde.</h2>");
}

// =============================================
// CONFIGURACIONES GLOBALES
// =============================================
define('APP_NAME', 'OMNIPOS');
define('BASE_URL', 'https://app.omniposmx.com');
define('ENVIRONMENT', 'development');

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
