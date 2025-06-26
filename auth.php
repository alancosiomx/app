<?php
// =============================================
// auth.php - Verificación segura de sesión
// =============================================

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'gc_maxlifetime'  => 1800,
        'read_and_close'  => false
    ]);

    // Regenerar ID para prevenir fixation
    if (empty($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }

    // Guardar IP y User-Agent para validar integridad
    $_SESSION['ip'] = $_SESSION['ip'] ?? $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SESSION['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'];
}

// Verificación de autenticación e integridad
if (
    empty($_SESSION['usuario_id']) ||
    $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
) {
    error_log("Intento no autorizado desde IP: " . $_SERVER['REMOTE_ADDR']);
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: /admin/servicios/login.php?error=session_expired");
    exit;
}

// Protección CSRF para POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        error_log("⚠️ Posible ataque CSRF - Usuario ID: " . ($_SESSION['usuario_id'] ?? 'desconocido'));
        die("Error de seguridad. Por favor recarga la página.");
    }
}
