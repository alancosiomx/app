<?php
require_once __DIR__ . '/whatsapp_functions.php';

$telefono = '+5219982460606'; // Tu número verificado en Sandbox
$mensaje  = "🚀 Hola Alan, mensaje desde front_whatsapp PHP listo";

echo enviarWhatsapp($mensaje, $telefono);
