<?php
require_once __DIR__ . '/../init.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$contenido = __DIR__ . '/contenido_nuevo.php';
require_once __DIR__ . '/../layout.php';
