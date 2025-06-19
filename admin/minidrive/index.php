<?php
require_once dirname(__DIR__) . '/init.php';

$dir = __DIR__;
$archivos = array_diff(scandir($dir), ['.', '..', 'index.php']);

$contenido = __DIR__ . '/contenido_minidrive.php';
require_once dirname(__DIR__) . '/layout.php';
