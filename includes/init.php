<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/menu.php';

function include_footer_once(): void {
    if (!defined('FOOTER_INCLUDED')) {
        require_once __DIR__ . '/footer.php';
        if (!defined('FOOTER_INCLUDED')) {
            define('FOOTER_INCLUDED', true);
        }
    }
}

include_footer_once();
?>
