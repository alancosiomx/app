<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/menu.php';

if (!function_exists('include_footer_once')) {
    function include_footer_once() {
        if (!defined('FOOTER_INCLUDED')) {
            require_once __DIR__ . '/footer.php';
            define('FOOTER_INCLUDED', true);
        }
    }
    register_shutdown_function('include_footer_once');
}
?>
