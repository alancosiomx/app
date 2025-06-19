<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ Cargar el SDK manualmente desde la ruta correcta
require_once __DIR__ . '/vendor/twilio-php-main/src/Twilio/autoload.php';

use Twilio\Rest\Client;

// ✅ Credenciales
$sid    = 'AC607efa330bae90ab6be43bc12d58622e';
$token  = 'b47e5e5365d06665301c0b7b222b81b2';
$twilio = new Client($sid, $token);

// ✅ WhatsApp
$from = 'whatsapp:+14155238886';
$to   = 'whatsapp:+5219982460606';

$mensaje = "📲 ¡Mensaje enviado con SDK manual en ruta vendor/twilio-php-main!";

try {
    $message = $twilio->messages->create($to, [
        "from" => $from,
        "body" => $mensaje
    ]);
    echo "✅ Mensaje enviado con SID: " . $message->sid;
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
