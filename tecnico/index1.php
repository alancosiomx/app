<?php
// app/tecnico/index.php
session_start();
$contenido = __DIR__ . '/bloques/inicio_dashboard.php';
include __DIR__ . '/layout_tecnico.php';
